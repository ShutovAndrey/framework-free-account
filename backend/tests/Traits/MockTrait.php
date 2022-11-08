<?php

namespace tests\Traits;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * App Test Trait.
 */
trait MockTrait
{
    /**
     * Add mock to container.
     *
     * @param string $class The class or interface
     *
     * @return MockObject The mock
     */
    protected function mock(string $class): MockObject
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class not found: %s', $class));
        }

        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->set($class, $mock);

        return $mock;
    }
}
