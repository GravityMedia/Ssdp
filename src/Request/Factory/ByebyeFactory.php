<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Request\Factory;

use GravityMedia\Ssdp\Options\ByebyeOptions;
use GravityMedia\Ssdp\Request\NotifyRequest;
use Psr\Http\Message\RequestInterface;

/**
 * Byebye request factory class
 *
 * @package GravityMedia\Ssdp\Request\Factory
 */
class ByebyeFactory implements FactoryInterface
{
    /**
     * Create byebye request object
     *
     * @param ByebyeOptions $options
     *
     * @throws \RuntimeException
     *
     * @return RequestInterface
     */
    public function createRequest($options)
    {
        if (!$options instanceof ByebyeOptions) {
            throw new \RuntimeException('Options must be instance of ' . ByebyeOptions::class);
        }

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
}
