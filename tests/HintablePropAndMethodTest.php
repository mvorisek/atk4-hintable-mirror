<?php

declare(strict_types=1);

namespace atk4\core\tests;

use atk4\core\AtkPhpunit;
use atk4\core\Exception;
use atk4\core\Hintable\Method;
use atk4\core\Hintable\MethodTrait;
use atk4\core\Hintable\Prop;
use atk4\core\Hintable\PropTrait;

/**
 * @coversDefaultClass \atk4\core\Hintable\MagicAbstract
 */
class HintablePropAndMethodTest extends AtkPhpunit\TestCase
{
    public function testPropName()
    {
        $cl = new HintablePropAndMethodMock();
        $this->assertSame('x', $cl->propName()->x);
        $this->assertSame('y', $cl->propName()->y);
    }

    public function testPropNameFull()
    {
        $cl = new HintablePropAndMethodMock();
        $this->assertSame(HintablePropAndMethodMock::class . '::x', $cl->propNameFull()->x);
        $this->assertSame(HintablePropAndMethodMock::class . '::y', $cl->propNameFull()->y);
        $this->assertSame(\stdClass::class . '::z', Prop::propNameFull(\stdClass::class)->z);
    }

    public function testMethodName()
    {
        $cl = new HintablePropAndMethodMock();
        $this->assertSame('x', $cl->methodName()->x());
        $this->assertSame('y', $cl->methodName()->y());
    }

    public function testMethodNameFull()
    {
        $cl = new HintablePropAndMethodMock();
        $this->assertSame(HintablePropAndMethodMock::class . '::x', $cl->methodNameFull()->x());
        $this->assertSame(HintablePropAndMethodMock::class . '::y', $cl->methodNameFull()->y());
        $this->assertSame(\stdClass::class . '::z', Method::methodNameFull(\stdClass::class)->z());
    }

    public function testMethodClosure()
    {
        $cl = new HintablePropAndMethodMock();
        $this->assertSame(HintablePropAndMethodMock::class . '::pub', $cl->methodClosure()->pub()());
    }

    public function testMethodClosureStatic()
    {
        $cl = new HintablePropAndMethodMock();

        // calling static method as instance method is valid in PHP
        // and also the only supported option by us
        $this->assertSame(HintablePropAndMethodMock::class . '::pubStat', $cl->methodClosure()->pubStat()());

        $this->expectException(Exception::class);
        $this->assertSame(HintablePropAndMethodMock::class . '::pubStat', $cl->methodClosure()::pubStat()());
    }

    public function testMethodClosureProtected()
    {
        $cl = new HintablePropAndMethodMock();
        $this->assertSame(HintablePropAndMethodMock::class . '::priv', $cl->methodClosureProtected()->priv()());
        $this->assertSame(HintablePropAndMethodMock::class . '::privStat', $cl->methodClosureProtected()->privStat()());
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

// @codingStandardsIgnoreStart
class HintablePropAndMethodMock
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
// @codingStandardsIgnoreEnd
