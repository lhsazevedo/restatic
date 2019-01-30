<?php

namespace ReStatic\Test;

use ReStatic\ProxyManager;

/**
 * @covers \ReStatic\ProxyManager
 */
class ProxyManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateStaticProxies()
    {
        // Instantiate ReStatic and use setContainer
        $proxyManager = new ProxyManager($this->getMock('Psr\Container\ContainerInterface'));
        $proxyManager->setContainer(new Fixture\Container(array('queue' => new \SplQueue)));

        // Register a proxy and enable them
        $proxyManager->addProxy('Queue', 'ReStatic\Test\Fixture\QueueProxy');
        $enabled = $proxyManager->enable();
        $this->assertTrue($enabled);

        // Enable again, which should be a no-op
        $proxyManager->enable();

        // Test to see if the alias was loaded and works as a static proxy
        \Queue::enqueue('foo');
        $queue = \Queue::getInstance();
        $this->assertInstanceOf('SplQueue', $queue);
        $this->assertEquals('foo', $queue->dequeue());
    }
}
