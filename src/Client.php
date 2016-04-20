<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp;

use GravityMedia\Ssdp\Event\DiscoverEvent;
use GravityMedia\Ssdp\Exception\DiscoverException;
use GravityMedia\Ssdp\Options\AliveOptions;
use GravityMedia\Ssdp\Options\ByebyeOptions;
use GravityMedia\Ssdp\Options\DiscoverOptions;
use GravityMedia\Ssdp\Options\UpdateOptions;
use GravityMedia\Ssdp\Request\Factory as RequestFactory;
use Socket\Raw\Exception as SocketException;
use Socket\Raw\Factory as SocketFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zend\Diactoros\Request\Serializer as RequestSerializer;
use Zend\Diactoros\Response\Serializer as ResponseSerializer;
use Zend\Diactoros\Uri;

/**
 * Ssdp client class
 *
 * @package GravityMedia\Ssdp
 */
class Client
{
    /**
     * The multicast address
     */
    const MULTICAST_ADDRESS = '239.255.255.250';

    /**
     * The multicast port
     */
    const MULTICAST_PORT = 1900;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var SocketFactory
     */
    protected $socketFactory;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * Get event dispatcher
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if (null === $this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcher();
        }

        return $this->eventDispatcher;
    }

    /**
     * Set event dispatcher
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Get socket factory
     *
     * @return SocketFactory
     */
    public function getSocketFactory()
    {
        if (null === $this->socketFactory) {
            $this->socketFactory = new SocketFactory();
        }

        return $this->socketFactory;
    }

    /**
     * Set socket factory
     *
     * @param SocketFactory $socketFactory
     *
     * @return $this
     */
    public function setSocketFactory(SocketFactory $socketFactory)
    {
        $this->socketFactory = $socketFactory;

        return $this;
    }

    /**
     * Get request factory
     *
     * @return RequestFactory
     */
    public function getRequestFactory()
    {
        if (null === $this->requestFactory) {
            $this->requestFactory = new RequestFactory();
        }

        return $this->requestFactory;
    }

    /**
     * Set request factory
     *
     * @param RequestFactory $requestFactory
     *
     * @return $this
     */
    public function setRequestFactory(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;

        return $this;
    }

    /**
     * Send alive request
     *
     * @param AliveOptions $options
     *
     * @return PromiseInterface
     */
    public function alive(AliveOptions $options)
    {
        $request = $this->getRequestFactory()->createAliveRequest($options);
        $data = trim(RequestSerializer::toString($request)) . "\r\n\r\n";

        $socket = $this->getSocketFactory()->createUdp4();
        $socket->setOption(SOL_SOCKET, SO_BROADCAST, 1);
        $socket->setOption(IPPROTO_IP, IP_MULTICAST_IF, 0);
        $socket->setOption(IPPROTO_IP, IP_MULTICAST_LOOP, 0);
        $socket->setOption(IPPROTO_IP, IP_MULTICAST_TTL, 4);
        $socket->setOption(IPPROTO_IP, MCAST_JOIN_GROUP, ['group' => self::MULTICAST_ADDRESS, 'interface' => 0]);
        $socket->bind('0.0.0.0');
        $socket->sendTo($data, 0, sprintf('%s:%s', self::MULTICAST_ADDRESS, self::MULTICAST_PORT));

        return $this;
    }

    /**
     * Send byebye request
     *
     * @param ByebyeOptions $options
     *
     * @return PromiseInterface
     */
    public function byebye(ByebyeOptions $options)
    {
        $request = $this->getRequestFactory()->createByebyeRequest($options);
        $data = trim(RequestSerializer::toString($request)) . "\r\n\r\n";

        $socket = $this->getSocketFactory()->createUdp4();
        $socket->setOption(SOL_SOCKET, SO_BROADCAST, 1);
        $socket->setOption(IPPROTO_IP, IP_MULTICAST_IF, 0);
        $socket->setOption(IPPROTO_IP, IP_MULTICAST_LOOP, 0);
        $socket->setOption(IPPROTO_IP, IP_MULTICAST_TTL, 4);
        $socket->setOption(IPPROTO_IP, MCAST_JOIN_GROUP, ['group' => self::MULTICAST_ADDRESS, 'interface' => 0]);
        $socket->bind('0.0.0.0');
        $socket->sendTo($data, 0, sprintf('%s:%s', self::MULTICAST_ADDRESS, self::MULTICAST_PORT));

        return $this;
    }

    /**
     * Send discover request
     *
     * @param DiscoverOptions $options
     * @param int $timeout
     *
     * @return $this
     */
    public function discover(DiscoverOptions $options, $timeout = 2)
    {
        $request = $this->getRequestFactory()->createDiscoverRequest($options);
        $data = trim(RequestSerializer::toString($request)) . "\r\n\r\n";

        $socket = $this->getSocketFactory()->createUdp4();
        $socket->setOption(SOL_SOCKET, SO_BROADCAST, 1);

        try {
            $socket->sendTo($data, 0, sprintf('%s:%s', self::MULTICAST_ADDRESS, self::MULTICAST_PORT));
        } catch (SocketException $exception) {
            $socket->close();

            $event = new DiscoverEvent();
            $event->setException(new DiscoverException('Error sending discover broadcast', 0, $exception));
            $this->getEventDispatcher()->dispatch(DiscoverEvent::EVENT_DISCOVER_ERROR, $event);

            return $this;
        }

        $socket->setOption(SOL_SOCKET, SO_RCVTIMEO, ['sec' => $timeout, 'usec' => 0]);
        do {
            try {
                $message = $socket->recvFrom(1024, MSG_WAITALL, $remote);
            } catch (SocketException $exception) {
                if (SOCKET_EAGAIN === $exception->getCode()) {
                    break;
                }

                $socket->close();

                $event = new DiscoverEvent();
                $event->setException(new DiscoverException('Error receiving discover message', 0, $exception));
                $this->getEventDispatcher()->dispatch(DiscoverEvent::EVENT_DISCOVER_ERROR, $event);

                return $this;
            }

            if (null !== $message) {
                $event = $this->createDiscoverEvent($message)->setRemote($remote);
                $this->getEventDispatcher()->dispatch(DiscoverEvent::EVENT_DISCOVER, $event);
            }
        } while (null !== $message);

        $socket->close();

        return $this;
    }

    /**
     * Send update request
     *
     * @param UpdateOptions $options
     *
     * @return PromiseInterface
     */
    public function update(UpdateOptions $options)
    {
        $request = $this->getRequestFactory()->createUpdateRequest($options);
        $data = trim(RequestSerializer::toString($request)) . "\r\n\r\n";

        $socket = $this->getSocketFactory()->createUdp4();
        $socket->setOption(SOL_SOCKET, SO_BROADCAST, 1);
        $socket->setOption(IPPROTO_IP, IP_MULTICAST_IF, 0);
        $socket->setOption(IPPROTO_IP, IP_MULTICAST_LOOP, 0);
        $socket->setOption(IPPROTO_IP, IP_MULTICAST_TTL, 4);
        $socket->setOption(IPPROTO_IP, MCAST_JOIN_GROUP, ['group' => self::MULTICAST_ADDRESS, 'interface' => 0]);
        $socket->bind('0.0.0.0');
        $socket->sendTo($data, 0, sprintf('%s:%s', self::MULTICAST_ADDRESS, self::MULTICAST_PORT));

        return $this;
    }

    /**
     * Create discover event
     *
     * @param string $message
     *
     * @return DiscoverEvent
     */
    protected function createDiscoverEvent($message)
    {
        $response = ResponseSerializer::fromString($message);
        $event = new DiscoverEvent();

        if ($response->hasHeader('CACHE-CONTROL')) {
            $value = $response->getHeaderLine('CACHE-CONTROL');
            $event->setLifetime(intval(substr($value, strpos($value, '=') + 1)));
        }

        if ($response->hasHeader('DATE')) {
            $event->setDate(new \DateTime($response->getHeaderLine('DATE')));
        }

        if ($response->hasHeader('LOCATION')) {
            $event->setDescriptionUrl(new Uri($response->getHeaderLine('LOCATION')));
        }

        if ($response->hasHeader('SERVER')) {
            $event->setServerString($response->getHeaderLine('SERVER'));
        }

        if ($response->hasHeader('ST')) {
            $event->setSearchTargetString($response->getHeaderLine('ST'));
        }

        if ($response->hasHeader('USN')) {
            $event->setUniqueServiceNameString($response->getHeaderLine('USN'));
        }

        return $event;
    }
}
