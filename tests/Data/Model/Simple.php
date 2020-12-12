<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data\Model;

use Atk4\Data\Model;

/**
 * @property string   $x     @Atk\Field()
 * @property int      $refId @Atk\Field()
 * @property Standard $ref   @Atk\RefOne()
 */
class Simple extends Model
{
    public $table = 'simple'; // @phpstan-ignore-line issue with phpstan

    protected function init(): void
    {
        parent::init();

        $this->addField('x', ['type' => 'string', 'required' => true]);

        $this->addField($this->fieldName()->refId, ['type' => 'integer']);
        $this->hasOne($this->fieldName()->ref, ['model' => [Standard::class], 'our_field' => $this->fieldName()->refId]);
    }
}
