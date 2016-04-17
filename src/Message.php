<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp;

use Psr\Http\Message\UriInterface;

/**
 * Message class
 *
 * @package GravityMedia\Ssdp
 */
class Message
{
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
}
