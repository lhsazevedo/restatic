# Slim demo

I will show you a simple [Slim](https://slimframework.com/) application.

Your application bootstrap:

```php
<?php

use App\Controllers\HomeController;
use App\Facades\DB;
use App\Facades\View;
use ReStatic\ProxyManager;
use Slim\Factory\AppFactory;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;

require __DIR__ . '/../vendor/autoload.php';

// Setup Container
$container = new \DI\Container();
$container->set('db', function () {
    return new PDO('mysql:dbname=testdb;host=127.0.0.1', 'dbuser', 'dbpass');
});
$cointainer->set('view', function () {
    $loader = new TwigFilesystemLoader('/path/to/templates');
    return new TwigEnvironment($loader);
})

// Setup Facades
$proxyManager = new ProxyManager($container);
$proxyManager->addProxy('DB', DB::class);
$proxyManager->addProxy('View', View::class);
$proxyManager->enable(ProxyManager::ROOT_NAMESPACE_ANY);

// Create App
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/', HomeController::class);

$app->run();
```

Your Static Proxy classes:

```php
// app/Facades/View.php
class View extends StaticProxy
{
    public static function getInstanceIdentifier()
    {
        return 'view';
    }
}

// app/Facades/DB.php
class DB extends StaticProxy
{
    public static function getInstanceIdentifier()
    {
        return 'db';
    }
}
```

Your controller class:

```php
<?php

namespace App\Controllers;

class HomeController
{
    public function __invoke()
    {
        return View::render('home.index', [
            'articles' => DB::query('SELECT * FROM articles')
        ]);
    }
}
```

Pretty cool, huh? Some interesting things to note about this example is that we've actually hidden the fact that we are
using PDO and Twig from the controller. We could easily swap something else in that uses the same interfaces, and the
controller code would not need to be altered. All we would need to do is put different objects into the application
container. In fact, that is *exactly* how testing the controller would work. The test could be bootstrapped with mock or
stub objects put into the container.
