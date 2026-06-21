<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('actor')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('actor')) {
            $query->where(function ($q) use ($request) {
                $q->where('actor_name', 'like', '%' . $request->actor . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(25)->withQueryString();

        $stats = [
            'total'   => ActivityLog::count(),
            'today'   => ActivityLog::whereDate('created_at', today())->count(),
            'logins'  => ActivityLog::where('action', 'user.login')->whereDate('created_at', today())->count(),
            'changes' => ActivityLog::whereIn('action', ['user.created', 'user.updated', 'user.deleted', 'user.role_changed'])
                                    ->whereDate('created_at', today())->count(),
        ];

        $actionTypes = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.activity.index', compact('logs', 'stats', 'actionTypes'));
    }
}
