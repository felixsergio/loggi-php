<?php

namespace FelixLoggi;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

/**
 * Class Merchant
 *
 * @package  FelixBraspag\Marketplace
 */
class Logger extends MonologLogger
{
    private $logger;

    /**
     * Logger constructor.
     * @param string $channel
     */
    public function __construct($channel = 'main')
    {
        if(getenv('LOGGI_PHP_LOGFILE')) {
            $this->monolog = new MonologLogger($channel);
            $this->monolog->pushHandler(new StreamHandler(getenv('LOGGI_PHP_LOGFILE'), MonologLogger::DEBUG));

            return $this->monolog;
        }

        return null;
    }

    public function execute()
    {
        return $this->monolog;
    }
}
