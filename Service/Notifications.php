<?php

namespace RMS\PushNotificationsBundle\Service;

use RMS\PushNotificationsBundle\Message\MessageInterface;
use RMS\PushNotificationsBundle\Service\OS\OSNotificationServiceInterface;

class Notifications
{
    /**
     * Array of handlers
     *
     * @var OSNotificationServiceInterface[]
     */
    protected $handlers = array();

    protected $logger;

    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Sends a message to a device, identified by
     * the OS and the supplied device token
     *
     * @param \RMS\PushNotificationsBundle\Message\MessageInterface $message
     * @throws \RuntimeException
     * @return bool
     */
    public function send(MessageInterface $message)
    {
        if (!isset($this->handlers[$message->getTargetOS()])) {
            throw new \RuntimeException("OS type {$message->getTargetOS()} not supported");
        }

        $handler = $this->handlers[$message->getTargetOS()];
        if ($this->logger && method_exists($handler, 'setLogger')) {
            $handler->setLogger($this->logger);
        }

        return $handler->send($message);
    }

    /**
     * Adds a handler
     *
     * @param $osType
     * @param $service
     */
    public function addHandler($osType, $service)
    {
        if (!isset($this->handlers[$osType])) {
            $this->handlers[$osType] = $service;
        }
    }
}
