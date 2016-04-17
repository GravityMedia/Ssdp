<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp;

use Ramsey\Uuid\Uuid;

/**
 * Unique service name
 *
 * @package GravityMedia\Ssdp
 */
class UniqueServiceName extends AbstractIdentifier
{
    /**
     * Create unique service name from string.
     *
     * @param string $uniqueServiceName
     *
     * @return self
     */
    public static function fromString($uniqueServiceName)
    {
        $instance = new static();
        $uniqueServiceName = strtolower(trim($uniqueServiceName));

        $parts = explode('::', $uniqueServiceName, 2);
        if (!substr($parts[0], 0, 4) === 'uuid') {
            throw new \InvalidArgumentException('Invalid string.');
        }

        $instance->setId(Uuid::fromString(substr($parts[0], 5)));
        if (count($parts) < 2) {
            return $instance;
        }

        if ($parts[1] === 'upnp:rootdevice') {
            return $instance->setRootDevice(true);
        }

        $parts = explode(':', $parts[1], 5);
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
        $id = $this->getId();
        if (null === $id) {
            $id = Uuid::fromString(Uuid::NIL);
        }

        if ($this->isRootDevice()) {
            return sprintf('uuid:%s::upnp:rootdevice', $id);
        }

        if ($this->isDevice()) {
            return sprintf(
                'uuid:%s::urn:%s:device:%s:%u',
                $id,
                $this->getDomainName(),
                $this->getType(),
                $this->getVersion()
            );
        }

        if ($this->isService()) {
            return sprintf(
                'uuid:%s::urn:%s:service:%s:%u',
                $id,
                $this->getDomainName(),
                $this->getType(),
                $this->getVersion()
            );
        }

        return sprintf('uuid:%s', $id);
    }
}
