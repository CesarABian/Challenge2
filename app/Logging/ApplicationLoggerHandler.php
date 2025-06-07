<?php

namespace App\Logging;

use Monolog\LogRecord;
use App\Models\ApplicationLog;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class ApplicationLoggerHandler extends AbstractProcessingHandler implements HandlerInterface
{
    public string $table = 'application_logs';

    protected function write(LogRecord $record): void
    {
        $data = [
            'message'       => $record['message'],
            'context'       => json_encode($record['context']),
            'level'         => $record['level'],
            'level_name'    => $record['level_name'],
            'channel'       => $record['channel'],
            'record_datetime' => $record['datetime']->format('Y-m-d H:i:s'),
            'extra'         => json_encode($record['extra']),
            'formatted'     => $record['formatted'],
            'remote_addr'   => '',
            'user_agent'    => '',
        ];
        ApplicationLog::create($data);
    }
}
