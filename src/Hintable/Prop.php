<?php

declare(strict_types=1);

namespace atk4\core\Hintable;

class Prop
{
    private function __construct()
    {
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any property returns its (short) name.
     *
     * @param object|string $targetClass
     *
     * @return object
     */
    public static function prop($targetClass)
    {
        $cl = MagicPropAndMethod::class;

        return new $cl($targetClass, MagicPropAndMethod::TYPE_PROPERTY);
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any property returns its full name, ie. class name + "::" + short name.
     *
     * @param object|string $targetClass
     *
     * @return object
     */
    public static function propFull($targetClass)
    {
        $cl = MagicPropAndMethod::class;

        return new $cl($targetClass, MagicPropAndMethod::TYPE_PROPERTY_FULL);
    }
}
