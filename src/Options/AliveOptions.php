<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Options;

/**
 * Alive request options class
 *
 * @package GravityMedia\Ssdp\Request\Options
 */
class AliveOptions extends UpdateOptions
{
    /**
     * Default message lifetime (seconds until advertisement expires)
     */
    const DEFAULT_MESSAGE_LIFETIME = 1800;

    /**
     * Default server name
     */
    const DEFAULT_SERVER_NAME = 'GravityMedia-Ssdp';

    /**
     * Default server version
     */
    const DEFAULT_SERVER_VERSION = '1.0.x-dev';

    /**
     * @var string
     */
    private static $defaultServerString;

    /**
     * @var int
     */
    protected $messageLifetime;

    /**
     * @var string
     */
    protected $serverString;

    /**
     * Get default server string
     *
     * @return string
     */
    public function getDefaultServerString()
    {
        if (null === self::$defaultServerString) {
            self::$defaultServerString = sprintf(
                '%s/%s UPnP/1.1 %s/%s',
                PHP_OS,
                php_uname('r'),
                self::DEFAULT_SERVER_NAME,
                self::DEFAULT_SERVER_VERSION
            );
        }

        return self::$defaultServerString;
    }

    /**
     * Get message lifetime
     *
     * @return int
     */
    public function getMessageLifetime()
    {
        if (null === $this->messageLifetime) {
            return self::DEFAULT_MESSAGE_LIFETIME;
        }

        return $this->messageLifetime;
    }

    /**
     * Set message lifetime
     *
     * @param int $messageLifetime
     *
     * @return $this
     */
    public function setMessageLifetime($messageLifetime)
    {
        $this->messageLifetime = $messageLifetime;
        return $this;
    }

    /**
     * Get server string
     *
     * @return string
     */
    public function getServerString()
    {
        if (null === $this->serverString) {
            return $this->getDefaultServerString();
        }

        return $this->serverString;
    }

    /**
     * Set server string
     *
     * @param string $serverString
     *
     * @return $this
     */
    public function setServerString($serverString)
    {
        $this->serverString = $serverString;
        return $this;
    }
}
