<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\SsdpTest;

use GravityMedia\Ssdp\SsdpEvent;
use GravityMedia\Ssdp\SsdpMessenger;
use PHPUnit_Framework_Assert as Assert;

/**
 * SSDP messenger test
 *
 * @package GravityMedia\SsdpTest
 *
 * @covers GravityMedia\Ssdp\SsdpMessenger
 * @uses   GravityMedia\Ssdp\Message\AbstractMessage
 * @uses   GravityMedia\Ssdp\Message\Request\Advertisement\AbstractMessage
 * @uses   GravityMedia\Ssdp\Message\Request\Advertisement\Alive
 * @uses   GravityMedia\Ssdp\Message\Request\Advertisement\Byebye
 * @uses   GravityMedia\Ssdp\Message\Request\Advertisement\Update
 * @uses   GravityMedia\Ssdp\Message\Request\Search\AbstractMessage
 * @uses   GravityMedia\Ssdp\Message\Request\Search\Discover
 * @uses   GravityMedia\Ssdp\Message\Response\Search\AbstractMessage
 * @uses   GravityMedia\Ssdp\Message\Response\Search\Discover
 * @uses   GravityMedia\Ssdp\NotificationType
 * @uses   GravityMedia\Ssdp\Socket\Socket
 * @uses   GravityMedia\Ssdp\SearchTarget
 * @uses   GravityMedia\Ssdp\SsdpEvent
 * @uses   GravityMedia\Ssdp\UniqueServiceName
 */
class SsdpMessengerTest extends \PHPUnit_Framework_TestCase
{
    public function testAlive()
    {
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $eventDispatcherMock
            ->expects($this->atLeastOnce())
            ->method('dispatch')
            ->with($this->equalTo(SsdpEvent::ALIVE), $this->isInstanceOf('GravityMedia\Ssdp\SsdpEvent'));

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcherMock */
        $ssdpMessenger = new SsdpMessenger($eventDispatcherMock);
        $ssdpMessenger->alive();
    }

    public function testByebye()
    {
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $eventDispatcherMock
            ->expects($this->atLeastOnce())
            ->method('dispatch')
            ->with($this->equalTo(SsdpEvent::BYEBYE), $this->isInstanceOf('GravityMedia\Ssdp\SsdpEvent'));

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcherMock */
        $ssdpMessenger = new SsdpMessenger($eventDispatcherMock);
        $ssdpMessenger->byebye();
    }

    public function testDiscover()
    {
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $eventDispatcherMock
            ->expects($this->atLeastOnce())
            ->method('dispatch')
            ->with(
                Assert::logicalOr($this->equalTo(SsdpEvent::DISCOVER), $this->equalTo(SsdpEvent::EXCEPTION)),
                $this->isInstanceOf('GravityMedia\Ssdp\SsdpEvent')
            );

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcherMock */
        $ssdpMessenger = new SsdpMessenger($eventDispatcherMock);
        $ssdpMessenger->discover();
    }

    public function testUpdate()
    {
        $eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $eventDispatcherMock
            ->expects($this->atLeastOnce())
            ->method('dispatch')
            ->with($this->equalTo(SsdpEvent::UPDATE), $this->isInstanceOf('GravityMedia\Ssdp\SsdpEvent'));

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcherMock */
        $ssdpMessenger = new SsdpMessenger($eventDispatcherMock);
        $ssdpMessenger->update();
    }
}
