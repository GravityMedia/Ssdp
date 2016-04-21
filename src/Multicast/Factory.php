<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Multicast;

use Socket\Raw\Factory as SocketFactory;
use Socket\Raw\Socket;

/**
 * Multicast factory class
 *
 * @package GravityMedia\Ssdp\Multicast
 */
class Factory
{
    /**
     * @var SocketFactory
     */
    protected $socketFactory;

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
     * Create socket
     *
     * @param string $address
     * @param int $timeout
     *
     * @return Socket
     */
    public function createSocket($address, $timeout = 0)
    {
        $socket = $this->getSocketFactory()->createUdp4();

        $socket
            ->setOption(SOL_SOCKET, SO_BROADCAST, 1)
            ->setOption(SOL_SOCKET, SO_SNDTIMEO, ['sec' => $timeout, 'usec' => 0])
            ->setOption(SOL_SOCKET, SO_RCVTIMEO, ['sec' => $timeout, 'usec' => 0]);

        $socket
            ->setOption(IPPROTO_IP, IP_MULTICAST_IF, 0)
            ->setOption(IPPROTO_IP, IP_MULTICAST_LOOP, 0)
            ->setOption(IPPROTO_IP, IP_MULTICAST_TTL, 1)
            ->setOption(IPPROTO_IP, MCAST_JOIN_GROUP, ['group' => $address, 'interface' => 0]);

        $socket->bind('0.0.0.0');

        return $socket;
    }
}
