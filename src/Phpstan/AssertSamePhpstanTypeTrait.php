<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Phpstan;

trait AssertSamePhpstanTypeTrait
{
    /**
     * @param string $expectedPhpstanType compared against PHPStan\dumpType($v) result
     * @param mixed  $value
     */
    public static function assertSamePhpstanType(string $expectedPhpstanType, $value): void
    {
        PhpstanUtil::ignoreUnusedVariable($expectedPhpstanType);
        PhpstanUtil::ignoreUnusedVariable($value);

        static::assertTrue(true); // assertion is done by AssertSamePhpstanTypeRule phpstan rule
    }
}
