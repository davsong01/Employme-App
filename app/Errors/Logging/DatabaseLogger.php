<?php 


namespace App\Errors\Logging;

use App\Errors\Services\SystemLogService;
use Monolog\LogRecord;

class DatabaseLogger
{
    public function __invoke($logger)
    {
        try {
            $logger->pushProcessor(function (LogRecord $record) {
                try {
                    // Convert LogRecord object to array
                    $data = [
                        'level_name' => $record->level->getName(),
                        'message'    => $record->message,
                        'context'    => (array) $record->context,
                        'channel'    => $record->channel,
                        'datetime'   => $record->datetime,
                        'extra'      => (array) $record->extra,
                    ];

                    app(SystemLogService::class)->store($data);

                } catch (\Throwable $e) {
                    // fail silently
                }

                return $record; // always return record
            });
        } catch (\Throwable $e) {
            // fail silently if logger itself fails to add processor
        }
    }
}