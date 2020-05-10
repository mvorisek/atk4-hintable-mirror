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
 * @coversDefaultClass \atk4\core\Hintable\MagicPropAndMethod
 */
class HintablePropAndMethodTest extends AtkPhpunit\TestCase
{
    public function testProp()
    {
        $cl = new HintablePropAndMethodMock();
        $this->assertSame('x', $cl->prop()->x);
        $this->assertSame('y', $cl->prop()->y);
    }

    public function testPropFull()
    {
        $cl = new HintablePropAndMethodMock();
        $this->assertSame(HintablePropAndMethodMock::class . '::x', $cl->propFull()->x);
        $this->assertSame(HintablePropAndMethodMock::class . '::y', $cl->propFull()->y);
        $this->assertSame(\stdClass::class . '::z', Prop::propFull(\stdClass::class)->z);
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

        // private method in anonymous class
        $cl = new class() extends \stdClass {
            private function privAnon()
            {
                return __METHOD__;
            }
        };
        $this->assertSame(\stdClass::class . '::privAnon', $cl->methodClosureProtected()->privAnon()());

        // anonymous class passed by its anonymous/generated string name
        $this->assertSame(\stdClass::class . '::privAnon', Method::methodClosureProtected(get_class($cl))->privAnon());
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

    public static function pubstat()
    {
        return __METHOD__;
    }
}
// @codingStandardsIgnoreEnd
