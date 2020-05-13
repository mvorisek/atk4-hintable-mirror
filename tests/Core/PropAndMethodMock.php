<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use Mvorisek\Atk4\Hintable\Core\MethodTrait;
use Mvorisek\Atk4\Hintable\Core\PropTrait;

class PropAndMethodMock
{
    use PropTrait;
    use MethodTrait;

    private function priv()
    {
        return __METHOD__;
    }

    public function pub()
    {
        return __METHOD__;
    }

    private static function privStat()
    {
        return __METHOD__;
    }

    public static function pubStat()
    {
        return __METHOD__;
    }
}
