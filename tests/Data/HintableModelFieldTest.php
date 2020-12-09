<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data;

use Atk4\Core\AtkPhpunit;
use Atk4\Data\Exception;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Data\MagicModelField
 */
class HintableModelFieldTest extends AtkPhpunit\TestCase
{
    public function testFieldName(): void
    {
        $cl = new Model\Simple();
        $this->assertSame('x', $cl->fieldName()->x);

        // "y" property/field is not defined
        $this->expectException(Exception::class);
        $cl->fieldName()->y; // @phpstan-ignore-line
    }
}
