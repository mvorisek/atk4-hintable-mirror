<?php

declare(strict_types=1);

namespace atk4\core\Hintable;

use atk4\core\Exception;

class MagicPropAndMethod
{
    /** @const string */
    public const TYPE_PROPERTY = 'p_n';
    /** @const string */
    public const TYPE_PROPERTY_FULL = 'p_nf';
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
     *
     * @deprecated Atk internal only, to use this class use Prop class or Method class.
     */
    public function __construct($targetClass, string $type)
    {
        $this->_atk__core__magic_self_class__class = $targetClass;
        $this->_atk__core__magic_self_class__type = $type;
    }

    public function throwNotSupported(): void
    {
        $opName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        throw new Exception('Operation "' . $opName . '" not supported');
    }

    public function __debugInfo(): array
    {
        return [
            'class' => $this->_atk__core__magic_self_class__class,
            'type' => $this->_atk__core__magic_self_class__type,
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
        $this->throwNotSupported();
    }

    protected function buildFullName(string $name): string
    {
        $cl = $this->_atk__core__magic_self_class__class;
        return (is_string($cl) ? (new \ReflectionClass($cl))->getName() : get_class($cl)) . '::' . $name;
    }

    public function __get(string $name): string
    {
        if ($this->_atk__core__magic_self_class__type === self::TYPE_PROPERTY) {
            return $name;
        }

        if ($this->_atk__core__magic_self_class__type === self::TYPE_PROPERTY_FULL) {
            return $this->buildFullName($name);
        }

        $this->throwNotSupported();
    }

    /**
     * @return string|\Closure
     */
    public function __call(string $name, array $args)
    {
        if ($this->_atk__core__magic_self_class__type === self::TYPE_METHOD_NAME) {
            return $name;
        }

        if ($this->_atk__core__magic_self_class__type === self::TYPE_METHOD_NAME_FULL) {
            return $this->buildFullName($name);
        }

        if ($this->_atk__core__magic_self_class__type === self::TYPE_METHOD_CLOSURE) {
            $cl = $this->_atk__core__magic_self_class__class;
            return (static function () use ($cl, $name) {
                return \Closure::fromCallable([$cl, $name]);
            })();
        }

        if ($this->_atk__core__magic_self_class__type === self::TYPE_METHOD_CLOSURE_PROTECTED) {
            $cl = $this->_atk__core__magic_self_class__class;
            return \Closure::bind(function () use ($cl, $name) {
                return \Closure::fromCallable([$cl, $name]);
            }, null, $cl)();
        }

        $this->throwNotSupported();
    }
}
