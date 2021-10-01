<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data;

use Atk4\Data\Persistence;
use Atk4\Data\Schema\Migration;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Data\HintableModelTrait
 */
class HintableModelSqlTest extends HintableModelArrayTest
{
    protected function createPersistence(): Persistence
    {
        $db = new Persistence\Sql('sqlite::memory:');

        (new Migration(new Model\Simple($db)))->create();
        (new Migration(new Model\Standard($db)))->create();

        return $db;
    }
}
