<?php

namespace ReStatic\Test;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use ReStatic\StaticProxy;
use ReStatic\Test\Fixture\QueueProxy;
use RuntimeException;

/**
 * @covers \ReStatic\StaticProxy
 *
 */
class StaticProxyTest extends TestCase
{
    public function testErrorWhenUsingBaseClassDirectly()
    {
        $this->expectException(BadMethodCallException::class);

        StaticProxy::getInstanceIdentifier();
    }

    public function testErrorWhenContainerNotSet()
    {
        $rc = new \ReflectionClass(StaticProxy::class);
        $rp = $rc->getProperty('container');
        $rp->setAccessible(true);
        $rp->setValue(null, null);

        $this->expectException(RuntimeException::class);

        QueueProxy::getInstance();
    }

    public function testCanSetAndUseContainer()
    {
        $queue = new \SplQueue();
        $container = new Fixture\Container(array('queue' => $queue));
        QueueProxy::setContainer($container);
        $this->assertTrue(QueueProxy::isEmpty());
    }
}
