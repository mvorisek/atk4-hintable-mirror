<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Data\Model;

/**
 * @property string $field_in_extended_class @Atk4\Field()
 */
class ModelExtendsAndImplements extends Simple implements IModelAddonFields
{
    protected function init(): void
    {
        parent::init();

        $this->addField('field_in_extended_class');
    }
}
