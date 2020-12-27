# ReStatic

[![Build Status](https://travis-ci.org/lhsazevedo/restatic.svg?branch=master)](https://travis-ci.org/lhsazevedo/restatic)

Laravel like Facades (static proxies) for PSR11 containers.


```php
class HomeController
{
    public function __invoke()
    {
        // It just works!
        return View::render('home.index', [
            'articles' => DB::query('SELECT * FROM articles')
        ]);
    }
}
```

## Requirements
PHP 7.2+ or 8+

## Usage

Setup Container
```php
$container = new \DI\Container();
$container->set('db', function () {
    return new PDO('mysql:dbname=testdb;host=127.0.0.1', 'dbuser', 'dbpass');
});
```

Create Static Proxies
```php
// app/Facades/DB.php

class DB extends StaticProxy
{
    public static function getInstanceIdentifier()
    {
        return 'db';
    }
}
```

Setup Facades
```php
$proxyManager = new ProxyManager($container);
$proxyManager->addProxy('DB', DB::class);
$proxyManager->addProxy('View', View::class);
$proxyManager->enable(ProxyManager::ROOT_NAMESPACE_ANY);
```

## Concepts

* **Static Proxy** – Static class that proxies static method calls to instance methods on its *Proxy Subject*.
* **Proxy Subject (Instance)** – An object instance, stored in a *Container*, that is linked to a *Static Proxy*.
* **Proxy Manager** – Mediating object used to associate *Static Proxies* to an *Alias Loader* and *Container*.
* **Alias** – A memorable class name used as an alias to a fully-qualified class name of a *Static Proxy* class.
* **Alias Loader** – Maintainer of the associations between *Aliases* and *Static Proxies*. It is injected into the
  autoloader stack to handle Aliases as they are referenced.
* **Container** – A IoC container (e.g., a Service Locator or DIC) that provides the *Proxy Subject* instances. It must
  implement the PSR-11 stardard's `ContainerInterface`.
* **Instance Identifier** – An identifier used to fetch a *Proxy Subject* from a *Container*. Each *Static Proxy* must
  specify the Instance Identifier needed to get its Proxy Subject.
* **Root Namespace** – The namespace that an *Alias* can be referenced in. This can be configured as the global
  namespace (default), a specific namespace, or *any* namespace (i.e., the Alias works from any namespace).

## More
- [FAQ](docs/faq.md)
- [Demo application](docs/slim-demo.md)

## Inspiration
ReStatic is based on the awesome package XStatic created by [Jeremy Lindblom](https://twitter.com/jeremeamia).

## Disclaimer

> I would not consider myself to be *for* or *against* the use of static proxy interfaces (or Laravel's "Facades"), but I
> do think it is a fascinating and unique idea, and that it is very cool that you can write code this way and still have
> it work and be testable. I am curious to see if developers, especially library and framework developers, find ways to
> use, *but not require*, these static proxy interfaces in order to make their projects appeal to a wider range of PHP
> developers.  
> — [Jeremy Lindblom](https://twitter.com/jeremeamia)