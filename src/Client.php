<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp;

use Clue\React\Multicast\Factory as MulticastFactory;
use GravityMedia\Ssdp\Options\AliveOptions;
use GravityMedia\Ssdp\Options\ByebyeOptions;
use GravityMedia\Ssdp\Options\DiscoverOptions;
use GravityMedia\Ssdp\Options\UpdateOptions;
use GravityMedia\Ssdp\Request\Factory\AliveFactory;
use GravityMedia\Ssdp\Request\Factory\ByebyeFactory;
use GravityMedia\Ssdp\Request\Factory\DiscoverFactory;
use GravityMedia\Ssdp\Request\Factory\UpdateFactory;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
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
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var MulticastFactory
     */
    private $multicastFactory;

    /**
     * @var AliveFactory
     */
    protected $aliveRequestFactory;

    /**
     * @var ByebyeFactory
     */
    protected $byebyeRequestFactory;

    /**
     * @var DiscoverFactory
     */
    protected $discoverRequestFactory;

    /**
     * @var UpdateFactory
     */
    protected $updateRequestFactory;

    /**
     * Create client object
     *
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * Get multicast factory
     *
     * @return MulticastFactory
     */
    public function getMulticastFactory()
    {
        if (null === $this->multicastFactory) {
            $this->multicastFactory = new MulticastFactory($this->loop);
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
     * Get alive request factory
     *
     * @return AliveFactory
     */
    public function getAliveRequestFactory()
    {
        if (null === $this->aliveRequestFactory) {
            $this->aliveRequestFactory = new AliveFactory();
        }

        return $this->aliveRequestFactory;
    }

    /**
     * Get byebye request factory
     *
     * @return ByebyeFactory
     */
    public function getByebyeRequestFactory()
    {
        if (null === $this->byebyeRequestFactory) {
            $this->byebyeRequestFactory = new ByebyeFactory();
        }

        return $this->byebyeRequestFactory;
    }

    /**
     * Get discover request factory
     *
     * @return DiscoverFactory
     */
    protected function getDiscoverRequestFactory()
    {
        if (null === $this->discoverRequestFactory) {
            $this->discoverRequestFactory = new DiscoverFactory();
        }

        return $this->discoverRequestFactory;
    }

    /**
     * Get update request factory
     *
     * @return UpdateFactory
     */
    public function getUpdateRequestFactory()
    {
        if (null === $this->updateRequestFactory) {
            $this->updateRequestFactory = new UpdateFactory();
        }

        return $this->updateRequestFactory;
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
        $request = $this->getAliveRequestFactory()->createRequest($options);
        $data = trim(RequestSerializer::toString($request)) . "\r\n\r\n";

        $socket = $this->getMulticastFactory()->createSender();

        $socket->send($data, sprintf('%s:%s', self::MULTICAST_ADDRESS, self::MULTICAST_PORT));

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
        $request = $this->getByebyeRequestFactory()->createRequest($options);
        $data = trim(RequestSerializer::toString($request)) . "\r\n\r\n";

        $socket = $this->getMulticastFactory()->createSender();

        $socket->send($data, sprintf('%s:%s', self::MULTICAST_ADDRESS, self::MULTICAST_PORT));

        return $this;
    }

    /**
     * Send discover request
     *
     * @param DiscoverOptions $options
     * @param int $timeout
     *
     * @return PromiseInterface
     */
    public function discover(DiscoverOptions $options, $timeout = 2)
    {
        $request = $this->getDiscoverRequestFactory()->createRequest($options);
        $data = trim(RequestSerializer::toString($request)) . "\r\n\r\n";

        $socket = $this->getMulticastFactory()->createSender();

        $timer = $this->loop->addTimer($timeout, function () use ($socket, &$deferred) {
            $deferred->resolve();
            $socket->close();
        });

        $deferred = new Deferred(function () use ($socket, &$timer) {
            $timer->cancel();
            $socket->close();
            throw new \RuntimeException('Discovery cancelled.');
        });

        $socket->on('message', function ($message, $remote) use ($deferred) {
            $deferred->notify([
                'message' => $this->parseMessage($message),
                'remote' => $remote
            ]);
        });

        $socket->send($data, sprintf('%s:%s', self::MULTICAST_ADDRESS, self::MULTICAST_PORT));

        return $deferred->promise();
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
        $request = $this->getUpdateRequestFactory()->createRequest($options);
        $data = trim(RequestSerializer::toString($request)) . "\r\n\r\n";

        $socket = $this->getMulticastFactory()->createSender();

        $socket->send($data, sprintf('%s:%s', self::MULTICAST_ADDRESS, self::MULTICAST_PORT));

        return $this;
    }

    /**
     * Parse message
     *
     * @param string $message
     *
     * @return Message
     */
    protected function parseMessage($message)
    {
        $response = ResponseSerializer::fromString($message);
        $message = new Message();

        if ($response->hasHeader('CACHE-CONTROL')) {
            $value = $response->getHeaderLine('CACHE-CONTROL');
            $message->setLifetime(intval(substr($value, strpos($value, '=') + 1)));
        }

        if ($response->hasHeader('DATE')) {
            $message->setDate(new \DateTime($response->getHeaderLine('DATE')));
        }

        if ($response->hasHeader('LOCATION')) {
            $message->setDescriptionUrl(new Uri($response->getHeaderLine('LOCATION')));
        }

        if ($response->hasHeader('SERVER')) {
            $message->setServerString($response->getHeaderLine('SERVER'));
        }

        if ($response->hasHeader('ST')) {
            $message->setSearchTargetString($response->getHeaderLine('ST'));
        }

        if ($response->hasHeader('USN')) {
            $message->setUniqueServiceNameString($response->getHeaderLine('USN'));
        }

        return $message;
    }
}
