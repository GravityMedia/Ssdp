<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\SsdpTest;

use GravityMedia\Ssdp\Client;
use GravityMedia\Ssdp\Multicast\Factory as MulticastFactory;
use GravityMedia\Ssdp\Options\DiscoverOptions;
use GravityMedia\Ssdp\Request\Factory as RequestFactory;
use GravityMedia\Ssdp\Request\SearchRequest;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Socket\Raw\Socket;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Client test class
 *
 * @package GravityMedia\SsdpTest
 *
 * @covers GravityMedia\Ssdp\Client
 * @uses   GravityMedia\Ssdp\AbstractIdentifier
 * @uses   GravityMedia\Ssdp\Options\DiscoverOptions
 * @uses   GravityMedia\Ssdp\Request\Factory
 * @uses   GravityMedia\Ssdp\Request\SearchRequest
 * @uses   GravityMedia\Ssdp\SearchTarget
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testDiscover()
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $socket = $this->prophesize(Socket::class);

        $multicastFactory = $this->prophesize(MulticastFactory::class);
        $multicastFactory
            ->createSocket(Argument::exact(Client::MULTICAST_ADDRESS), Argument::type('int'))
            ->willReturn($socket->reveal());

        $request = $this->prophesize(RequestInterface::class);
        $request
            ->getMethod()
            ->willReturn(SearchRequest::METHOD);
        $request
            ->getHeaders()
            ->willReturn([]);
        $request
            ->getBody()
            ->willReturn('');
        $request
            ->getRequestTarget()
            ->willReturn('*');
        $request
            ->getProtocolVersion()
            ->willReturn('1.1');

        $requestFactory = $this->prophesize(RequestFactory::class);
        $requestFactory
            ->createDiscoverRequest(Argument::type(DiscoverOptions::class))
            ->willReturn($request->reveal());

        $discoverOptions = $this->prophesize(DiscoverOptions::class);

        $client = new Client();
        $client->setEventDispatcher($eventDispatcher->reveal());
        $client->setMulticastFactory($multicastFactory->reveal());
        $client->setRequestFactory($requestFactory->reveal());

        $client->discover($discoverOptions->reveal());

        $socket
            ->sendTo(Argument::type('string'), 0, sprintf('%s:%s', Client::MULTICAST_ADDRESS, Client::MULTICAST_PORT))
            ->shouldHaveBeenCalled();

        $socket
            ->recvFrom(Argument::type('int'), MSG_WAITALL, Argument::any())
            ->shouldHaveBeenCalled();
    }
}
