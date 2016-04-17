<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\SsdpTest;

use Clue\React\Multicast\Factory as MulticastFactory;
use GravityMedia\Ssdp\Client;
use GravityMedia\Ssdp\Options\DiscoverOptions;
use React\Datagram\SocketInterface;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use React\Promise\CancellablePromiseInterface;
use React\Promise\PromiseInterface;

/**
 * Client test class
 *
 * @package GravityMedia\SsdpTest
 *
 * @covers GravityMedia\Ssdp\Client
 * @uses   GravityMedia\Ssdp\AbstractIdentifier
 * @uses   GravityMedia\Ssdp\Options\DiscoverOptions
 * @uses   GravityMedia\Ssdp\Request\Factory\DiscoverFactory
 * @uses   GravityMedia\Ssdp\Request\SearchRequest
 * @uses   GravityMedia\Ssdp\SearchTarget
 * @uses   GravityMedia\Ssdp\Message
 */
class ClientTest extends TestCase
{
    public function testDiscoverCancel()
    {
        $socket = $this->getMock(SocketInterface::class);
        $socket
            ->expects($this->once())
            ->method('send');
        $socket
            ->expects($this->once())
            ->method('close');

        $multicastFactory = $this->getMockBuilder(MulticastFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $multicastFactory
            ->expects($this->once())
            ->method('createSender')
            ->will($this->returnValue($socket));

        $timer = $this->getMock(TimerInterface::class);
        $timer
            ->expects($this->once())
            ->method('cancel');

        $loop = $this->getMock(LoopInterface::class);
        $loop
            ->expects($this->once())
            ->method('addTimer')
            ->will($this->returnValue($timer));

        $client = new Client($loop);
        $client->setMulticastFactory($multicastFactory);

        $options = new DiscoverOptions();
        $promise = $client->discover($options);
        $this->assertInstanceOf(PromiseInterface::class, $promise);
        if (!($promise instanceof CancellablePromiseInterface)) {
            $this->markTestSkipped();
        }

        $promise->cancel();
        $promise->then(
            null,
            $this->expectCallableOnce()
        );
    }

    public function testDiscoverTimeout()
    {
        $loop = LoopFactory::create();
        $client = new Client($loop);

        $options = new DiscoverOptions();
        $options->setMaximumWaitTime(0.01);

        $promise = $client->discover($options);
        $loop->run();

        $promise->then(
            $this->expectCallableOnce(),
            $this->expectCallableNever(),
            $this->expectCallableNever()
        );
    }

    /*public function testDiscover()
    {
        $loop = LoopFactory::create();
        $client = new Client($loop);

        $options = new DiscoverOptions();

        $client->discover($options)->then(
            function () {
                echo 'Discovery completed.' . PHP_EOL;
            },
            function ($reason) {
                echo 'An error occurred: ' . $reason . PHP_EOL;
            },
            function ($progress) {
                print 'Device found:' . PHP_EOL;
                var_dump($progress);
            }
        );

        $loop->run();
    }*/
}
