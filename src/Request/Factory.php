<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Request;

use GravityMedia\Ssdp\Options\AliveOptions;
use GravityMedia\Ssdp\Options\ByebyeOptions;
use GravityMedia\Ssdp\Options\DiscoverOptions;
use GravityMedia\Ssdp\Options\UpdateOptions;
use Psr\Http\Message\RequestInterface;

/**
 * Request factory class
 *
 * @package GravityMedia\Ssdp\Request\Factory
 */
class Factory
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
    public function createAliveRequest(AliveOptions $options)
    {
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

    /**
     * Create byebye request object
     *
     * @param ByebyeOptions $options
     *
     * @throws \RuntimeException
     *
     * @return RequestInterface
     */
    public function createByebyeRequest(ByebyeOptions $options)
    {
        if (null === $options->getUniqueServiceName()) {
            throw new \RuntimeException('Unique service name not specified.');
        }

        $request = new NotifyRequest();
        return $request
            ->withRequestTarget('*')
            ->withProtocolVersion('1.1')
            ->withHeader('HOST', (string)$request->getUri())
            ->withHeader('NT', (string)$options->getNotificationType())
            ->withHeader('NTS', '"ssdp:byebye"')
            ->withHeader('USN', (string)$options->getUniqueServiceName());
    }


    /**
     * Create discover request object
     *
     * @param DiscoverOptions $options
     *
     * @throws \RuntimeException
     *
     * @return RequestInterface
     */
    public function createDiscoverRequest(DiscoverOptions $options)
    {
        $request = new SearchRequest();
        return $request
            ->withRequestTarget('*')
            ->withProtocolVersion('1.1')
            ->withHeader('HOST', (string)$request->getUri())
            ->withHeader('MAN', '"ssdp:discover"')
            ->withHeader('MX', (string)$options->getMaximumWaitTime())
            ->withHeader('ST', (string)$options->getSearchTarget());
    }

    /**
     * Create update request object
     *
     * @param UpdateOptions $options
     *
     * @throws \RuntimeException
     *
     * @return RequestInterface
     */
    public function createUpdateRequest(UpdateOptions $options)
    {
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
