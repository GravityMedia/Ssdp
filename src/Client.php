<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp;

use GravityMedia\Ssdp\Event\DiscoverEvent;
use GravityMedia\Ssdp\Exception\DiscoverException;
use GravityMedia\Ssdp\Multicast\Factory as MulticastFactory;
use GravityMedia\Ssdp\Options\AliveOptions;
use GravityMedia\Ssdp\Options\ByebyeOptions;
use GravityMedia\Ssdp\Options\DiscoverOptions;
use GravityMedia\Ssdp\Options\UpdateOptions;
use GravityMedia\Ssdp\Request\Factory as RequestFactory;
use Psr\Http\Message\RequestInterface;
use Socket\Raw\Exception as SocketException;
use Socket\Raw\Socket;
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
     * @var MulticastFactory
     */
    protected $multicastFactory;

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
     * Get multicast factory
     *
     * @return MulticastFactory
     */
    public function getMulticastFactory()
    {
        if (null === $this->multicastFactory) {
            $this->multicastFactory = new MulticastFactory();
        }

        return $this->multicastFactory;
    }

    /**
     * Set multicast factory
     *
     * @param MulticastFactory $multicastFactory
     *
     * @return $this
     */
    public function setMulticastFactory(MulticastFactory $multicastFactory)
    {
        $this->multicastFactory = $multicastFactory;

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
     * Send request
     *
     * @param Socket $socket
     * @param RequestInterface $request
     * @param callable $onSuccess
     * @param callable $onFailure
     *
     * @return boolean
     */
    protected function sendRequest(Socket $socket, RequestInterface $request, $onSuccess, $onFailure)
    {
        $data = trim(RequestSerializer::toString($request)) . "\r\n\r\n";

        try {
            $bytes = $socket->sendTo($data, 0, sprintf('%s:%s', self::MULTICAST_ADDRESS, self::MULTICAST_PORT));
        } catch (SocketException $exception) {
            return $onFailure($socket, $exception);
        }

        return $onSuccess($socket, $bytes);
    }

    /**
     * Receive response
     *
     * @param Socket $socket
     * @param callable $onSuccess
     * @param callable $onFailure
     *
     * @return boolean
     */
    protected function receiveResponse(Socket $socket, $onSuccess, $onFailure)
    {
        try {
            $message = $socket->recvFrom(1024, MSG_WAITALL, $remote);
        } catch (SocketException $exception) {
            return $onFailure($socket, $exception);
        }

        return $onSuccess($socket, $message, $remote);
    }

    /**
     * Send alive request
     *
     * @param AliveOptions $options
     *
     * @return boolean
     */
    public function alive(AliveOptions $options)
    {
        return $this->sendRequest(
            $this->getMulticastFactory()->createSocket(self::MULTICAST_ADDRESS),
            $this->getRequestFactory()->createAliveRequest($options),
            function (Socket $socket) {
                $socket->close();

                return true;
            },
            function (Socket $socket, SocketException $exception) {
                $socket->close();

                $event = new DiscoverEvent();
                $event->setException(new DiscoverException('Error sending alive broadcast', 0, $exception));
                $this->getEventDispatcher()->dispatch(DiscoverEvent::EVENT_DISCOVER_ERROR, $event);

                return false;
            }
        );
    }

    /**
     * Send byebye request
     *
     * @param ByebyeOptions $options
     *
     * @return boolean
     */
    public function byebye(ByebyeOptions $options)
    {
        return $this->sendRequest(
            $this->getMulticastFactory()->createSocket(self::MULTICAST_ADDRESS),
            $this->getRequestFactory()->createByebyeRequest($options),
            function (Socket $socket) {
                $socket->close();

                return true;
            },
            function (Socket $socket, SocketException $exception) {
                $socket->close();

                $event = new DiscoverEvent();
                $event->setException(new DiscoverException('Error sending byebye broadcast', 0, $exception));
                $this->getEventDispatcher()->dispatch(DiscoverEvent::EVENT_DISCOVER_ERROR, $event);

                return false;
            }
        );
    }

    /**
     * Send discover request
     *
     * @param DiscoverOptions $options
     * @param int $timeout
     *
     * @return boolean
     */
    public function discover(DiscoverOptions $options, $timeout = 2)
    {
        return $this->sendRequest(
            $this->getMulticastFactory()->createSocket(self::MULTICAST_ADDRESS, $timeout),
            $this->getRequestFactory()->createDiscoverRequest($options),
            function (Socket $socket) {
                do {
                    $continue = $this->receiveResponse(
                        $socket,
                        function (Socket $socket, $message, $remote) {
                            if (null === $message) {
                                return false;
                            }

                            $event = $this->createDiscoverEvent($message)->setRemote($remote);
                            $this->getEventDispatcher()->dispatch(DiscoverEvent::EVENT_DISCOVER, $event);

                            return true;
                        },
                        function (Socket $socket, SocketException $exception) {
                            if (SOCKET_EAGAIN === $exception->getCode()) {
                                return false;
                            }

                            $exception = new DiscoverException('Error receiving discover message', 0, $exception);
                            $event = new DiscoverEvent();
                            $event->setException($exception);
                            $this->getEventDispatcher()->dispatch(DiscoverEvent::EVENT_DISCOVER_ERROR, $event);

                            return false;
                        }
                    );

                } while ($continue);

                $socket->close();

                return true;
            },
            function (Socket $socket, SocketException $exception) {
                $socket->close();

                $event = new DiscoverEvent();
                $event->setException(new DiscoverException('Error sending discover broadcast', 0, $exception));
                $this->getEventDispatcher()->dispatch(DiscoverEvent::EVENT_DISCOVER_ERROR, $event);

                return false;
            }
        );
    }

    /**
     * Send update request
     *
     * @param UpdateOptions $options
     *
     * @return boolean
     */
    public function update(UpdateOptions $options)
    {
        return $this->sendRequest(
            $this->getMulticastFactory()->createSocket(self::MULTICAST_ADDRESS),
            $this->getRequestFactory()->createUpdateRequest($options),
            function (Socket $socket) {
                $socket->close();

                return true;
            },
            function (Socket $socket, SocketException $exception) {
                $socket->close();

                $event = new DiscoverEvent();
                $event->setException(new DiscoverException('Error sending byebye broadcast', 0, $exception));
                $this->getEventDispatcher()->dispatch(DiscoverEvent::EVENT_DISCOVER_ERROR, $event);

                return false;
            }
        );
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
