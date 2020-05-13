<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Data;

use Mvorisek\Atk4\Hintable\Core\MagicAbstract;

class MagicModelField extends MagicAbstract
{
    /** @const string */
    public const TYPE_FIELD_NAME = 'field_n';

    protected function _atk__data__hintable_magic__getModel(): HintableModel
    {
        return $this->_atk__core__hintable_magic__class;
    }

    protected function _atk__data__hintable_magic__getModelPropDef(string $name): HintablePropertyDef
    {
        $model = $this->_atk__data__hintable_magic__getModel();

        $hProps = \Closure::bind(function () use ($model) {
            return $model->getHintableProps();
        }, null, HintableModel::class)();

        return $hProps[$name];
    }

    public function __get(string $name): string
    {
        if ($this->_atk__core__hintable_magic__type === self::TYPE_FIELD_NAME) {
            return $this->_atk__data__hintable_magic__getModelPropDef($name)->fieldName;
        }

        $this->_atk__core__hintable_magic__throwNotSupported();
    }
}
