<?php

namespace App\Errors\Controllers;

use App\Http\Controllers\Controller;
use App\Errors\Services\SystemLogService;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    protected SystemLogService $logService;

    public function __construct(SystemLogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Display recent logs and recurring errors.
     */
    public function index()
    {
        $logs = $this->logService->getRecentLogs(100); // recent 100 logs
        $recurring = $this->logService->getRecurringErrors(20); // top 20 recurring

        return view('dashboard.admin.errors.system_errors.index', compact('logs', 'recurring'));
    }

    /**
     * Get recent logs as JSON (optional API usage).
     */
    public function recentLogs(Request $request)
    {
        $limit = $request->input('limit', 100);
        $logs = $this->logService->getRecentLogs($limit);

        return response()->json($logs);
    }

    /**
     * Get recurring errors as JSON (optional API usage).
     */
    public function recurringErrors(Request $request)
    {
        $limit = $request->input('limit', 10);
        $errors = $this->logService->getRecurringErrors($limit);

        return response()->json($errors);
    }

    /**
     * Clear all database logs.
     */
    public function clearDatabaseLogs()
    {
        $this->logService->clearDatabaseLogs();

        return back()->with('message', 'All database logs cleared.');
    }

    public function delete($id)
    {
        $this->logService->deleteError($id);

        return back()->with('message', 'Delete Successful.');
    }
}