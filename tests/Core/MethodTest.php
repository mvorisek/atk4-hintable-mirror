<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use atk4\core\AtkPhpunit;
use atk4\core\Exception;
use Mvorisek\Atk4\Hintable\Core\Method;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Core\MagicMethod
 */
class MethodTest extends AtkPhpunit\TestCase
{
    public function testMethodName()
    {
        $cl = new MethodMock();
        $this->assertSame('x', $cl->methodName()->x());
        $this->assertSame('y', $cl->methodName()->y());
    }

    public function testMethodNameFull()
    {
        $cl = new MethodMock();
        $this->assertSame(MethodMock::class . '::x', $cl->methodNameFull()->x());
        $this->assertSame(MethodMock::class . '::y', $cl->methodNameFull()->y());
        $this->assertSame(\stdClass::class . '::z', Method::methodNameFull(\stdClass::class)->z());
    }

    public function testGetterError()
    {
        $cl = new MethodMock();
        $this->expectException(Exception::class);
        gettype($cl->methodName()->unsupported);
    }

    public function testMethodClosure()
    {
        $cl = new MethodMock();
        $this->assertSame(MethodMock::class . '::pub', $cl->methodClosure()->pub()());
    }

    public function testMethodClosureStatic()
    {
        $cl = new MethodMock();

        // calling static method as instance method is valid in PHP
        // and also the only supported option by us
        $this->assertSame(MethodMock::class . '::pubStat', $cl->methodClosure()->pubStat()());

        $this->expectException(Exception::class);
        $this->assertSame(MethodMock::class . '::pubStat', $cl->methodClosure()::pubStat()());
    }

    public function testMethodClosureProtected()
    {
        $cl = new MethodMock();
        $this->assertSame(MethodMock::class . '::priv', $cl->methodClosureProtected()->priv()());
        $this->assertSame(MethodMock::class . '::privStat', $cl->methodClosureProtected()->privStat()());
    }

    public function testMethodClosureAnonymous()
    {
        $cl = new class() extends \stdClass {
            private function privAnon()
            {
                return __METHOD__;
            }

            private static function privAnonStat()
            {
                return __METHOD__;
            }
        };

        $this->assertSame(get_class($cl) . '::privAnon', Method::methodClosureProtected($cl)->privAnon()());

        $this->assertSame(get_class($cl) . '::privAnonStat', Method::methodClosureProtected($cl)->privAnonStat()());
        $this->assertSame(get_class($cl) . '::privAnonStat', Method::methodClosureProtected(get_class($cl))->privAnonStat()());
    }
}
