<?php

namespace ReStatic;

class AliasLoader implements AliasLoaderInterface
{
    /**
     * @var array Aliases that can be loaded
     */
    private $aliases;

    /**
     * @var array List of created aliases
     */
    private $aliased = [];

    /**
     * @var bool Whether or not the loader has been registered
     */
    private $isRegistered = false;

    /**
     * @var bool Whether or not we are in our loader
     */
    private $loading = false;

    /**
     * @var string Namespace that the alias should be created in
     */
    private $rootNamespace = false;

    /**
     * @param array $aliases
     */
    public function __construct(array $aliases = array())
    {
        $this->aliases = $aliases;
    }

    public function addAlias(string $alias, string $fqcn)
    {
        // Ensure aliases are only added once
        if (isset($this->aliases[$alias])) {
            throw new \RuntimeException("The alias, {$alias}, has already been added and cannot be modified.");
        }

        $this->aliases[$alias] = $fqcn;

        return $this;
    }

    public function isRegistered(): bool
    {
        return $this->isRegistered;
    }

    public function load($fqcn): void
    {
        // Determine the alias and namespace from the requested class
        $alias = $fqcn;
        $namespace = '';
        if (false !== ($pos = strrpos($fqcn, '\\'))) {
            $namespace = substr($alias, 0, $pos + 1);
            $alias = substr($alias, $pos + 1);
        }

        // If the alias has been registered, handle it
        if (isset($this->aliases[$alias])) {
            // Determine what namespace the alias should be loaded into, depending on the root namespace
            $namespace = ($this->rootNamespace === true) ? $namespace : $this->rootNamespace;

            // Create the class alias if not already aliased
            if (!isset($this->aliased[$key = $namespace . $alias])) {
                class_alias($this->aliases[$alias], $namespace . $alias);
            }
            $this->aliased[$key] = true;
        }
    }

    public function register($rootNamespace = false): bool
    {
        // Do nothing if the Alias Loader is already registered
        if ($this->isRegistered) {
            return true;
        }

        // If a specific Root Namespace was provided, normalize it to end in a backslash
        if ($rootNamespace) {
            $this->rootNamespace = is_string($rootNamespace) ? rtrim($rootNamespace, '\\') . '\\' : $rootNamespace;
        }

        // Register the `load()` method of this object as an autoloader
        return $this->isRegistered = spl_autoload_register(array($this, 'load'), true);
    }
}
