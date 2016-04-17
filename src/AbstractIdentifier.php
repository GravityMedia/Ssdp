<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp;

use Ramsey\Uuid\UuidInterface;

/**
 * Abstract identifier class
 *
 * @package GravityMedia\Ssdp
 */
abstract class AbstractIdentifier
{
    /**
     * @var UuidInterface
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $rootDevice;

    /**
     * @var boolean
     */
    protected $device;

    /**
     * @var boolean;
     */
    protected $service;

    /**
     * @var string
     */
    protected $domainName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $version;

    /**
     * Get id
     *
     * @return null|UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param UuidInterface $id
     *
     * @return $this
     */
    public function setId(UuidInterface $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return whether this is a root device
     *
     * @return boolean
     */
    public function isRootDevice()
    {
        if (null === $this->rootDevice) {
            return false;
        }

        return $this->rootDevice;
    }

    /**
     * Set root device
     *
     * @param boolean $rootDevice
     *
     * @return $this
     */
    public function setRootDevice($rootDevice)
    {
        $this->rootDevice = $rootDevice;
        return $this;
    }

    /**
     * Return whether this is a device
     *
     * @return boolean
     */
    public function isDevice()
    {
        return $this->device;
    }

    /**
     * Set device
     *
     * @param boolean $device
     *
     * @return $this
     */
    public function setDevice($device)
    {
        $this->device = $device;
        return $this;
    }

    /**
     * Return whether this is a service
     *
     * @return boolean
     */
    public function isService()
    {
        return $this->service;
    }

    /**
     * Set service
     *
     * @param boolean $service
     *
     * @return $this
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Get domainName
     *
     * @return string
     */
    public function getDomainName()
    {
        if (null === $this->domainName) {
            return 'schemas-upnp-org';
        }

        return $this->domainName;
    }

    /**
     * Set domainName
     *
     * @param string $domainName
     *
     * @return $this
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        if (null === $this->type) {
            return 'Basic';
        }

        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get version
     *
     * @return int
     */
    public function getVersion()
    {
        if (null === $this->version) {
            return 1;
        }

        return $this->version;
    }

    /**
     * Set version
     *
     * @param int $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Return this object as a string when the object is used in any string context.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Return this sobject as a string.
     *
     * @return string
     */
    abstract public function toString();
}
