<?php

namespace App\Logging;

use Monolog\Logger;

class ApplicationLogger
{
    /**
     * Create a custom Monolog instance.
     *
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $logger = new Logger("ApplicationLoggerHandler");
        return $logger->pushHandler(new ApplicationLoggerHandler);
    }
}
