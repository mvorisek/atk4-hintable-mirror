<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use Atk4\Core\Exception;
use Atk4\Core\Phpunit\TestCase;
use Mvorisek\Atk4\Hintable\Core\Prop;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Core\MagicProp
 */
class PropTest extends TestCase
{
    public function testPropName(): void
    {
        $mock = new PropMock();
        $this->assertSame('pub', $mock->propName()->pub);
        $this->assertSame('priv', $mock->propName()->priv);
        $this->assertSame('undeclared', $mock->propName()->undeclared); // @phpstan-ignore-line

        $this->assertSame('_pub_', $mock->pub);
        $this->assertSame('_pub_', $mock->{$mock->propName()->pub});
    }

    public function testPropNameFull(): void
    {
        $mock = new PropMock();
        $this->assertSame(PropMock::class . '::pub', $mock->propNameFull()->pub);
        $this->assertSame(PropMock::class . '::priv', $mock->propNameFull()->priv);
        $this->assertSame(PropMock::class . '::undeclared', $mock->propNameFull()->undeclared); // @phpstan-ignore-line
        $this->assertSame(\stdClass::class . '::undeclared', Prop::propNameFull(\stdClass::class)->undeclared);
    }

    public function testMethodAccessException(): void
    {
        $mock = new PropMock();
        $this->expectException(Exception::class);
        $mock->propName()->undeclared(); // @phpstan-ignore-line
    }

    public function testPhpstanPropNameStringType(): void
    {
        $mock = new PropMock();
        $this->assertSame(21, $mock->pubInt);
        $this->assertIsString($mock->propName()->pubInt);
        $this->expectException(\TypeError::class);
        $this->assertSame('unused', chr($mock->propName()->pubInt)); // @phpstan-ignore-line
    }
}
