<?php

namespace FelixLoggi;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

/**
 * Class Merchant
 *
 * @package  FelixBraspag\Marketplace
 */
class Logger
{
    private $logger;

    /**
     * Logger constructor.
     * @param string $channel
     */
    public function __construct($channel = 'main')
    {
        if(getenv('LOGGI_PHP_LOGFILE')) {
            $this->logger = new MonologLogger($channel);
            $this->pushHandler(new StreamHandler(getenv('LOGGI_PHP_LOGFILE'), MonologLogger::DEBUG));

            return $this->logger;
        }

        return null;
    }
}
