<?php

namespace App\Errors\Controllers;

use App\Http\Controllers\Controller;
use App\Errors\Services\ErrorLogService;
use Illuminate\Http\Request;

class ErrorLogFileController extends Controller
{
    public function __construct(protected ErrorLogService $logService) {}

    /**
     * Show all log files.
     */
    public function index()
    {
        $files = $this->logService->getLogFiles();

        return view('dashboard.admin.errors.file_errors.index', compact('files'));
    }

    /**
     * Download a specific log file.
     */
    public function download(string $file)
    {
        return $this->logService->download($file);
    }

    /**
     * Delete a specific log file.
     */
    public function delete(string $file)
    {
        $this->logService->delete($file);

        return back()->with('success', "Log file '$file' deleted.");
    }

    /**
     * Delete all log files.
     */
    public function deleteAll()
    {
        $this->logService->deleteAll();

        return back()->with('success', 'All log files deleted.');
    }
}