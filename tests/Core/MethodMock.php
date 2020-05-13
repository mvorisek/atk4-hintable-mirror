<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use Mvorisek\Atk4\Hintable\Core\MethodTrait;

class MethodMock
{
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

    protected function ignoreUnused(): string
    {
        return $this->priv() . "\n" . static::privStat();
    }
}
