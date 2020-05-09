<?php

declare(strict_types=1);

namespace atk4\core\Hintable;

final class Method
{
    private function __construct()
    {
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any method call returns its (short) name.
     *
     * @param object|string $targetClass
     *
     * @return static
     */
    public static function methodName($targetClass)
    {
        $cl = MagicPropAndMethod::class;
        return new $cl($targetClass, MagicPropAndMethod::TYPE_METHOD_NAME);
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any method call returns its full name, ie. class name + "::" + short name.
     *
     * @param object|string $targetClass
     *
     * @return static
     */
    public static function methodNameFull($targetClass)
    {
        $cl = MagicPropAndMethod::class;
        return new $cl($targetClass, MagicPropAndMethod::TYPE_METHOD_NAME_FULL);
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any method call returns its Closure bound to static.
     *
     * @param object|string $targetClass
     *
     * @return static
     */
    public static function methodClosure($targetClass)
    {
        $cl = MagicPropAndMethod::class;
        return new $cl($targetClass, MagicPropAndMethod::TYPE_METHOD_CLOSURE);
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any method call returns its Closure bound to the target class.
     *
     * @param object|string $targetClass
     *
     * @return static
     */
    public static function methodClosureProtected($targetClass)
    {
        $cl = MagicPropAndMethod::class;
        return new $cl($targetClass, MagicPropAndMethod::TYPE_METHOD_CLOSURE_PROTECTED);
    }
}
