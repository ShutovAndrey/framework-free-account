<?php

declare(strict_types=1);

namespace App\Factory;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Factory.
 */
class LoggerFactory
{
    private string $path;

    private string $name;

    private int $level;

    public function __construct(array $settings)
    {
        $this->path = (string) $settings['path'];
        $this->level = (int) $settings['level'];
        $this->name = (string) $settings['name'];
    }

    /**
     * Build the logger.
     *
     * @return LoggerInterface The logger
     */
    public function createInstance(): LoggerInterface
    {
        $logger = new Logger($this->name);

        $logger->pushHandler(new StreamHandler(
            $this->path,
            $this->level
        ));

        return $logger;
    }
}
