<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use Atk4\Core\AtkPhpunit;
use Atk4\Core\Exception;
use Mvorisek\Atk4\Hintable\Core\Prop;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Core\MagicProp
 */
class PropTest extends AtkPhpunit\TestCase
{
    public function testPropName(): void
    {
        $cl = new PropMock();
        $this->assertSame('pub', $cl->propName()->pub);
        $this->assertSame('priv', $cl->propName()->priv);
        $this->assertSame('undeclared', $cl->propName()->undeclared); // @phpstan-ignore-line

        $this->assertSame('_pub_', $cl->pub);
        $this->assertSame('_pub_', $cl->{$cl->propName()->pub});
    }

    public function testPropNameFull(): void
    {
        $cl = new PropMock();
        $this->assertSame(PropMock::class . '::pub', $cl->propNameFull()->pub);
        $this->assertSame(PropMock::class . '::priv', $cl->propNameFull()->priv);
        $this->assertSame(PropMock::class . '::undeclared', $cl->propNameFull()->undeclared); // @phpstan-ignore-line
        $this->assertSame(\stdClass::class . '::undeclared2', Prop::propNameFull(\stdClass::class)->undeclared2); // @phpstan-ignore-line
    }

    public function testMethodAccessException(): void
    {
        $cl = new PropMock();
        $this->expectException(Exception::class);
        $cl->propName()->unsupported(); // @phpstan-ignore-line
    }

    public function testPhpstanStringType(): void
    {
        $cl = new PropMock();
        $this->assertSame(21, $cl->pubInt);
        $this->expectException(\TypeError::class);
        $this->assertSame('unused', chr($cl->propName()->pubInt)); // @phpstan-ignore-line
    }
}
