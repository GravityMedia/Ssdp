<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Request\Factory;

use GravityMedia\Ssdp\Options\DiscoverOptions;
use GravityMedia\Ssdp\Request\SearchRequest;
use Psr\Http\Message\RequestInterface;

/**
 * Discover request factory class
 *
 * @package GravityMedia\Ssdp\Request\Factory
 */
class DiscoverFactory implements FactoryInterface
{
    /**
     * Create discover request object
     *
     * @param DiscoverOptions $options
     *
     * @throws \RuntimeException
     *
     * @return RequestInterface
     */
    public function createRequest($options)
    {
        if (!$options instanceof DiscoverOptions) {
            throw new \RuntimeException('Options must be instance of ' . DiscoverOptions::class);
        }

        $request = new SearchRequest();
        return $request
            ->withRequestTarget('*')
            ->withProtocolVersion('1.1')
            ->withHeader('HOST', (string)$request->getUri())
            ->withHeader('MAN', '"ssdp:discover"')
            ->withHeader('MX', (string)$options->getMaximumWaitTime())
            ->withHeader('ST', (string)$options->getSearchTarget());
    }
}
