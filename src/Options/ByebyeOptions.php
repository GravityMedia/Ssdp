<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Options;

use GravityMedia\Ssdp\NotificationType;
use GravityMedia\Ssdp\UniqueServiceName;

/**
 * Byebye request options class
 *
 * @package GravityMedia\Ssdp\Request\Options
 */
class ByebyeOptions
{
    /**
     * Default notification type
     */
    const DEFAULT_NOTIFICATION_TYPE = 'upnp:rootdevice';

    /**
     * @var NotificationType
     */
    protected $notificationType;

    /**
     * @var UniqueServiceName
     */
    protected $uniqueServiceName;

    /**
     * Get notification type
     *
     * @return NotificationType
     */
    public function getNotificationType()
    {
        if (null === $this->notificationType) {
            return NotificationType::fromString(self::DEFAULT_NOTIFICATION_TYPE);
        }

        return $this->notificationType;
    }

    /**
     * Set notification type
     *
     * @param NotificationType $notificationType
     *
     * @return $this
     */
    public function setNotificationType(NotificationType $notificationType)
    {
        $this->notificationType = $notificationType;
        return $this;
    }

    /**
     * Get unique service name
     *
     * @return UniqueServiceName
     */
    public function getUniqueServiceName()
    {
        if (null === $this->uniqueServiceName) {
            return new UniqueServiceName();
        }

        return $this->uniqueServiceName;
    }

    /**
     * Set unique service name
     *
     * @param UniqueServiceName $uniqueServiceName
     *
     * @return $this
     */
    public function setUniqueServiceName(UniqueServiceName $uniqueServiceName)
    {
        $this->uniqueServiceName = $uniqueServiceName;
        return $this;
    }
}
