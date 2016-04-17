<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp;

use Ramsey\Uuid\Uuid;

/**
 * Notification type
 *
 * @package GravityMedia\Ssdp
 */
class NotificationType extends AbstractIdentifier
{
    /**
     * Create notification type from string.
     *
     * @param string $searchTarget
     *
     * @return self
     */
    public static function fromString($searchTarget)
    {
        $instance = new static();
        $searchTarget = strtolower(trim($searchTarget));

        if ($searchTarget === 'upnp:rootdevice') {
            return $instance->setRootDevice(true);
        }

        if (substr($searchTarget, 0, 4) === 'upnp') {
            return $instance->setId(Uuid::fromString(substr($searchTarget, 5)));
        }

        $parts = explode(':', $searchTarget, 5);
        if (5 !== count($parts) || 'urn' !== array_shift($parts)) {
            throw new \InvalidArgumentException('Invalid string.');
        }

        $instance->setDomainName(array_shift($parts));

        $name = array_shift($parts);

        if ('device' === $name) {
            return $instance
                ->setDevice(true)
                ->setType(array_shift($parts))
                ->setVersion((int)array_shift($parts));
        }

        if ('service' === $name) {
            return $instance
                ->setService(true)
                ->setType(array_shift($parts))
                ->setVersion((int)array_shift($parts));
        }

        throw new \InvalidArgumentException('Invalid string.');
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        if ($this->isRootDevice()) {
            return 'upnp:rootdevice';
        }

        if ($this->isDevice()) {
            return sprintf('urn:%s:device:%s:%u', $this->getDomainName(), $this->getType(), $this->getVersion());
        }

        if ($this->isService()) {
            return sprintf('urn:%s:service:%s:%u', $this->getDomainName(), $this->getType(), $this->getVersion());
        }

        $id = $this->getId();
        if (null === $id) {
            $id = Uuid::fromString(Uuid::NIL);
        }

        return sprintf('uuid:%s', $id);
    }
}
