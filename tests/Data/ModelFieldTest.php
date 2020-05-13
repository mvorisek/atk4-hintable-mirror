<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data;

use atk4\core\AtkPhpunit;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Core\Prop
 */
class ModelFieldTest extends AtkPhpunit\TestCase
{
    public function testFieldName()
    {
        $cl = new Model\Simple();
        $this->assertSame('x', $cl->fieldName()->x);

        // "y" property/field is not defined
        $this->expectException(\Throwable::class);
        $this->assertSame('y', @$cl->fieldName()->y);
    }
}
