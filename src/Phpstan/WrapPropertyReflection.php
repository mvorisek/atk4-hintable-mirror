<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Phpstan;

use PHPStan\Reflection\Annotations\AnnotationPropertyReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;

class WrapPropertyReflection implements PropertyReflection
{
    protected AnnotationPropertyReflection $reflection;

    public function __construct(string $name, ClassReflection $declaringClass, Type $type)
    {
        $this->reflection = new AnnotationPropertyReflection( // @phpstan-ignore phpstanApi.constructor
            $name,
            $declaringClass,
            $type,
            $type,
            true,
            false
        );
    }

    #[\Override]
    public function getDeclaringClass(): ClassReflection
    {
        return $this->reflection->getDeclaringClass(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function isStatic(): bool
    {
        return $this->reflection->isStatic(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function isPrivate(): bool
    {
        return $this->reflection->isPrivate(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function isPublic(): bool
    {
        return $this->reflection->isPublic(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function getDocComment(): ?string
    {
        return $this->reflection->getDocComment(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function getReadableType(): Type
    {
        return $this->reflection->getReadableType(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function getWritableType(): Type
    {
        return $this->reflection->getWritableType(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function canChangeTypeAfterAssignment(): bool
    {
        return $this->reflection->canChangeTypeAfterAssignment(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function isReadable(): bool
    {
        return $this->reflection->isReadable(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function isWritable(): bool
    {
        return $this->reflection->isWritable(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function isDeprecated(): TrinaryLogic
    {
        return $this->reflection->isDeprecated(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function getDeprecatedDescription(): ?string
    {
        return $this->reflection->getDeprecatedDescription(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function isInternal(): TrinaryLogic
    {
        return $this->reflection->isInternal(); // @phpstan-ignore phpstanApi.method
    }
}
