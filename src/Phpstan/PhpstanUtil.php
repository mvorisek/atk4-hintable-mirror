<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Phpstan;

class PhpstanUtil
{
    /**
     * @return bool
     */
    final public static function alwaysFalseAnalyseOnly()
    {
        return false;
    }

    /**
     * @param mixed $value
     */
    final public static function ignoreUnusedVariable($value): void
    {
        if (self::alwaysFalseAnalyseOnly()) {
            assert($value !== null);
        }
    }

    /**
     * @return never
     */
    final public static function fakeNeverReturn(): void
    {
    }
}
