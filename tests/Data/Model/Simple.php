<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data\Model;

use Mvorisek\Atk4\Hintable\Data\HintableModel;

/**
 * @property string $x @Atk\Field()
 */
class Simple extends HintableModel
{
    public function init(): void
    {
        parent::init();

        $this->addField('x', ['type' => 'string', 'required' => true]);
    }
}
