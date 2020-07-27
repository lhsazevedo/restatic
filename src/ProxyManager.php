<?php

namespace ReStatic;

use Psr\Container\ContainerInterface;

/**
 * The Proxy Manager is the mediator between the Static Proxies, Container, and Alias Loader. It is the main object
 * that the client interacts with at runtime
 */
class ProxyManager
{
    const ROOT_NAMESPACE_GLOBAL = false;
    const ROOT_NAMESPACE_ANY = true;

    /**
     * @var ContainerInterface Container to inject into the Static Proxy classes and that holds the actual instances
     */
    private $container;

    /**
     * @var AliasLoaderInterface Alias Loader that resolves aliases to their corresponding Static Proxy classes
     */
    private $aliasLoader;

    /**
     * @param $container   Container that holds the actual instances
     * @param $aliasLoader Alias Loader object that stores and resolves
     */
    public function __construct(ContainerInterface $container, AliasLoaderInterface $aliasLoader = null)
    {
        $this->container = $container;
        $this->aliasLoader = $aliasLoader ?: new AliasLoader();
    }

    /**
     * Enables the Static Proxies by injecting the Container object and registering the Alias Loader for autoloading
     *
     * @param bool|string $rootNamespace The namespace that the alias should be created in
     *
     * @see \ReStatic\AliasLoaderInterface::register()
     */
    public function enable($rootNamespace = self::ROOT_NAMESPACE_GLOBAL): bool
    {
        // If ReStatic is already enabled, this is a no-op
        if ($this->aliasLoader->isRegistered()) {
            return true;
        }

        // Register the loader to handle aliases and link the proxies to the container
        if ($this->aliasLoader->register($rootNamespace)) {
            StaticProxy::setContainer($this->container);
        }

        return $this->aliasLoader->isRegistered();
    }

    /**
     * Adds a Static Proxy class by delegating to the Alias Loader
     *
     * @param $alias     Alias to associate with the Static Proxy class
     * @param $proxyFqcn FQCN of the Static Proxy class
     */
    public function addProxy(string $alias, string $proxyFqcn): self
    {
        $this->aliasLoader->addAlias($alias, $proxyFqcn);

        return $this;
    }

    /**
     * Sets the Container object that provides the actual subjects' instances
     *
     * @param $container Instance of a Container (or Service Locator)
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        StaticProxy::setContainer($this->container);

        return $this;
    }
}
