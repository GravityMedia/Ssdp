<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Request;

use GravityMedia\Ssdp\Client;
use Zend\Diactoros\Request;

/**
 * Search request class
 *
 * @package GravityMedia\Ssdp\Request
 */
class SearchRequest extends Request
{
    /**
     * The method
     */
    const METHOD = 'M-SEARCH';

    /**
     * Create search request object
     */
    public function __construct()
    {
        parent::__construct(sprintf('%s:%s', Client::MULTICAST_ADDRESS, Client::MULTICAST_PORT), self::METHOD);
    }
}
