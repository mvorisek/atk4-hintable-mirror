<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data;

use Atk4\Core\AtkPhpunit;
use Atk4\Data\Exception;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Data\HintableModelTrait
 */
class HintableModelTest extends AtkPhpunit\TestCase
{
    public function testFieldName(): void
    {
        $cl = new Model\Simple();
        $this->assertSame('simple', $cl->table);
        $this->assertSame('x', $cl->fieldName()->x);
        $this->assertSame('x', Model\Simple::hinting()->fieldName()->x);

        $cl = new Model\Standard();
        $this->assertSame('prefix_standard', $cl->table);
        $this->assertSame('x', $cl->fieldName()->x);
        $this->assertSame('yy', $cl->fieldName()->y);
        $this->assertSame('id', $cl->fieldName()->id);
        $this->assertSame('name', $cl->fieldName()->_name);
        $this->assertSame('simpleOne', $cl->fieldName()->simpleOne);
        $this->assertSame('simpleMany', $cl->fieldName()->simpleMany);
    }

    public function testFieldNameUndeclaredException(): void
    {
        $cl = new Model\Simple();
        $this->expectException(Exception::class);
        $cl->fieldName()->y; // @phpstan-ignore-line
    }

    protected function createDatabaseForRefTest(): \Atk4\Data\Persistence
    {
        $db = new \Atk4\Data\Persistence\Array_();

        $simpleA = (new Model\Simple($db))
            ->set(Model\Simple::hinting()->fieldName()->x, 'a')
            ->save();
        $simpleB1 = (new Model\Simple($db))
            ->set(Model\Simple::hinting()->fieldName()->x, 'b1')
            ->save();
        $simpleB2 = (new Model\Simple($db))
            ->set(Model\Simple::hinting()->fieldName()->x, 'b2')
            ->save();

        $standardTemplate = (new Model\Standard($db))
            ->set(Model\Standard::hinting()->fieldName()->x, 'xx')
            ->set(Model\Standard::hinting()->fieldName()->y, 'yy')
            ->set(Model\Standard::hinting()->fieldName()->_name, 'zz')
            ->set(Model\Standard::hinting()->fieldName()->dtImmutable, new \DateTime('2000-1-1 12:00:00'))
            ->set(Model\Standard::hinting()->fieldName()->dtInterface, new \DateTimeImmutable('2000-2-1 12:00:00'))
            ->set(Model\Standard::hinting()->fieldName()->dtMulti, new \DateTimeImmutable('2000-3-1 12:00:00'));
        $standardA = (clone $standardTemplate)
            ->set(Model\Standard::hinting()->fieldName()->simpleOneId, $simpleA->id)
            ->save();
        $standardB = (clone $standardTemplate)
            ->set(Model\Standard::hinting()->fieldName()->simpleOneId, $simpleB2->id)
            ->save();

        $simpleA
            ->set($simpleA->fieldName()->refId, $standardA->id)
            ->save();
        $simpleB1
            ->set($simpleA->fieldName()->refId, $standardB->id)
            ->save();
        $simpleB2
            ->set($simpleA->fieldName()->refId, $standardB->id)
            ->save();

        return $db;
    }

    public function testRefOneGetter(): void
    {
        $cl = new Model\Standard();
        $cl->invokeInit();
        $this->assertInstanceOf(Model\Simple::class, $cl->simpleOne);

        $db = $this->createDatabaseForRefTest();

        $cl = new Model\Simple($db);
        $this->assertSame(2, (clone $cl)->load(2)->ref->id);
        $this->assertSame(2, (clone $cl)->load(3)->ref->id);

        $cl = new Model\Standard($db);
        $this->assertSame(1, $cl->simpleOne->loadAny()->id);
        $this->assertSame(3, $cl->load(2)->simpleOne->id);
    }

    public function testRefManyGetter(): void
    {
        // TODO seems like a bug in atk4/data
//        $cl = new Model\Standard();
//        $cl->invokeInit();
//        $this->assertInstanceOf(Model\Simple::class, $cl->simpleMany);

        $db = $this->createDatabaseForRefTest();

        $cl = new Model\Standard($db);
        $this->assertSame([2 => 2, 3 => 3], array_map(function (Model\Simple $model) {
            return $model->id;
        }, iterator_to_array($cl->load(2)->simpleMany)));
    }

    public function testRefManyGetterDirectLoadException(): void
    {
        $db = $this->createDatabaseForRefTest();
        $cl = new Model\Standard($db);
        $this->expectException(Exception::class);
        $cl->simpleMany->loadAny();
    }
}
