<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Request\Factory;

use GravityMedia\Ssdp\Options\UpdateOptions;
use GravityMedia\Ssdp\Request\NotifyRequest;
use Psr\Http\Message\RequestInterface;

/**
 * Update request factory class
 *
 * @package GravityMedia\Ssdp\Request\Factory
 */
class UpdateFactory implements FactoryInterface
{
    /**
     * Create update request object
     *
     * @param UpdateOptions $options
     *
     * @throws \RuntimeException
     *
     * @return RequestInterface
     */
    public function createRequest($options)
    {
        if (!$options instanceof UpdateOptions) {
            throw new \RuntimeException('Options must be instance of ' . UpdateOptions::class);
        }

        if (null === $options->getUniqueServiceName()) {
            throw new \RuntimeException('Unique service name not specified.');
        }

        $request = new NotifyRequest();
        return $request
            ->withRequestTarget('*')
            ->withProtocolVersion('1.1')
            ->withHeader('HOST', (string)$request->getUri())
            ->withHeader('LOCATION', (string)$options->getDescriptionUrl())
            ->withHeader('NT', (string)$options->getNotificationType())
            ->withHeader('NTS', '"ssdp:update"')
            ->withHeader('USN', (string)$options->getUniqueServiceName());
    }
}
