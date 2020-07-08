<?php

namespace ReStatic\Test;

use ReStatic\AliasLoader;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

/**
 * @covers \ReStatic\AliasLoader
 */
class AliasLoaderTest extends TestCase
{
    public function testCanRegisterLoader()
    {
        $loader = new AliasLoader();

        $this->assertFalse($loader->isRegistered());

        $loader->register('Fake\Foo');

        $this->assertTrue($loader->isRegistered());
        $this->assertFirstLoader($loader);
    }

    public function testRegisteringIsIdempotent()
    {
        $loader = new AliasLoader();
        
        $this->assertFalse($loader->isRegistered());

        $loader->register();
        $loader->register();

        $this->assertOneLoader();
    }

    public function testCanCreateClassAliasesWithTheLoader()
    {
        $loader = new AliasLoader();
        $loader->register();
        $loader->addAlias('alias_foo', Foo::class);
        $loader->load(Foo::class);

        $foo = new \alias_foo;
        $this->assertInstanceOf(Foo::class, $foo);
    }

    protected function isAliasLoader(Callable $callable): bool
    {
        if (! is_array($callable)) {
            return false;
        }

        [$object, $method] = $callable;

        return $object instanceof AliasLoader && $method === 'load';
    }

    protected function assertOneLoader()
    {
        $autoloaders = spl_autoload_functions();

        $aliasLoaders = array_filter($autoloaders, [$this, 'isAliasLoader']);

        $this->assertCount(1, $aliasLoaders);
    }

    protected function tearDown(): void
    {
        $loaders = spl_autoload_functions();

        foreach ($loaders as $loader) {
            if ($this->isAliasLoader($loader)) {
                spl_autoload_unregister($loader);
            }
        }
    }

    private function assertFirstLoader(AliasLoader $loader)
    {
        $autoloaders = spl_autoload_functions();
        $firstLoader = $autoloaders[0];

        $this->assertIsArray($firstLoader);

        [$object, $method] = $firstLoader;

        $this->assertEquals($loader, $object);
        $this->assertEquals('load', $method);
    }
}

class Foo {
    //
}
