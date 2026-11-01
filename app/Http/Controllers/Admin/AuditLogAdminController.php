<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogAdminController extends Controller
{
    /**
     * Tampilkan riwayat log audit aktivitas pengelola (Khusus Super Admin).
     */
    public function index(Request $request): View
    {
        $query = AdminAuditLog::with('user')->latest('created_at');

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        }

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $logs = $query->paginate(20)->withQueryString();
        $adminUsers = User::orderBy('name')->get();
        $distinctActions = AdminAuditLog::select('action')->distinct()->pluck('action')->filter()->values();

        return view('admin.audit_logs.index', [
            'logs' => $logs,
            'adminUsers' => $adminUsers,
            'distinctActions' => $distinctActions,
            'filters' => [
                'q' => $request->input('q'),
                'action' => $request->input('action'),
                'user_id' => $request->input('user_id'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ],
        ]);
    }
}
