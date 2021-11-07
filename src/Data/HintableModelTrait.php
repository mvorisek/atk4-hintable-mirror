<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Data;

use Atk4\Data\Exception;
use Atk4\Data\Model;
use Mvorisek\Atk4\Hintable\Core\MagicAbstract;

/**
 * Adds hintable fields support to Model thru magic properties.
 *
 * How to define a hintable field:
 *   1. Define model field no later than in Model::init() like:
 *      <code>$m->addField('firstName');</code>
 *   2. Annotate model property in class phpdoc like:
 *      <code>@property string $firstName @Atk4\Field()</code>
 *      - use "field_name" parameter to change the target field name, by default mapped to the same name
 *      - use "visibility" parameter to limit the visibility, valid values are:
 *        - "public"        = default, no access restrictions
 *        - "protected_set" = property cannot be set outside the Model class
 *        - "protected"     = like protected property
 *      - regular class property MUST NOT be defined as there is no way to unset it when the class is created
 *        at least by "<code>ReflectionClass::newInstanceWithoutConstructor()</code>"
 *
 * Usecase - get/set field data:
 *   Simply use the magic property like a regular one, example:
 *   <code>$n = $m->firstName;</code>
 *   <code>$m->firstName = $n;</code>
 *
 * Usecase - get field name/definition:
 *   <code>$m->fieldName()->firstName;</code>
 *   <code>$m->getField($m->fieldName()->firstName);</code>
 */
trait HintableModelTrait
{
    /** @var HintablePropertyDef[] */
    private $_hintableProps;

    /**
     * @var bool Enable validation if all fields are hintable after self::init() is called.
     *           Validation is always skipped if this class is not extended or if extended as anonymous class.
     */
    protected $requireAllFieldsHintable = true;

    /**
     * @param class-string<Model> $className
     *
     * @return HintablePropertyDef[]
     */
    protected function createHintablePropsFromClassDoc(string $className): array
    {
        $this->assertIsModel();

        return HintablePropertyDef::createFromClassDoc($className);
    }

    /**
     * @return HintablePropertyDef[]
     */
    protected function getHintableProps(): array
    {
        $this->assertIsModel();

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
                \Closure::bind(function () use ($defs, $cl): void {
                    foreach ($defs as $def) {
                        if (array_key_exists($def->name, get_object_vars($this))) {
                            throw (new Exception('Hintable properties must remain magical, they must be not defined in the code'))
                                ->addMoreInfo('property', $def->name)
                                ->addMoreInfo('class', $cl);
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
        $this->assertIsModel();

        // do not check if get_class($this) === this base class or if class is anonymous

        // also test if ref type is matching the field/ref type

        // @TODO
    }

    public function __isset(string $name): bool
    {
        $hProps = $this->getModel(true)->getHintableProps();
        if (isset($hProps[$name])) {
            return true;
        }

        // default behaviour
        return isset($this->{$name});
    }

    /**
     * @return mixed
     */
    public function &__get(string $name)
    {
        $hProps = $this->getModel(true)->getHintableProps();
        if (isset($hProps[$name])) {
            $hProp = $hProps[$name];
            if ($hProp->refType !== HintablePropertyDef::REF_TYPE_NONE) {
                /** @var Model */
                $model = $this->ref($hProp->fieldName);

                if ($hProp->refType === HintablePropertyDef::REF_TYPE_ONE) {
                    // TODO this requires checking all parents!
//                    // ensure no more than one record can load
//                    // TOOD this is quite dirty, as extra query to DB is sent
//                    $model->onHookShort(Model::HOOK_AFTER_LOAD, \Closure::bind(function () {
//                        (clone $this)->loadOne();
//                    }, $model, Model::class), [], -11);

                    return $model;
                } elseif ($hProp->refType === HintablePropertyDef::REF_TYPE_MANY) {
                    return $model;
                }
            }

            $resNoRef = $this->get($hProp->fieldName);

            return $resNoRef;
        }

        // default behaviour
        return $this->{$name};
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $hProps = $this->getModel(true)->getHintableProps();
        if (isset($hProps[$name])) {
            // @TODO check visibility - also for __isset, __get, __unset
            // @TODO check value type

            $this->set($hProps[$name]->fieldName, $value);

            return;
        }

        // default behaviour
        $this->{$name} = $value;
    }

    public function __unset(string $name): void
    {
        $hProps = $this->getModel(true)->getHintableProps();
        if (isset($hProps[$name])) {
            $this->setNull($hProps[$name]->fieldName);

            return;
        }

        // default behaviour
        unset($this->{$name});
    }

    // TODO we can check once initialized (init was called for the 1st time), but not sooner,
    // otherwise init cannot be overridden
//    protected function init(): void
//    {
//        parent::init();
//
//        $this->checkRequireAllFieldsHintable(true);
//    }

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
        // @phpstan-ignore-next-line
        return new class(static::class, '') extends MagicAbstract {
            public function __call(string $name, array $args)
            {
                if (in_array($name, ['fieldName'], true)) {
                    $cl = (new \ReflectionClass($this->_atk__core__hintable_magic__class))->newInstanceWithoutConstructor();

                    return $cl->{$name}();
                }

                throw $this->_atk__core__hintable_magic__createNotSupportedException();
            }
        };
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any property returns its field name.
     *
     * @return static
     *
     * @phpstan-return MagicModelField<static, string>
     */
    public function fieldName()
    {
        $cl = MagicModelField::class;

        return new $cl($this, MagicModelField::TYPE_FIELD_NAME);
    }
}
