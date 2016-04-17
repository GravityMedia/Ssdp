<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\Ssdp\Options;

use GravityMedia\Ssdp\SearchTarget;

/**
 * Discover request options class
 *
 * @package GravityMedia\Ssdp\Request\Options
 */
class DiscoverOptions
{
    /**
     * Default maximum wait time (seconds to delay response)
     */
    const DEFAULT_MAXIMUM_WAIT_TIME = 1;

    /**
     * Default search target
     */
    const DEFAULT_SEARCH_TARGET = 'ssdp:all';

    /**
     * @var int
     */
    protected $maximumWaitTime;

    /**
     * @var SearchTarget
     */
    protected $searchTarget;

    /**
     * Get maximum wait time
     *
     * @return int
     */
    public function getMaximumWaitTime()
    {
        if (null === $this->maximumWaitTime) {
            return self::DEFAULT_MAXIMUM_WAIT_TIME;
        }

        return $this->maximumWaitTime;
    }

    /**
     * Set maximum wait time
     *
     * @param int $maximumWaitTime
     *
     * @return $this
     */
    public function setMaximumWaitTime($maximumWaitTime)
    {
        $this->maximumWaitTime = $maximumWaitTime;
        return $this;
    }

    /**
     * Get search target
     *
     * @return SearchTarget
     */
    public function getSearchTarget()
    {
        if (null === $this->searchTarget) {
            return SearchTarget::fromString(self::DEFAULT_SEARCH_TARGET);
        }

        return $this->searchTarget;
    }

    /**
     * Set search target
     *
     * @param SearchTarget $searchTarget
     *
     * @return $this
     */
    public function setSearchTarget(SearchTarget $searchTarget)
    {
        $this->searchTarget = $searchTarget;
        return $this;
    }
}
