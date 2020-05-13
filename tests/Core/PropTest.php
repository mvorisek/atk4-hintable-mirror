<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use atk4\core\AtkPhpunit;
use atk4\core\Exception;
use Mvorisek\Atk4\Hintable\Core\Prop;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Core\MagicProp
 */
class PropTest extends AtkPhpunit\TestCase
{
    public function testPropName()
    {
        $cl = new PropMock();
        $this->assertSame('x', $cl->propName()->x);
        $this->assertSame('y', $cl->propName()->y);
        $this->assertSame('z', $cl->propName()->z);

        $this->assertSame('xx', $cl->{$cl->propName()->x});
    }

    public function testPropNameFull()
    {
        $cl = new PropMock();
        $this->assertSame(PropMock::class . '::x', $cl->propNameFull()->x);
        $this->assertSame(PropMock::class . '::y', $cl->propNameFull()->y);
        $this->assertSame(\stdClass::class . '::z', Prop::propNameFull(\stdClass::class)->z);
    }

    public function testCallError()
    {
        $cl = new PropMock();
        $this->expectException(Exception::class);
        $cl->propName()->unsupported();
    }
}
