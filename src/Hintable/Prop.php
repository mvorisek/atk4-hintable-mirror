<?php

declare(strict_types=1);

namespace atk4\core\Hintable;

class Prop
{
    private function __construct()
    {
    }

    /**
     * Returns a magic class, document it using phpdoc as an instance of the target class,
     * any property returns its (short) name.
     *
     * @param object|string $targetClass
     *
     * @return object
     */
    public static function propName($targetClass)
    {
        $cl = MagicPropAndMethod::class;

        return new $cl($targetClass, MagicPropAndMethod::TYPE_PROPERTY_NAME);
    }

    /**
     * Returns a magic class, document it using phpdoc as an instance of the target class,
     * any property returns its full name, ie. class name + "::" + short name.
     *
     * @param object|string $targetClass
     *
     * @return object
     */
    public static function propNameFull($targetClass)
    {
        $cl = MagicPropAndMethod::class;

        return new $cl($targetClass, MagicPropAndMethod::TYPE_PROPERTY_NAME_FULL);
    }
}
