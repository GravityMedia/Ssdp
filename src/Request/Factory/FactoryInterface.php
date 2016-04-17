<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Request\Factory;

use Psr\Http\Message\RequestInterface;

/**
 * Request factory interface
 *
 * @package GravityMedia\Ssdp\Request\Factory
 */
interface FactoryInterface
{
    /**
     * Create request object
     *
     * @param object $options
     *
     * @throws \RuntimeException
     *
     * @return RequestInterface
     */
    public function createRequest($options);
}
