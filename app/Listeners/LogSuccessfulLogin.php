<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        ActivityLog::create([
            'actor_id'    => $event->user->id,
            'actor_name'  => $event->user->name,
            'actor_role'  => $event->user->role ?? 'viewer',
            'action'      => 'user.login',
            'target_type' => 'user',
            'target_id'   => $event->user->id,
            'target_name' => $event->user->name,
            'description' => "{$event->user->name} signed in",
            'ip_address'  => request()->ip(),
        ]);
    }
}
