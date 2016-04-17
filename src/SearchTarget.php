<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp;

/**
 * Search target
 *
 * @package GravityMedia\Ssdp
 */
class SearchTarget extends NotificationType
{
    /**
     * Return whether to search for all devices
     *
     * @return bool
     */
    public function isAll()
    {
        if (null !== $this->getId()) {
            return false;
        }

        if ($this->isRootDevice()) {
            return false;
        }

        if ($this->isDevice()) {
            return false;
        }

        if ($this->isService()) {
            return false;
        }

        return true;
    }

    /**
     * Create search target from string.
     *
     * @param string $searchTarget
     *
     * @return self
     */
    public static function fromString($searchTarget)
    {
        if (strtolower(trim($searchTarget)) === 'ssdp:all') {
            return new static();
        }

        return parent::fromString($searchTarget);
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        if ($this->isAll()) {
            return 'ssdp:all';
        }

        return parent::toString();
    }
}
