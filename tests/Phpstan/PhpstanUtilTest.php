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
            $this->assertTrue(false);
        }
    }

    public function testUseVariable(): void
    {
        (function (string $name): void { // ignore this line once phpstan emits an error for unused variable
            $this->assertTrue(true);
        })('');

        (function (string $name): void {
            PhpstanUtil::ignoreUnusedVariable($name);

            $this->assertTrue(true);
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

        $this->assertNull(PhpstanUtil::alwaysFalseAnalyseOnly() ? false : $fx());
    }
}
