<?php

namespace App\Errors\Services;

use App\Errors\Models\SystemLog;

class SystemLogService
{
    /**
     * Store a log record in the database.
     *
     * @param array $record
     */
    public function store(array $record): void
    {
        try {
            // Only log important levels
            if (!in_array($record['level_name'], ['ERROR','CRITICAL','ALERT','EMERGENCY'])) {
                return;
            }

            // Capture exception stack trace if present
            $stackTrace = null;
            if (!empty($record['context']['exception']) && $record['context']['exception'] instanceof \Throwable) {
                $stackTrace = (string) $record['context']['exception'];
            }

            SystemLog::create([
                'level'       => $record['level_name'] ?? 'UNKNOWN',
                'message'     => $record['message'] ?? 'No message',
                'context'     => $record['context'] ?? [],
                'stack_trace' => isset($record['context']['exception']) && $record['context']['exception'] instanceof \Throwable
                                    ? (string) $record['context']['exception']
                                    : null,
                'source'      => 'app',
                'logged_at'   => now(),
            ]);

        } catch (\Throwable $e) {
            // Never let logging fail crash the app
        }
    }

    /**
     * Get latest logs.
     */
    public function getRecentLogs(int $limit = 50)
    {
        return SystemLog::latest('logged_at')
            ->paginate($limit);
    }

    /**
     * Get most recurring errors.
     */
    public function getRecurringErrors(int $limit = 20)
    {
        return SystemLog::selectRaw('message, count(*) as total')
            ->whereIn('level', ['ERROR','CRITICAL','ALERT','EMERGENCY'])
            ->groupBy('message')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Clear all database logs.
     */
    public function clearDatabaseLogs(): void
    {
        SystemLog::truncate();
    }

    public function deleteError(int $id): bool
    {
        $log = SystemLog::find($id);

        if (!$log) {
            return false;
        }

        return $log->delete();
    }
}