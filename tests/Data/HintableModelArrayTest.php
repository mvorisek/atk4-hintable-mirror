<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Exception;
use Atk4\Data\Model as AtkModel;
use Atk4\Data\Persistence;
use Mvorisek\Atk4\Hintable\Phpstan\PhpstanUtil;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Data\HintableModelTrait
 */
class HintableModelArrayTest extends TestCase
{
    protected function createPersistence(): Persistence
    {
        return new Persistence\Array_();
    }

    protected function createDatabaseForRefTest(): Persistence
    {
        $db = $this->createPersistence();

        $db->atomic(function () use ($db) {
            $simple1 = (new Model\Simple($db))->createEntity()
                ->set(Model\Simple::hinting()->fieldName()->x, 'a')
                ->save();
            $simple2 = (new Model\Simple($db))->createEntity()
                ->set(Model\Simple::hinting()->fieldName()->x, 'b1')
                ->save();
            $simple3 = (new Model\Simple($db))->createEntity()
                ->set(Model\Simple::hinting()->fieldName()->x, 'b2')
                ->save();

            $standardTemplate = (new Model\Standard($db))->createEntity()
                ->set(Model\Standard::hinting()->fieldName()->x, 'xx')
                ->set(Model\Standard::hinting()->fieldName()->y, 'yy')
                ->set(Model\Standard::hinting()->fieldName()->_name, 'zz')
                ->set(Model\Standard::hinting()->fieldName()->dtImmutable, new \DateTime('2000-1-1 12:00:00 GMT'))
                ->set(Model\Standard::hinting()->fieldName()->dtInterface, new \DateTimeImmutable('2000-2-1 12:00:00 GMT'))
                ->set(Model\Standard::hinting()->fieldName()->dtMulti, new \DateTimeImmutable('2000-3-1 12:00:00 GMT'));
            for ($i = 0; $i < 10; ++$i) {
                (clone $standardTemplate)->save()->delete();
            }
            $standard11 = (clone $standardTemplate)
                ->set(Model\Standard::hinting()->fieldName()->simpleOneId, $simple1->id)
                ->save();
            $standard12 = (clone $standardTemplate)
                ->set(Model\Standard::hinting()->fieldName()->simpleOneId, $simple3->id)
                ->save();
            /* 13 - null simpleOneId */ (clone $standardTemplate)
                ->save();
            /* 14 - invalid simpleOneId */ (clone $standardTemplate)
                ->set(Model\Standard::hinting()->fieldName()->simpleOneId, 999)
                ->save();

            $simple1
                ->set(Model\Simple::hinting()->fieldName()->refId, $standard11->id)
                ->save();
            $simple2
                ->set(Model\Simple::hinting()->fieldName()->refId, $standard12->id)
                ->save();
            $simple3
                ->set(Model\Simple::hinting()->fieldName()->refId, $standard12->id)
                ->save();
        });

        return $db;
    }

    public function testRefBasic(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Simple($db);

        $entity = $model->load(1);
        static::assertSame(1, $entity->getId());
        static::assertSame(1, $entity->id);
        static::assertSame('a', $entity->x);
        static::assertSame(11, $entity->refId);
        static::assertSame(11, $entity->ref->id);

        $entity = $model->load(2);
        static::assertSame('b1', $entity->x);
        static::assertSame(12, $entity->ref->id);

        $entity = $model->load(3);
        static::assertSame('b2', $entity->x);
        static::assertSame(12, $entity->ref->id);

        static::assertNull($model->tryLoad(4));
    }

    public function testRefWithoutPersistence(): void
    {
        $model = new Model\Standard();
        $model->invokeInit();

        static::assertInstanceOf(Model\Simple::class, $model->simpleOne);

        // TODO atk4/data does not support traversing 1:N reference without persistence
        // $this->assertInstanceOf(Model\Simple::class, $model->simpleMany);
    }

    /**
     * @param array<int> $expectedIds
     */
    protected static function assertModelIds(array $expectedIds, AtkModel $model): void
    {
        $resAssoc = array_map(function (AtkModel $model) {
            return $model->id;
        }, iterator_to_array((clone $model)->setOrder($model->idField, 'asc')));

        static::assertSame(array_values($resAssoc), array_keys($resAssoc));
        static::assertSame(array_values($resAssoc), $expectedIds);
    }

    public function testRefOne(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Standard($db);

        static::assertInstanceOf(Model\Simple::class, $model->simpleOne);
        static::assertInstanceOf(Model\Simple::class, $model->load(11)->simpleOne);
        static::assertSame(1, $model->load(11)->simpleOne->id);
        static::assertSame('a', $model->load(11)->simpleOne->x);
        static::assertSame(3, $model->load(12)->simpleOne->id);
        static::assertSame('b2', $model->load(12)->simpleOne->x);
        static::assertSame(3, $model->load(12)->simpleOne->getModel()->loadOne()->id);
        $simpleXName = $model->simpleOne->fieldName()->x;
        static::assertSame('b2', $model->load(12)->simpleOne->getModel()->loadBy($simpleXName, 'b2')->x);

        if ($db instanceof Persistence\Array_) { // TODO https://github.com/atk4/data/issues/997
            static::assertModelIds([1, 2, 3], $model->simpleOne);
        } else {
            static::assertModelIds([1, 3], $model->simpleOne);
        }
        static::assertModelIds([1], $model->load(11)->simpleOne->getModel());
        static::assertModelIds([3], $model->load(12)->simpleOne->getModel());
        static::assertSame(3, $model->load(12)->simpleOne->getModel()->loadBy($simpleXName, 'b2')->id);
        static::assertNull($model->load(11)->simpleOne->getModel()->tryLoadBy($simpleXName, 'b2'));
        static::assertModelIds([3], $model->load(12)->simpleOne->getModel()->loadBy($simpleXName, 'b2')->getModel());
    }

    public function testRefMany(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Standard($db);

        static::assertInstanceOf(Model\Simple::class, $model->simpleMany);
        static::assertInstanceOf(Model\Simple::class, $model->load(11)->simpleMany);
        static::assertSame(1, $model->load(11)->simpleMany->loadOne()->id);
        static::assertSame('a', $model->load(11)->simpleMany->loadOne()->x);
        static::assertSame(2, $model->load(12)->simpleMany->load(2)->id);
        static::assertSame('b1', $model->load(12)->simpleMany->load(2)->x);
        static::assertSame(3, $model->load(12)->simpleMany->load(3)->id);
        static::assertSame('b2', $model->load(12)->simpleMany->load(3)->x);

        static::assertModelIds([1, 2, 3], $model->simpleMany);
        static::assertModelIds([1], $model->load(11)->simpleMany);
        static::assertModelIds([2, 3], $model->load(12)->simpleMany);
        $simpleXName = $model->simpleMany->fieldName()->x;
        static::assertSame(3, $model->load(12)->simpleMany->loadBy($simpleXName, 'b2')->id);
        static::assertNull($model->load(11)->simpleMany->tryLoadBy($simpleXName, 'b2'));
        static::assertModelIds([2, 3], $model->load(12)->simpleMany->loadBy($simpleXName, 'b2')->getModel());
    }

    public function testRefOneLoadOneException(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Standard($db);
        $modelSimple = $model->simpleOne;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('more than one record can be loaded');
        $modelSimple->loadOne();
    }

    public function testRefManyLoadOneException(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Standard($db);
        $modelSimple = $model->simpleMany;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('more than one record can be loaded');
        $modelSimple->loadOne();
    }

    public function testRefOneTraverseNull(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Standard($db);

        $entity13 = $model->load(13);
        static::assertNull($entity13->simpleOne);

        $entityNull = $model->createEntity();
        static::assertNull($entityNull->simpleOne);
    }

    public function testRefOneTraverseInvalidException(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Standard($db);
        $entity14 = $model->load(14);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No record was found');
        PhpstanUtil::ignoreUnusedVariable($entity14->simpleOne);
    }

    public function testRefOneReverseTraverseNullException(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Standard($db);
        $entityNull = $model->createEntity();

        static::assertNull($entityNull->simpleOne);

        $model->getReference($model->fieldName()->simpleOne)
            ->setDefaults(['ourField' => $model->fieldName()->id]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to traverse on null value');
        PhpstanUtil::ignoreUnusedVariable($entityNull->simpleMany);
    }

    public function testRefManyTraverseNullException(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Standard($db);
        $entityNull = $model->createEntity();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to traverse on null value');
        PhpstanUtil::ignoreUnusedVariable($entityNull->simpleMany);
    }

    public function testPhpstanModelIteratorAggregate(): void
    {
        $db = $this->createDatabaseForRefTest();
        $model = new Model\Simple($db);

        static::assertIsString($model->loadAny()->x);
        foreach ($model as $modelItem) {
            static::assertIsString($modelItem->x);
        }
    }
}
