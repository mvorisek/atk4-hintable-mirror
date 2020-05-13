<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use atk4\core\AtkPhpunit;
use atk4\core\Exception;
use Mvorisek\Atk4\Hintable\Core\Method;
use Mvorisek\Atk4\Hintable\Core\Prop;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Core\MagicAbstract
 */
class PropAndMethodTest extends AtkPhpunit\TestCase
{
    public function testPropName()
    {
        $cl = new PropAndMethodMock();
        $this->assertSame('x', $cl->propName()->x);
        $this->assertSame('y', $cl->propName()->y);
    }

    public function testPropNameFull()
    {
        $cl = new PropAndMethodMock();
        $this->assertSame(PropAndMethodMock::class . '::x', $cl->propNameFull()->x);
        $this->assertSame(PropAndMethodMock::class . '::y', $cl->propNameFull()->y);
        $this->assertSame(\stdClass::class . '::z', Prop::propNameFull(\stdClass::class)->z);
    }

    public function testMethodName()
    {
        $cl = new PropAndMethodMock();
        $this->assertSame('x', $cl->methodName()->x());
        $this->assertSame('y', $cl->methodName()->y());
    }

    public function testMethodNameFull()
    {
        $cl = new PropAndMethodMock();
        $this->assertSame(PropAndMethodMock::class . '::x', $cl->methodNameFull()->x());
        $this->assertSame(PropAndMethodMock::class . '::y', $cl->methodNameFull()->y());
        $this->assertSame(\stdClass::class . '::z', Method::methodNameFull(\stdClass::class)->z());
    }

    public function testMethodClosure()
    {
        $cl = new PropAndMethodMock();
        $this->assertSame(PropAndMethodMock::class . '::pub', $cl->methodClosure()->pub()());
    }

    public function testMethodClosureStatic()
    {
        $cl = new PropAndMethodMock();

        // calling static method as instance method is valid in PHP
        // and also the only supported option by us
        $this->assertSame(PropAndMethodMock::class . '::pubStat', $cl->methodClosure()->pubStat()());

        $this->expectException(Exception::class);
        $this->assertSame(PropAndMethodMock::class . '::pubStat', $cl->methodClosure()::pubStat()());
    }

    public function testMethodClosureProtected()
    {
        $cl = new PropAndMethodMock();
        $this->assertSame(PropAndMethodMock::class . '::priv', $cl->methodClosureProtected()->priv()());
        $this->assertSame(PropAndMethodMock::class . '::privStat', $cl->methodClosureProtected()->privStat()());
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
