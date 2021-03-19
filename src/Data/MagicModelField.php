<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Data;

use Atk4\Data\Exception;
use Atk4\Data\Model;
use Mvorisek\Atk4\Hintable\Core\MagicAbstract;

/**
 * @template TTargetClass of object
 * @template TReturnType
 * @extends MagicAbstract<TTargetClass&Model, TReturnType>
 */
class MagicModelField extends MagicAbstract
{
    /** @const string */
    public const TYPE_FIELD_NAME = 'field_n';

    /**
     * @return array{Model, string}
     */
    protected function _atk__data__hintable_magic__getModelWithHintableTraitClass(): array
    {
        $model = $this->_atk__core__hintable_magic__class;

        $classUsesFunc = static function (string $class, string $needleTrait) use (&$classUsesFunc): bool {
            foreach (class_uses($class) as $trait) {
                if ($trait === $needleTrait || $classUsesFunc($trait, $needleTrait)) {
                    return true;
                }
            }

            return false;
        };

        $hintableTraitClass = null;
        $cl = get_class($model);
        do {
            if ($classUsesFunc($cl, HintableModelTrait::class)) {
                $hintableTraitClass = $cl;

                break;
            }
        } while ($cl = get_parent_class($cl));

        if ($hintableTraitClass === null) {
            throw (new Exception('Model does not use hintable model trait'))
                ->addMoreInfo('class', get_class($model));
        }

        return [$model, $hintableTraitClass];
    }

    protected function _atk__data__hintable_magic__getModelPropDef(string $name): HintablePropertyDef
    {
        [$model, $hintableTraitClass] = $this->_atk__data__hintable_magic__getModelWithHintableTraitClass();

        $hProps = \Closure::bind(function () use ($model): array {
            return $model->getHintableProps(); // @phpstan-ignore-line
        }, null, $hintableTraitClass)();

        if (!isset($hProps[$name])) {
            throw (new Exception('Hintable property is not defined'))
                ->addMoreInfo('property', $name)
                ->addMoreInfo('class', get_class($model));
        }

        return $hProps[$name];
    }

    public function __get(string $name): string
    {
        if ($this->_atk__core__hintable_magic__type === self::TYPE_FIELD_NAME) {
            return $this->_atk__data__hintable_magic__getModelPropDef($name)->fieldName;
        }

        throw $this->_atk__core__hintable_magic__createNotSupportedException();
    }
}
