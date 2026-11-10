<?php

namespace App\Models;

use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_no',
        'customer_id',
        'technician_id',
        'laptop',
        'complaint',
        'agreed_cost',
        'photo_path',
        'thumb_path',
        'photo_path_2',
        'thumb_path_2',
        'status',
        'received_by',
        'received_at',
        'finished_by',
        'finished_at',
        'handed_over_by',
        'handed_over_at',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => ServiceStatus::class,
            'agreed_cost' => 'integer',
            'received_at' => 'datetime',
            'finished_at' => 'datetime',
            'handed_over_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'tracking_no';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function finisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finished_by');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ServiceLog::class)->orderBy('created_at', 'asc');
    }

    public function scopeSearch(Builder $query, ?string $q): Builder
    {
        if (blank($q)) {
            return $query;
        }

        $term = trim($q);

        return $query->where(function (Builder $sub) use ($term) {
            $sub->where('tracking_no', 'like', "%{$term}%")
                ->orWhere('laptop', 'like', "%{$term}%")
                ->orWhereHas('customer', function (Builder $c) use ($term) {
                    $c->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                })
                ->orWhereHas('technician', function (Builder $t) use ($term) {
                    $t->where('name', 'like', "%{$term}%");
                });
        });
    }

    public function scopeStatusFilter(Builder $query, ?string $filter): Builder
    {
        if (blank($filter) || $filter === 'Aktif') {
            return $query->whereIn('status', [ServiceStatus::Diterima->value, ServiceStatus::Diagnosa->value, ServiceStatus::Dikerjakan->value, ServiceStatus::Selesai->value]);
        }

        if ($filter === 'Semua') {
            return $query;
        }

        $filterMap = [
            'Dikerjakan' => ServiceStatus::Dikerjakan->value,
            'Selesai' => ServiceStatus::Selesai->value,
            'Diserahkan' => ServiceStatus::Serahkan->value,
            'Dibatalkan' => ServiceStatus::Dibatalkan->value,
        ];

        $enumVal = $filterMap[$filter] ?? strtolower($filter);

        return $query->where('status', $enumVal);
    }
}
