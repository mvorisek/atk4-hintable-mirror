<?php

declare(strict_types=1);

namespace atk4\core\Hintable;

class MagicPropAndMethod extends MagicAbstract
{
    /** @const string */
    public const TYPE_PROPERTY_NAME = 'p_n';
    /** @const string */
    public const TYPE_PROPERTY_NAME_FULL = 'p_nf';
    /** @const string */
    public const TYPE_METHOD_NAME = 'm_n';
    /** @const string */
    public const TYPE_METHOD_NAME_FULL = 'm_nf';
    /** @const string Closure will be bound to static */
    public const TYPE_METHOD_CLOSURE = 'm_c';
    /** @const string Closure will be bound to the target class */
    public const TYPE_METHOD_CLOSURE_PROTECTED = 'm_cp';

    public function __get(string $name): string
    {
        if ($this->_atk__core__hintable_magic__type === self::TYPE_PROPERTY_NAME) {
            return $name;
        }

        if ($this->_atk__core__hintable_magic__type === self::TYPE_PROPERTY_NAME_FULL) {
            return $this->_atk__core__hintable_magic__buildFullName($name);
        }

        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    /**
     * @return string|\Closure
     */
    public function __call(string $name, array $args)
    {
        if ($this->_atk__core__hintable_magic__type === self::TYPE_METHOD_NAME) {
            return $name;
        }

        if ($this->_atk__core__hintable_magic__type === self::TYPE_METHOD_NAME_FULL) {
            return $this->_atk__core__hintable_magic__buildFullName($name);
        }

        $cl = $this->_atk__core__hintable_magic__class;

        if ($this->_atk__core__hintable_magic__type === self::TYPE_METHOD_CLOSURE) {
            return (static function () use ($cl, $name) {
                return \Closure::fromCallable([$cl, $name]);
            })();
        }

        if ($this->_atk__core__hintable_magic__type === self::TYPE_METHOD_CLOSURE_PROTECTED) {
            return \Closure::bind(function () use ($cl, $name) {
                return \Closure::fromCallable([$cl, $name]);
            }, null, $cl)();
        }

        $this->_atk__core__hintable_magic__throwNotSupported();
    }
}
