<?php

namespace Tests;

use ReStatic\AliasLoader;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

/**
 * @covers \ReStatic\AliasLoader
 * @runTestsInSeparateProcesses
 */
class AliasLoaderTest extends TestCase
{
    public function testCanRegisterLoader()
    {
        $loader = new AliasLoader();

        $this->assertFalse($loader->isRegistered());

        $loader->register('Fake\Foo');

        $this->assertTrue($loader->isRegistered());
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

        $foo = new \alias_foo();
        $this->assertInstanceOf(Foo::class, $foo);
    }

    public function testSameClassNameWithRootNamespacesLoadsCorrectClass()
    {
        $loader = new AliasLoader();
        $loader->register(true);
        $loader->addAlias('Bar', Foo::class);

        $this->assertNotInstanceOf(Foo::class, $bar = new Fixture\Bar());
        $this->assertTrue($bar->notFoo); // double check

        $this->assertInstanceOf(Foo::class, new Bar());
        $this->assertInstanceOf(Foo::class, new \Bar());
    }

    public function testSameClassNameWithoutRootNamespacesLoadsCorrectClass()
    {
        $loader = new AliasLoader();
        $loader->register();
        $loader->addAlias('Bar', Foo::class);

        $this->assertNotInstanceOf(Foo::class, $bar = new Fixture\Bar());
        $this->assertTrue($bar->notFoo); // double check

        $this->assertInstanceOf(Foo::class, new \Bar());

        $this->expectException(\Error::class);
        $this->expectExceptionMessageMatches("/Class ['\"]Tests\\Bar['\"'] not found/");
        new Bar();
    }

    public function testAliasesCanOnlyBeRegisteredOnce()
    {
        $loader = new AliasLoader();
        $loader->addAlias('test', 'test');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("The alias, test, has already been added and cannot be modified.");

        $loader->addAlias('test', 'test');
    }

    protected function isAliasLoader(callable $callable): bool
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
}

class Foo
{
    //
}
