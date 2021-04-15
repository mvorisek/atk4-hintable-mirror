<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Data;

use Atk4\Data\Exception;

// @TODO or using Doctrine Annotation? https://www.doctrine-project.org/projects/doctrine-annotations/en/latest/index.html#reading-annotations

class HintablePropertyDef
{
    /** @const string No access restrictions */
    public const VISIBILITY_PUBLIC = 'public';
    /** @const string Property can not be set outside the Model class */
    public const VISIBILITY_PROTECTED_SET = 'protected_set';
    /** @const string Like protected property */
    public const VISIBILITY_PROTECTED = 'protected';

    /** @const int Field is not a reference */
    public const REF_TYPE_NONE = 0;
    /** @const int */
    public const REF_TYPE_ONE = 1;
    /** @const int */
    public const REF_TYPE_MANY = 2;

    /** @var array<string, static[]> */
    private static $_cacheDefsByClass = [];

    /** @var string */
    public $className;
    /** @var string */
    public $name;
    /** @var string */
    public $fieldName;
    /** @var string[] */
    public $allowedTypes;
    /** @var int */
    public $refType;
    /** @var string */
    public $visibility;

    /**
     * @param string[] $allowedTypes
     */
    public function __construct(string $className, string $name, string $fieldName, array $allowedTypes)
    {
        $this->className = $className;
        $this->name = $name;
        $this->fieldName = $fieldName;
        $this->allowedTypes = $allowedTypes;
    }

    /**
     * @return static[]
     */
    public static function createFromClassDoc(string $className): array
    {
        $classRefl = new \ReflectionClass($className);

        if (!isset(self::$_cacheDefsByClass[$classRefl->getName()])) {
            $defs = [];
            $classDoc = preg_replace('~\s+~', ' ', preg_replace('~^\s*(?:/\s*)?\*+(?:/\s*$)?|\s*\*+/\s*$~m', '', $classRefl->getDocComment() ?: ''));
            foreach (preg_split('~(?<!\w)(?=@property(?!\w))~', $classDoc) as $l) {
                $def = static::createFromClassDocLine($classRefl->getName(), $l);
                if ($def !== null) {
                    if (isset($defs[$def->name])) {
                        throw (new Exception('Hintable property is defined twice within the same class'))
                            ->addMoreInfo('property', $def->name)
                            ->addMoreInfo('class', $classRefl->getName());
                    }

                    $defs[$def->name] = $def;
                }
            }

            self::$_cacheDefsByClass[$classRefl->getName()] = $defs;
        }

        $defs = [];
        foreach (self::$_cacheDefsByClass[$classRefl->getName()] as $k => $def) {
            $defs[$k] = clone $def;
        }

        return $defs;
    }

    /**
     * @return static|null
     */
    protected static function createFromClassDocLine(string $className, string $classDocLine): ?self
    {
        if (!preg_match('~^@property ([^\$()]+?) \$([^ ]+) .*@Atk4\\\\(Field|RefOne|RefMany)\(((?:[^()"]+|="[^"]*")*)\)~s', $classDocLine, $matches)) {
            return null;
        }

        $allowedTypes = static::parseDocType($matches[1]);
        $refType = ['RefOne' => self::REF_TYPE_ONE, 'RefMany' => self::REF_TYPE_MANY][$matches[3]] ?? self::REF_TYPE_NONE;
        $opts = static::parseDocAtkFieldOptions($matches[4]);

        $fieldName = null;
        $visibility = null;
        foreach ($opts as $k => $v) {
            if ($k === 'field_name') {
                $fieldName = $v;
            } elseif ($k === 'visibility') {
                $visibility = $v;
            } else {
                throw (new Exception('Hintable property has invalid @Atk4\\' . $matches[3] . ' option'))
                    ->addMoreInfo('key', $k)
                    ->addMoreInfo('value', $v);
            }
        }

        $def = new static($className, $matches[2], $fieldName ?? $matches[2], $allowedTypes);
        $def->refType = $refType;
        $def->visibility = $visibility ?? self::VISIBILITY_PUBLIC;

        return $def;
    }

    /**
     * @return string[]
     */
    protected static function parseDocType(string $doc): array
    {
        $types = [];
        foreach (preg_split('~(?:[^"\|]+|="[^"]*")*\K\|~', $doc) as $t) {
            if (substr($t, 0, 1) === '?') {
                $t = substr($t, 1);
                $types[] = 'null';
            }
            $types[] = $t;
        }

        return array_unique($types);
    }

    /**
     * @return string[]
     */
    protected static function parseDocAtkFieldOptions(string $doc): array
    {
        if (trim($doc) === '') {
            return [];
        }

        $opts = [];
        foreach (preg_split('~(?:[^",]+|="[^"]*")*\K,~', $doc) as $opt) {
            if (!preg_match('~^([^"=]+)=(?:([^"=]+)|"(.*)")$~s', $opt, $matches)
                || ($matches[2] !== '' && $matches[2] !== (string) (int) $matches[2])) {
                throw (new Exception('Hintable property has invalid @Atk4\\Field option syntax'))
                    ->addMoreInfo('value', $opt);
            }
            $opts[trim($matches[1])] = $matches[2] !== '' ? (int) $matches[2] : trim($matches[3]);
        }

        return $opts;
    }

    public function validateVisibility(string $srcClassName, bool $getOnly): void
    {
        $fromProtected = $srcClassName instanceof $this->className;

        if ($this->visibility === self::VISIBILITY_PUBLIC
            || ($getOnly && $this->visibility === self::VISIBILITY_PROTECTED_SET)
            || $fromProtected) {
            return;
        }

        throw new Exception('Visibility of hintable property is restricted, it can not be '
            . ($this->visibility === self::VISIBILITY_PROTECTED_SET ? 'set' : 'read/set') . ' outside Model class');
    }

    /**
     * @param mixed $val
     */
    public function validateSet($val): void
    {
        if (count($this->allowedTypes) === 0) {
            return;
        }

        foreach ($this->allowedTypes as $t) {
            if ($this->validateSetSingle($val, $t)) {
                return;
            }
        }

        throw (new Exception('Value type of hintable property is restricted, value is not allowed'))
            ->addMoreInfo('allowed_types', $this->allowedTypes)
            ->addMoreInfo('value', $val);
    }

    /**
     * @param mixed $val
     */
    protected function validateSetSingle($val, string $allowedType): bool
    {
        if (substr($allowedType, -1) === ']') {
            $allowedType = preg_replace('~\[[^\[\]]*\]$~', '', $allowedType, 1, $c);
            if ($c !== 1 || !is_array($val)) {
                return false;
            }

            foreach ($val as $x) {
                if (!$this->validateSetSingle($x, $allowedType)) {
                    return false;
                }
            }

            return true;
        }

        if ($allowedType === 'null') {
            return $val === null;
        }
        if ($allowedType === 'mixed') { // mixed does not imply null
            return $val !== null;
        }
        if ($allowedType === 'bool' || $allowedType === 'boolean') {
            return is_bool($val);
        }
        if ($allowedType === 'int' || $allowedType === 'integer') {
            return is_int($val);
        }
        if ($allowedType === 'float' || $allowedType === 'double') {
            return is_float($val);
        }
        if ($allowedType === 'string') {
            return is_string($val);
        }
        if ($allowedType === 'array') {
            return is_array($val);
        }
        if ($allowedType === 'object') {
            return is_object($val);
        }

        return is_a($val, $allowedType);
    }
}
