<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Core;

use atk4\core\Exception;

abstract class MagicAbstract
{
    /** @var object|string */
    protected $_atk__core__hintable_magic__class;
    /** @var string */
    protected $_atk__core__hintable_magic__type;

    /**
     * @param object|string $targetClass
     */
    public function __construct($targetClass, string $type)
    {
        if (is_string($targetClass)) { // normalize/validate string class name
            $targetClass = (new \ReflectionClass($targetClass))->getName();
        }

        $this->_atk__core__hintable_magic__class = $targetClass;
        $this->_atk__core__hintable_magic__type = $type;
    }

    protected function _atk__core__hintable_magic__throwNotSupported(): void
    {
        $opName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        throw (new Exception('Operation "' . $opName . '" is not supported'))
            ->addMoreInfo('target_class', $this->_atk__core__hintable_magic__class)
            ->addMoreInfo('type', $this->_atk__core__hintable_magic__type);
    }

    protected function _atk__core__hintable_magic__buildFullName(string $name): string
    {
        $cl = $this->_atk__core__hintable_magic__class;

        return (is_string($cl) ? $cl : get_class($cl)) . '::' . $name;
    }

    public function __debugInfo(): array
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    public function __sleep(): array
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    public function __wakeup(): void
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    public function __clone()
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    public function __invoke()
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    public function __isset(string $name): bool
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    public function __set(string $name, $value): void
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    public function __unset(string $name): void
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    public static function __callStatic(string $name, array $args): void
    {
        (new static(\stdClass::class, 'static'))->_atk__core__hintable_magic__throwNotSupported();
    }

    public function __get(string $name): string
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }

    public function __call(string $name, array $args)
    {
        $this->_atk__core__hintable_magic__throwNotSupported();
    }
}
