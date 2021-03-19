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
        $this->assertSame(\stdClass::class . '::undeclared2', Prop::propNameFull(\stdClass::class)->undeclared2); // @phpstan-ignore-line
    }

    public function testMethodAccessException(): void
    {
        $mock = new PropMock();
        $this->expectException(Exception::class);
        $mock->propName()->unsupported(); // @phpstan-ignore-line
    }

    public function testPhpstanStringType(): void
    {
        $mock = new PropMock();
        $this->assertSame(21, $mock->pubInt);
        $this->expectException(\TypeError::class);
        \PHPStan\dumpType(\Atk4\Data\Model::hinting());
        \PHPStan\dumpType(\Atk4\Data\Model::hinting()->fieldName());
        \PHPStan\dumpType($mock->propName());
        \PHPStan\dumpType($mock->propName()->pubInt);
        $this->assertSame('unused', chr($mock->propName()->pubInt)); // @phpstan-ignore-line
    }
}
