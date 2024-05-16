<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Phpstan;

use Atk4\Core\Phpunit\TestCase;
use Mvorisek\Atk4\Hintable\Phpstan\PhpstanUtil;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;

class PhpstanUtilTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    #[DoesNotPerformAssertions]
    public function testAlwaysFalseAnalyseOnly(): void
    {
        if (PhpstanUtil::alwaysFalseAnalyseOnly()) {
            self::assertTrue(false); // @phpstan-ignore staticMethod.impossibleType
        }
    }

    public function testIgnoreUnusedVariable(): void
    {
        (static function (string $name): void { // ignore this line once phpstan emits an error for unused variable
            self::assertTrue(true); // @phpstan-ignore staticMethod.alreadyNarrowedType
        })('');

        (static function (string $name): void {
            PhpstanUtil::ignoreUnusedVariable($name);

            self::assertTrue(true); // @phpstan-ignore staticMethod.alreadyNarrowedType
        })('');
    }

    public function testFakeNeverReturn(): void
    {
        /**
         * @return never
         */
        $fx = static function () {
            PhpstanUtil::fakeNeverReturn();
        };

        $fxRes = PhpstanUtil::alwaysFalseAnalyseOnly() ? false : $fx();
        if (PhpstanUtil::alwaysFalseAnalyseOnly()) {
            self::assertFalse($fxRes); // @phpstan-ignore staticMethod.impossibleType
        }
        self::assertNull($fxRes); // @phpstan-ignore staticMethod.impossibleType
    }
}
