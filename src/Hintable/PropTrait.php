<?php

declare(strict_types=1);

namespace atk4\core\Hintable;

trait PropTrait
{
    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any property returns its (short) name.
     *
     * @return static
     */
    public function prop()
    {
        return Prop::prop($this);
    }

    /**
     * Returns a magic class that pretends to be instance of this class, but in reality
     * any property returns its full name, ie. class name + "::" + short name.
     *
     * @return static
     */
    public function propFull()
    {
        return Prop::propFull($this);
    }
}
