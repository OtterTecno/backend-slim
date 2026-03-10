<?php

namespace App\Config;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;

class AppLogger
{
    private static ?Logger $logger = null;

    public static function get(): Logger
    {
        if (self::$logger === null) {
            self::$logger = new Logger('app');

            // 1. Stream to stderr (standard for docker/cloud logs)
            self::$logger->pushHandler(new StreamHandler('php://stderr', Level::Debug));

            // 2. Rotating File Handler
            $logPath = __DIR__ . '/../../logs/app.log';
            $maxFiles = (int)($_ENV['LOGGER_ROTATIVO'] ?? 10);

            self::$logger->pushHandler(new RotatingFileHandler($logPath, $maxFiles, Level::Debug));
        }
        return self::$logger;
    }


    public static function info(string $message, array $context = []): void
    {
        self::get()->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::get()->error($message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::get()->debug($message, $context);
    }
}
