<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Phpstan;

use Atk4\Core\Phpunit\TestCase;
use Mvorisek\Atk4\Hintable\Phpstan\PhpstanUtil;

class PhpstanUtilTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testAlwaysFalseAnalyseOnly(): void
    {
        if (PhpstanUtil::alwaysFalseAnalyseOnly()) {
            static::assertTrue(false); // @phpstan-ignore-line
        }
    }

    public function testUseVariable(): void
    {
        (function (string $name): void { // ignore this line once phpstan emits an error for unused variable
            static::assertTrue(true); // @phpstan-ignore-line
        })('');

        (function (string $name): void {
            PhpstanUtil::ignoreUnusedVariable($name);

            static::assertTrue(true); // @phpstan-ignore-line
        })('');
    }

    public function testFakeNeverReturn(): void
    {
        /**
         * @return never
         */
        $fx = function () {
            PhpstanUtil::fakeNeverReturn();
        };

        $fxRes = PhpstanUtil::alwaysFalseAnalyseOnly() ? false : $fx();
        if (PhpstanUtil::alwaysFalseAnalyseOnly()) {
            static::assertFalse($fxRes); // @phpstan-ignore-line
        }
        static::assertNull($fxRes); // @phpstan-ignore-line
    }
}
