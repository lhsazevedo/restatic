<?php

namespace ReStatic;

/**
 * An Alias Loader is registered as an autoloader, and creates class aliases based on the aliases added to the loader
 */
interface AliasLoaderInterface
{
    /**
     * Creates an alias to a fully-qualified class name (FQCN)
     *
     * @param $alias Alias to associate with the class
     * @param $fqcn  FQCN of the class
     *
     * @throws \RuntimeException if the alias has already been added
     */
    public function addAlias(string $alias, string $fqcn): self;

    /**
     * Checks if the the Alias Loader has already been registered
     */
    public function isRegistered(): bool;

    /**
     * Loads an alias by creating a class_alias() to the requested class. This is used as an autoload function
     *
     * @param $fqcn FQCN of the class to be loaded
     */
    public function load(string $fqcn): void;

    /**
     * Registers the Alias Loader as an autoloader so that aliases can be resolved via `class_alias()`
     *
     * The Root Namespace can be configured such that the alias is created in a particular namespace. Valid values for
     * the `$rootNamespace` parameter are as follows:
     *
     * - `false` - The alias will be created in the global namespace (default)
     * - `true` - The alias will be created in the namespace where it is referenced
     * - Any specific namespace (e.g., 'Foo\\Bar') - The alias is created in the specified namespace
     * 
     * Returns true if the registration was successful
     *
     * @param bool|string $rootNamespace Namespace where the alias should be created
     */
    public function register($rootNamespace = false): bool;
}
