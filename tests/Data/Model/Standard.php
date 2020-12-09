<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data\Model;

use Atk4\Data\Model;
use Mvorisek\Atk4\Hintable\Data\HintableModelTrait;

/**
 * @property string                       $x           @Atk\Field()
 * @property string                       $y           @Atk\Field(field_name="yy")
 * @property int                          $id          @Atk\Field() Property Model::id is defined, but it represents the ID field
 * @property string                       $_name       @Atk\Field(field_name="name") Property Model::name is defined, so we need to use different property name
 * @property \DateTimeImmutable           $dtImmutable @Atk\Field()
 * @property \DateTimeInterface           $dtInterface @Atk\Field()
 * @property \DateTime|\DateTimeImmutable $dtMulti     @Atk\Field()
 */
class Standard extends Model
{
    use HintableModelTrait;

    protected function init(): void
    {
        parent::init();

        $this->getField($this->fieldName()->id)->type = 'integer';
        $this->getField($this->fieldName()->id)->required = true;

        $this->addField($this->fieldName()->x, ['type' => 'string', 'required' => true]);
        $this->addField($this->fieldName()->y, ['type' => 'string', 'required' => true]);
        $this->addField($this->fieldName()->_name, ['type' => 'string', 'required' => true]);

        $this->addField($this->fieldName()->dtImmutable, ['type' => 'datetime', 'required' => true]);
        $this->addField($this->fieldName()->dtInterface, ['type' => 'datetime', 'required' => true]);
        $this->addField($this->fieldName()->dtMulti, ['type' => 'datetime', 'required' => true]);
    }
}
