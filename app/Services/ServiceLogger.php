<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceLog;

class ServiceLogger
{
    public static function log(
        Service $service,
        int $userId,
        string $event,
        string $status,
        ?string $note = null,
        ?array $changes = null
    ): ServiceLog {
        return ServiceLog::create([
            'service_id' => $service->id,
            'user_id' => $userId,
            'event' => $event,
            'status' => $status,
            'note' => $note,
            'changes' => $changes,
            'created_at' => now(),
        ]);
    }
}
