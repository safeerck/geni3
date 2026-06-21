<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogger
{
    public static function log(
        string $action,
        string $description,
        ?string $targetType = null,
        ?int $targetId = null,
        ?string $targetName = null,
        ?array $properties = null,
        ?Request $request = null
    ): ActivityLog {
        $request ??= request();
        $user = auth()->user();

        return ActivityLog::create([
            'actor_id'    => $user?->id,
            'actor_name'  => $user?->name ?? 'System',
            'actor_role'  => $user?->role ?? 'viewer',
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'target_name' => $targetName,
            'description' => $description,
            'properties'  => $properties,
            'ip_address'  => $request->ip(),
        ]);
    }
}
