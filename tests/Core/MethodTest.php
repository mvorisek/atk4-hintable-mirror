<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use Atk4\Core\AtkPhpunit;
use Atk4\Core\Exception;
use Mvorisek\Atk4\Hintable\Core\Method;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Core\MagicMethod
 */
class MethodTest extends AtkPhpunit\TestCase
{
    public function testMethodName(): void
    {
        $cl = new MethodMock();
        $this->assertSame('pub', $cl->methodName()->pub());
        $this->assertSame('priv', $cl->methodName()->priv());
        $this->assertSame('undeclared', $cl->methodName()->undeclared()); // @phpstan-ignore-line
    }

    public function testMethodNameFull(): void
    {
        $cl = new MethodMock();
        $this->assertSame(MethodMock::class . '::pub', $cl->methodNameFull()->pub());
        $this->assertSame(MethodMock::class . '::priv', $cl->methodNameFull()->priv());
        $this->assertSame(MethodMock::class . '::undeclared', $cl->methodNameFull()->undeclared()); // @phpstan-ignore-line
        $this->assertSame(\stdClass::class . '::undeclared2', Method::methodNameFull(\stdClass::class)->undeclared2()); // @phpstan-ignore-line
    }

    public function testPropertyAccessException(): void
    {
        $cl = new MethodMock();
        $this->expectException(Exception::class);
        $cl->methodName()->unsupported; // @phpstan-ignore-line
    }

    public function testMethodClosure(): void
    {
        $cl = new MethodMock();
        $this->assertSame(MethodMock::class . '::pub', $cl->methodClosure()->pub()());
    }

    public function testMethodClosureStatic(): void
    {
        $cl = new MethodMock();

        // calling static method as instance method is valid in PHP
        // and also the only supported option by us
        $this->assertSame(MethodMock::class . '::pubStat', $cl->methodClosure()->pubStat()());

        $this->expectException(Exception::class);
        $this->assertSame(MethodMock::class . '::pubStat', $cl->methodClosure()::pubStat()()); // @phpstan-ignore-line
    }

    public function testMethodClosureProtected(): void
    {
        $cl = new MethodMock();
        $this->assertSame(MethodMock::class . '::priv', $cl->methodClosureProtected()->priv()());
        $this->assertSame(MethodMock::class . '::privStat', $cl->methodClosureProtected()->privStat()());
    }

    public function testMethodClosureAnonymous(): void
    {
        $cl = new class() extends \stdClass {
            private function privAnon(): string
            {
                return __METHOD__;
            }

            private static function privAnonStat(): string
            {
                return __METHOD__;
            }
        };

        $this->assertSame(get_class($cl) . '::privAnon', Method::methodClosureProtected($cl)->privAnon()()); // @phpstan-ignore-line
        $this->assertSame(get_class($cl) . '::privAnonStat', Method::methodClosureProtected($cl)->privAnonStat()()); // @phpstan-ignore-line
        $this->assertSame(get_class($cl) . '::privAnonStat', Method::methodClosureProtected(get_class($cl))->privAnonStat()()); // @phpstan-ignore-line
    }
}
