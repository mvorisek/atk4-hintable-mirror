<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data\Model;

use Atk4\Data\Model;
use Mvorisek\Atk4\Hintable\Data\HintableModelTrait;

/**
 * @property string $x @Atk\Field()
 */
class Simple extends Model
{
    use HintableModelTrait;

    protected function init(): void
    {
        parent::init();

        $this->addField('x', ['type' => 'string', 'required' => true]);
    }
}
