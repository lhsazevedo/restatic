<?php

namespace ReStatic\Test;

use ReStatic\StaticProxy;
use ReStatic\Test\Fixture\QueueProxy;

/**
 * @covers \ReStatic\StaticProxy
 *
 */
class StaticProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \BadMethodCallException
     */
    public function testErrorWhenUsingBaseClassDirectly()
    {
        StaticProxy::getInstanceIdentifier();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testErrorWhenContainerNotSet()
    {
        $rc = new \ReflectionClass('ReStatic\StaticProxy');
        $rp = $rc->getProperty('container');
        $rp->setAccessible(true);
        $rp->setValue(null, null);

        QueueProxy::getInstance();
    }

    public function testCanSetAndUseContainer()
    {
        $queue = new \SplQueue;
        $container = new Fixture\Container(array('queue' => $queue));
        QueueProxy::setContainer($container);
        $this->assertSame(
            $container,
            $this->readAttribute('ReStatic\Test\Fixture\QueueProxy', 'container')
        );
        $this->assertEquals('queue', QueueProxy::getInstanceIdentifier());
        $this->assertTrue(QueueProxy::isEmpty());
    }
}
