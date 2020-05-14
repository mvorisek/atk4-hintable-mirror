<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Data;

use atk4\data\Model;
use Mvorisek\Atk4\Hintable\Core\MagicAbstract;

/**
 * Model with hintable fields support thru magic properties.
 *
 * How to define a hintable field:
 *   1. Define model field no later than in init() like:
 *      <code>$m->addField('first_name');</code>
 *   2. Annotate model property in class phpdoc like:
 *      <code>@property string $first_name @Atk\Field()</code>
 *      - use "field_name" parameter to change the target field name, by default mapped to the same name
 *      - use "visibility" parameter to limit the visibility, valid values are:
 *        - "public"     = default, no access restrictions
 *        - "protected_set" = property can not be set outside the Model class
 *        - "protected"  = like protected property
 *      - regular class property MUST NOT be defined as there is no way to unset it when the class is created
 *        at least by "<code>ReflectionClass::newInstanceWithoutConstructor()</code>"
 *
 * Usecase - get/set field data:
 *   Simply use the magic property like a regular one, example:
 *   <code>$n = $m->first_name;</code>
 *   <code>$m->first_name = $n;</code>
 *
 * Usecase - get field definition:
 *   <code>$m->getField($m->prop()->first_name);</code>
 */
class HintableModel extends Model
{
    /** @var HintablePropertyDef[] */
    private $_hintableProps;

    /**
     * @var bool Enable validation if all fields are hintable after self::init() is called.
     *           Validation is always skipped if this class is not extended or if extended as anonymous class.
     */
    protected $requireAllFieldsHintable = true;

    /**
     * @return HintablePropertyDef[]
     */
    protected function createHintablePropsFromClassDoc(string $className): array
    {
        return HintablePropertyDef::createFromClassDoc($className);
    }

    /**
     * @return HintablePropertyDef[]
     */
    protected function getHintableProps(): array
    {
        if ($this->_hintableProps === null) {
            $cls = [];
            $cl = static::class;
            do {
                array_unshift($cls, $cl);
            } while ($cl = get_parent_class($cl));

            $defs = [];
            foreach ($cls as $cl) {
                $clDefs = $this->createHintablePropsFromClassDoc($cl);
                foreach ($clDefs as $clDef) {
                    // if property was defined in parent class already, simply override it
                    $defs[$clDef->name] = $clDef;
                }
            }

            // IMPORTANT: check if all hintable property are not set, otherwise the magic functions will not work!
            foreach ($cls as $cl) {
                \Closure::bind(function () use ($defs, $cl) {
                    foreach ($defs as $def) {
                        if (array_key_exists($def->name, get_object_vars($this)) && $def->name !== 'id') {
                            throw new Exception([
                                'Hintable properties must remain magical, they must be not defined in the code',
                                'property' => $def->name,
                                'class' => $cl,
                            ]);
                        }
                    }
                }, $this, $cl)();
            }

            $this->_hintableProps = $defs;
        }

        // check if all already declared fields has a hintable property
        // full check is done after self::init() when all fields are required to be present
        $this->checkRequireAllFieldsHintable(false);

        return $this->_hintableProps;
    }

    protected function checkRequireAllFieldsHintable(bool $requireAllHintableFields): void
    {
        // do not check if get_class($this) === this base class or if class is anonymous

        // @TODO
    }

    public function __isset(string $name): bool
    {
        $hProps = $this->getHintableProps();
        if (isset($hProps[$name])) {
            return true;
        }

        return isset($this->{$name}); // default behaviour
    }

    public function __get(string $name)
    {
        $hProps = $this->getHintableProps();
        if (isset($hProps[$name])) {
            $hProp = $hProps[$name];
            if ($hProp->refType === HintablePropertyDef::REF_TYPE_ONE || $hProp->refType === HintablePropertyDef::REF_TYPE_MANY) {
                return $this->ref($hProp->fieldName);
            }

            return $this->get($hProp->fieldName);
        }

        return $this->{$name}; // default behaviour
    }

    public function __set(string $name, $value): void
    {
        $hProps = $this->getHintableProps();
        if (isset($hProps[$name])) {
            // @TODO check visibility - also for __isset, __get, __unset
            // @TODO check value type

            $this->set($hProps[$name]->fieldName, $value);
        }

        $this->{$name} = $value; // default behaviour
    }

    public function __unset(string $name): void
    {
        $hProps = $this->getHintableProps();
        if (isset($hProps[$name])) {
            $this->setNull($hProps[$name]->fieldName);
        }

        unset($this->{$name}); // default behaviour
    }

    public function init(): void
    {
        parent::init();

        $this->checkRequireAllFieldsHintable(true);
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * only non-static hinting methods are supported.
     *
     * @return static
     */
    public static function hinting()
    {
        // @TODO this object should not support any modifications, ie. unset everything and prevent any calls except fieldName() and cache this class,
        // or better to allow to access
        return new class(static::class, '') extends MagicAbstract {
            public function __call(string $name, array $args)
            {
                if (in_array($name, ['fieldName'], true)) {
                    $cl = (new \ReflectionClass($this->_atk__core__hintable_magic__class))->newInstanceWithoutConstructor();

                    return $cl->{$name}();
                }

                $this->_atk__core__hintable_magic__throwNotSupported();
            }
        };
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any property returns its field name.
     *
     * @return static
     */
    public function fieldName()
    {
        $cl = MagicModelField::class;

        return new $cl($this, MagicModelField::TYPE_FIELD_NAME);
    }
}
