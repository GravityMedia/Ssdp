<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

namespace GravityMedia\SsdpTest;

/**
 * Test case class
 *
 * @package GravityMedia\SsdpTest
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function createCallableMock()
    {
        return $this->getMock(CallableStub::class);
    }

    protected function expectCallableOnce()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke');

        return $mock;
    }

    protected function expectCallableNever()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->never())
            ->method('__invoke');

        return $mock;
    }
}
