<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Mvorisek\Atk4\Hintable\Tests\Data\Model\IModelAddonFields;
use Mvorisek\Atk4\Hintable\Tests\Data\Model\ModelExtendsAndImplements;

class ExtendAndImplementsTest extends TestCase
{
    protected function createPersistence(): Persistence
    {
        $db = new \Atk4\Data\Persistence\Array_();
        $db->onHook(Persistence::HOOK_AFTER_ADD, function (Persistence $p, Model $m) {
            if (is_a($m, IModelAddonFields::class, true)) {
                $m->addField('field_in_interface');
            }
        });

        return $db;
    }

    public function testRefBasic(): void
    {
        $db = $this->createPersistence();

        $model = (new ModelExtendsAndImplements($db))->createEntity();
        $model->x = 'test';
        $model->refId = 2;
        $model->field_in_extended_class = 'test_extended';
        $model->field_in_interface = 'test';

        $this->assertSame('test', $model->get('x'));
        $this->assertSame(2, $model->get('refId'));
        $this->assertSame('test_extended', $model->get('field_in_extended_class'));
        $this->assertSame('test', $model->get('field_in_interface'));
    }
}
