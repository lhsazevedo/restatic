<?php

namespace Tests\Fixture;

use ReStatic\StaticProxy;

/**
 * @method static \SplQueue getInstance()
 */
class QueueProxy extends StaticProxy
{
    public static function getInstanceIdentifier()
    {
        return 'queue';
    }
}
