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
 * Notify request class
 *
 * @package GravityMedia\Ssdp\Request
 */
class NotifyRequest extends Request
{
    /**
     * The method
     */
    const METHOD = 'NOTIFY';

    /**
     * Create notify request object
     */
    public function __construct()
    {
        parent::__construct(sprintf('%s:%s', Client::MULTICAST_ADDRESS, Client::MULTICAST_PORT), self::METHOD);
    }
}
