<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Request\Factory;

use GravityMedia\Ssdp\Options\AliveOptions;
use GravityMedia\Ssdp\Request\NotifyRequest;
use Psr\Http\Message\RequestInterface;

/**
 * Alive request factory class
 *
 * @package GravityMedia\Ssdp\Request\Factory
 */
class AliveFactory implements FactoryInterface
{
    /**
     * Create alive request object
     *
     * @param AliveOptions $options
     *
     * @throws \RuntimeException
     *
     * @return RequestInterface
     */
    public function createRequest($options)
    {
        if (!$options instanceof AliveOptions) {
            throw new \RuntimeException('Options must be instance of ' . AliveOptions::class);
        }

        if (null === $options->getUniqueServiceName()) {
            throw new \RuntimeException('Unique service name not specified.');
        }

        $request = new NotifyRequest();
        return $request
            ->withRequestTarget('*')
            ->withProtocolVersion('1.1')
            ->withHeader('HOST', (string)$request->getUri())
            ->withHeader('CACHE-CONTROL', sprintf('max-age=%u', $options->getMessageLifetime()))
            ->withHeader('LOCATION', (string)$options->getDescriptionUrl())
            ->withHeader('NT', (string)$options->getNotificationType())
            ->withHeader('NTS', '"ssdp:alive"')
            ->withHeader('SERVER', $options->getServerString())
            ->withHeader('USN', (string)$options->getUniqueServiceName());
    }
}
