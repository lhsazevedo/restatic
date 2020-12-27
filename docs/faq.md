# FAQ

> Facades? Static Proxies? Isn't using static methods considered a bad practice?

Using static methods and classes makes your code harder to test. This is because your code becomes tightly coupled to
the class being referenced statically, and mocking static methods for unit tests is difficult. For this and other
reasons, using static methods is generally discouraged by object-oriented programming (OOP) experts. Generally,
techniques involving design patterns like *Service Locator* and *Dependency Injection* (DI) are preferred for managing
object dependencies and composition.

> But... using static methods is really easy.

True, and PHP developers that prefer frameworks like CodeIgniter, Laravel, Kohana, and FuelPHP are very accustomed to
using static methods in their application development. In some cases, it is an encouraged practice among these
communities, who argue that it makes the code more readable and contributes to *Rapid Application Development* (RAD).

> So, is there any kind of compromise?

Yep! Laravel 4 has a concept called "facades" (Note: This is not the same as the [Facade design
pattern](http://en.wikipedia.org/wiki/Facade_pattern)). These act as a static interface, or proxy, to an actual object
instance stored in a service container. The static proxy is linked to the container using a few tricks, including
defining class aliases via PHP's `class_alias()` function, and the use of the magic `__callStatic()` method. We can
thank [Taylor Otwell](https://twitter.com/taylorotwell) for developing this technique.

> OK, then what is the point of ReStatic?

ReStatic uses the same technique as Laravel's "facades" system, but provides two additional, but important, features:

1. **It works with any framework's service container** - ReStatic relies on the `ContainerInterface` of the
   [PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md) standard. You can use the [Acclimate
   library](https://github.com/jeremeamia/acclimate-container) to adapt any third-party containers to the normalized
   container interface that ReStatic depends on.
2. **It works within any namespace** - ReStatic injects an autoloader onto the stack, so no matter what namespace or
   scope you try to reference your aliased static proxy from, it will pass through the ReStatic autoloader. You can
   configure ReStatic to create the aliases in the global namespace, the current namespace, or a specific namespace.