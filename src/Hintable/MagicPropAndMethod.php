<?php

declare(strict_types=1);

namespace atk4\core\Hintable;

use atk4\core\Exception;

class MagicPropAndMethod
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

    /** @var object|string */
    protected $_atk__core__magic_self_class__class;
    /** @var string */
    protected $_atk__core__magic_self_class__type;

    /**
     * @param object|string $targetClass
     */
    public function __construct($targetClass, string $type)
    {
        if (is_string($targetClass)) {
            $targetClass = (new \ReflectionClass($targetClass))->getName();
        }

        $this->_atk__core__magic_self_class__class = $targetClass;
        $this->_atk__core__magic_self_class__type = $type;
    }

    private function getClass()
    {
        return $this->_atk__core__magic_self_class__class;
    }

    private function getType(): string
    {
        return $this->_atk__core__magic_self_class__type;
    }

    protected function throwNotSupported(): void
    {
        $opName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        throw new Exception('Operation "' . $opName . '" is not supported for type "' . $this->getType() . '"');
    }

    public function __debugInfo(): array
    {
        return [
            'class' => $this->getClass(),
            'type' => $this->getType(),
        ];
    }

    public function __sleep(): array
    {
        $this->throwNotSupported();
    }

    public function __wakeup(): void
    {
        $this->throwNotSupported();
    }

    public function __clone()
    {
        $this->throwNotSupported();
    }

    public function __isset(string $name): bool
    {
        $this->throwNotSupported();
    }

    public function __set(string $name, $value): void
    {
        $this->throwNotSupported();
    }

    public function __unset(string $name): void
    {
        $this->throwNotSupported();
    }

    public static function __callStatic(string $name, array $args): void
    {
        (new static(\stdClass::class, 'static/unknown'))->throwNotSupported();
    }

    protected function buildFullName(string $name): string
    {
        $cl = $this->getClass();

        return (is_string($cl) ? $cl : get_class($cl)) . '::' . $name;
    }

    public function __get(string $name): string
    {
        if ($this->getType() === self::TYPE_PROPERTY_NAME) {
            return $name;
        }

        if ($this->getType() === self::TYPE_PROPERTY_NAME_FULL) {
            return $this->buildFullName($name);
        }

        $this->throwNotSupported();
    }

    /**
     * @return string|\Closure
     */
    public function __call(string $name, array $args)
    {
        if ($this->getType() === self::TYPE_METHOD_NAME) {
            return $name;
        }

        if ($this->getType() === self::TYPE_METHOD_NAME_FULL) {
            return $this->buildFullName($name);
        }

        if ($this->getType() === self::TYPE_METHOD_CLOSURE) {
            $cl = $this->getClass();

            return (static function () use ($cl, $name) {
                return \Closure::fromCallable([$cl, $name]);
            })();
        }

        if ($this->getType() === self::TYPE_METHOD_CLOSURE_PROTECTED) {
            $cl = $this->getClass();

            return \Closure::bind(function () use ($cl, $name) {
                return \Closure::fromCallable([$cl, $name]);
            }, is_object($cl) ? $cl : null, $cl)();
        }

        $this->throwNotSupported();
    }
}
