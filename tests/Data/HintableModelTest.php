<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data;

use atk4\core\AtkPhpunit;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Data\HintableModel
 */
class HintableModelTest extends AtkPhpunit\TestCase
{
    public function testFieldName()
    {
        $cl = new Model\Simple();
        $this->assertSame('x', $cl->fieldName()->x);
        $this->assertSame('x', Model\Simple::hinting()->fieldName()->x);

        $cl = new Model\Standard();
        $this->assertSame('x', $cl->fieldName()->x);
        $this->assertSame('yy', $cl->fieldName()->y);
        $this->assertSame('id', $cl->fieldName()->id);
        $this->assertSame('name', $cl->fieldName()->_name);
    }
}
