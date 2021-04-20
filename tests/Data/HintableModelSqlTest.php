<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data;

use Atk4\Data\Persistence;
use Atk4\Schema\Migration;

/**
 * @coversDefaultClass \Mvorisek\Atk4\Hintable\Data\HintableModelTrait
 */
class HintableModelSqlTest extends HintableModelArrayTest
{
    protected function createPersistence(): Persistence
    {
        $persistence = new Persistence\Sql('sqlite::memory:');

        (new Migration(new Model\Simple($persistence)))->dropIfExists()->create();
        (new Migration(new Model\Standard($persistence)))->dropIfExists()->create();

        return $persistence;
    }
}
