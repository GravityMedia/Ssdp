<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Event;

use GravityMedia\Ssdp\Exception\DiscoverException;
use Psr\Http\Message\UriInterface;

use Symfony\Component\EventDispatcher\Event;

/**
 * Discover event class
 *
 * @package GravityMedia\Ssdp
 */
class DiscoverEvent extends Event
{
    const EVENT_DISCOVER = 'ssdp:discover';
    const EVENT_DISCOVER_ERROR = 'ssdp:discover:error';

    /**
     * @var DiscoverException
     */
    protected $exception;

    /**
     * @var int
     */
    protected $lifetime;

    /**
     * @var \DateTimeInterface
     */
    protected $date;

    /**
     * @var UriInterface
     */
    protected $descriptionUrl;

    /**
     * @var string
     */
    protected $serverString;

    /**
     * @var string
     */
    protected $searchTargetString;

    /**
     * @var string
     */
    protected $uniqueServiceNameString;

    /**
     * @var string
     */
    protected $remote;

    /**
     * Get exception
     *
     * @return DiscoverException
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Set exception
     *
     * @param DiscoverException $exception
     *
     * @return $this
     */
    public function setException(DiscoverException $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * Get lifetime
     *
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Set lifetime
     *
     * @param int $lifetime
     *
     * @return $this
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTimeInterface
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTimeInterface $date
     *
     * @return $this
     */
    public function setDate(\DateTimeInterface $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get description URL
     *
     * @return UriInterface
     */
    public function getDescriptionUrl()
    {
        return $this->descriptionUrl;
    }

    /**
     * Set description URL
     *
     * @param UriInterface $descriptionUrl
     *
     * @return $this
     */
    public function setDescriptionUrl(UriInterface $descriptionUrl)
    {
        $this->descriptionUrl = $descriptionUrl;
        return $this;
    }

    /**
     * Get server string
     *
     * @return string
     */
    public function getServerString()
    {
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

    /**
     * Get search target
     *
     * @return string
     */
    public function getSearchTargetString()
    {
        return $this->searchTargetString;
    }

    /**
     * Set search target
     *
     * @param string $searchTargetString
     *
     * @return $this
     */
    public function setSearchTargetString($searchTargetString)
    {
        $this->searchTargetString = $searchTargetString;
        return $this;
    }

    /**
     * Get unique service name
     *
     * @return string
     */
    public function getUniqueServiceNameString()
    {
        return $this->uniqueServiceNameString;
    }

    /**
     * Set unique service name
     *
     * @param string $uniqueServiceNameString
     *
     * @return $this
     */
    public function setUniqueServiceNameString($uniqueServiceNameString)
    {
        $this->uniqueServiceNameString = $uniqueServiceNameString;
        return $this;
    }

    /**
     * Get remote
     *
     * @return string
     */
    public function getRemote()
    {
        return $this->remote;
    }

    /**
     * Set remote
     *
     * @param string $remote
     *
     * @return $this
     */
    public function setRemote($remote)
    {
        $this->remote = $remote;
        return $this;
    }
}
