<?php

namespace ReStatic\Test;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReStatic\ProxyManager;

/**
 * @covers \ReStatic\ProxyManager
 */
class ProxyManagerTest extends TestCase
{
    public function testCanCreateStaticProxies()
    {
        // Instantiate ReStatic and use setContainer
        $container = $this->createContainerMock();
        $proxyManager = new ProxyManager($container);
        $proxyManager->setContainer(new Fixture\Container(array('queue' => new \SplQueue())));

        // Register a proxy and enable them
        $proxyManager->addProxy('Queue', Fixture\QueueProxy::class);
        $enabled = $proxyManager->enable();
        $this->assertTrue($enabled);

        // Enable again, which should be a no-op
        $proxyManager->enable();

        // Test to see if the alias was loaded and works as a static proxy
        \Queue::enqueue('foo');
        $queue = \Queue::getInstance();
        $this->assertInstanceOf(\SplQueue::class, $queue);
        $this->assertEquals('foo', $queue->dequeue());
    }

    public function createContainerMock(): ContainerInterface
    {
        /** @var \Psr\Container\ContainerInterface $container */
        $container = $this->createMock(ContainerInterface::class);
        return $container;
    }
}
