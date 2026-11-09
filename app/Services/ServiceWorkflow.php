<?php

namespace App\Services;

use App\Enums\ServiceStatus;
use App\Helpers\CustomerKey;
use App\Helpers\TrackingNumber;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ServiceWorkflow
{
    public function __construct(
        protected PhotoStorage $photoStorage
    ) {}

    public function receive(
        User $user,
        string $customerName,
        string $customerPhone,
        string $laptop,
        string $complaint,
        int $agreedCost,
        int $technicianId,
        UploadedFile $photo,
        ?UploadedFile $photo2 = null
    ): Service {
        $photoPaths = $this->photoStorage->store($photo);
        $photoPaths2 = $photo2 ? $this->photoStorage->store($photo2) : null;

        try {
            return DB::transaction(function () use (
                $user,
                $customerName,
                $customerPhone,
                $laptop,
                $complaint,
                $agreedCost,
                $technicianId,
                $photoPaths,
                $photoPaths2
            ) {
                $cleanName = trim(preg_replace('/\s+/', ' ', $customerName));
                $nameKey = CustomerKey::normalizeName($customerName);
                $cleanPhone = CustomerKey::normalizePhone($customerPhone);

                // Pelanggan dikenali dari pasangan (name_key, phone)
                $customer = Customer::firstOrCreate(
                    [
                        'name_key' => $nameKey,
                        'phone' => $cleanPhone,
                    ],
                    [
                        'name' => $cleanName,
                    ]
                );

                $trackingNo = TrackingNumber::generate();
                $now = now();

                $service = Service::create([
                    'tracking_no' => $trackingNo,
                    'customer_id' => $customer->id,
                    'technician_id' => $technicianId,
                    'laptop' => $laptop,
                    'complaint' => $complaint,
                    'agreed_cost' => $agreedCost,
                    'photo_path' => $photoPaths['photo_path'],
                    'thumb_path' => $photoPaths['thumb_path'],
                    'photo_path_2' => $photoPaths2['photo_path'] ?? null,
                    'thumb_path_2' => $photoPaths2['thumb_path'] ?? null,
                    'status' => ServiceStatus::Dikerjakan,
                    'received_by' => $user->id,
                    'received_at' => $now,
                ]);

                ServiceLogger::log(
                    service: $service,
                    userId: $user->id,
                    event: 'Dikerjakan',
                    status: ServiceStatus::Dikerjakan->value,
                    note: 'Laptop diterima dan mulai dikerjakan'
                );

                return $service;
            });
        } catch (\Throwable $e) {
            $this->photoStorage->delete($photoPaths['photo_path'], $photoPaths['thumb_path']);
            if ($photoPaths2) {
                $this->photoStorage->delete($photoPaths2['photo_path'], $photoPaths2['thumb_path']);
            }
            throw $e;
        }
    }

    public function markFinished(Service $service, User $user): Service
    {
        return DB::transaction(function () use ($service, $user) {
            /** @var Service $locked */
            $locked = Service::where('id', $service->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== ServiceStatus::Dikerjakan) {
                throw new InvalidArgumentException('Perubahan status tidak diperbolehkan');
            }

            $locked->update([
                'status' => ServiceStatus::Selesai,
                'finished_by' => $user->id,
                'finished_at' => now(),
            ]);

            ServiceLogger::log(
                service: $locked,
                userId: $user->id,
                event: 'Selesai',
                status: ServiceStatus::Selesai->value,
                note: 'Laptop selesai dikerjakan'
            );

            return $locked;
        });
    }

    public function handOver(Service $service, User $user): Service
    {
        return DB::transaction(function () use ($service, $user) {
            /** @var Service $locked */
            $locked = Service::where('id', $service->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === ServiceStatus::Serahkan) {
                throw new InvalidArgumentException('Servis ini sudah diserahkan');
            }

            if ($locked->status !== ServiceStatus::Selesai) {
                throw new InvalidArgumentException('Perubahan status tidak diperbolehkan');
            }

            $locked->update([
                'status' => ServiceStatus::Serahkan,
                'handed_over_by' => $user->id,
                'handed_over_at' => now(),
            ]);

            ServiceLogger::log(
                service: $locked,
                userId: $user->id,
                event: 'Diserahkan',
                status: ServiceStatus::Serahkan->value,
                note: 'Laptop diserahkan ke pelanggan'
            );

            return $locked;
        });
    }

    public function cancel(Service $service, User $user, string $reason): Service
    {
        return DB::transaction(function () use ($service, $user, $reason) {
            /** @var Service $locked */
            $locked = Service::where('id', $service->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== ServiceStatus::Dikerjakan) {
                throw new InvalidArgumentException('Perubahan status tidak diperbolehkan');
            }

            $cleanReason = trim($reason);
            if (empty($cleanReason)) {
                throw new InvalidArgumentException('Alasan pembatalan wajib diisi');
            }

            $locked->update([
                'status' => ServiceStatus::Dibatalkan,
                'agreed_cost' => 0,
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
                'cancel_reason' => $cleanReason,
            ]);

            ServiceLogger::log(
                service: $locked,
                userId: $user->id,
                event: 'Dibatalkan',
                status: ServiceStatus::Dibatalkan->value,
                note: 'Alasan: '.$cleanReason
            );

            return $locked;
        });
    }

    public function updateDetails(
        Service $service,
        User $user,
        string $laptop,
        string $complaint,
        int $technicianId
    ): Service {
        return DB::transaction(function () use ($service, $user, $laptop, $complaint, $technicianId) {
            /** @var Service $locked */
            $locked = Service::where('id', $service->id)->lockForUpdate()->firstOrFail();

            if (! in_array($locked->status, [ServiceStatus::Dikerjakan, ServiceStatus::Selesai], true)) {
                throw new InvalidArgumentException('Perubahan status tidak diperbolehkan');
            }

            $changes = [];

            if ($locked->laptop !== $laptop) {
                $changes['laptop'] = ['old' => $locked->laptop, 'new' => $laptop];
                $locked->laptop = $laptop;
            }

            if ($locked->complaint !== $complaint) {
                $changes['keluhan'] = ['old' => $locked->complaint, 'new' => $complaint];
                $locked->complaint = $complaint;
            }

            if ($locked->technician_id !== $technicianId) {
                $oldTech = $locked->technician?->name;
                $locked->technician_id = $technicianId;
                $locked->load('technician');
                $newTech = $locked->technician?->name;
                $changes['teknisi'] = ['old' => $oldTech, 'new' => $newTech];
            }

            if (! empty($changes)) {
                $locked->save();

                $changedLabels = array_keys($changes);
                ServiceLogger::log(
                    service: $locked,
                    userId: $user->id,
                    event: 'Data diubah',
                    status: $locked->status->value,
                    note: 'Diubah: '.implode(', ', $changedLabels),
                    changes: $changes
                );
            }

            return $locked;
        });
    }
}
