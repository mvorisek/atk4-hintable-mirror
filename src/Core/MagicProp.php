<?php

declare(strict_types=1);

namespace atk4\core\Hintable;

class MagicProp extends MagicAbstract
{
    /** @const string */
    public const TYPE_PROPERTY_NAME = 'p_n';
    /** @const string */
    public const TYPE_PROPERTY_NAME_FULL = 'p_nf';

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
}