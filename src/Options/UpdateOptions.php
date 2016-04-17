<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Options;

use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Uri;

/**
 * Update request options class
 *
 * @package GravityMedia\Ssdp\Request\Options
 */
class UpdateOptions extends ByebyeOptions
{
    /**
     * Default description URL string (with UPnP description for root device)
     */
    const DEFAULT_DESCRIPTION_URL_STRING = 'http://127.0.0.1:80/description.xml';

    /**
     * @var UriInterface
     */
    protected $descriptionUrl;

    /**
     * Get description URL
     *
     * @return UriInterface
     */
    public function getDescriptionUrl()
    {
        if (null === $this->descriptionUrl) {
            return new Uri(self::DEFAULT_DESCRIPTION_URL_STRING);
        }

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
}
