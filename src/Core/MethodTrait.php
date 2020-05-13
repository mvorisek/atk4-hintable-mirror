<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Core;

trait MethodTrait
{
    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any method call returns its (short) name.
     *
     * @return static
     */
    public function methodName()
    {
        return Method::methodName($this);
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any method call returns its full name, ie. class name + "::" + short name.
     *
     * @return static
     */
    public function methodNameFull()
    {
        return Method::methodNameFull($this);
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any method call returns its Closure bound to static.
     *
     * @return static
     */
    public function methodClosure()
    {
        return Method::methodClosure($this);
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any method call returns its Closure bound to the target class.
     *
     * @return static
     */
    public function methodClosureProtected()
    {
        return Method::methodClosureProtected($this);
    }
}
