<?php

namespace App\Errors\Services;

use Illuminate\Support\Facades\File;

class ErrorLogService
{
    protected string $logPath;

    public function __construct()
    {
        $this->logPath = storage_path('logs');
    }

    /**
     * List all log files with size and last modified date.
     */
    public function getLogFiles(): array
    {
        return collect(File::files($this->logPath))->map(function ($file) {
            return [
                'name'     => $file->getFilename(),
                'size'     => round($file->getSize() / 1024, 2) . ' KB',
                'modified' => date('Y-m-d H:i', $file->getMTime()),
            ];
        })->toArray();
    }

    /**
     * Download a specific log file.
     */
    public function download(string $file)
    {
        $filePath = $this->logPath . '/' . $file;

        if (!File::exists($filePath)) {
            abort(404, "Log file not found.");
        }

        return response()->download($filePath);
    }

    /**
     * Delete a specific log file.
     */
    public function delete(string $file): bool
    {
        $filePath = $this->logPath . '/' . $file;

        if (File::exists($filePath)) {
            return File::delete($filePath);
        }

        return false;
    }

    /**
     * Delete all log files in the folder.
     */
    public function deleteAll(): void
    {
        foreach (File::files($this->logPath) as $file) {
            File::delete($file);
        }
    }
}