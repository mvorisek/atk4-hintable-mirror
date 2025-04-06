<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Phpstan;

use PHPStan\Reflection\Annotations\AnnotationMethodReflection;
use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\Type;

class WrapMethodReflection implements MethodReflection
{
    protected AnnotationMethodReflection $reflection;

    public function __construct(string $name, ClassReflection $declaringClass, Type $returnType)
    {
        $this->reflection = new AnnotationMethodReflection( // @phpstan-ignore phpstanApi.constructor
            $name,
            $declaringClass,
            $returnType,
            [],
            false,
            false,
            null,
            TemplateTypeMap::createEmpty()
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
    public function getName(): string
    {
        return $this->reflection->getName(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function getPrototype(): ClassMemberReflection
    {
        return $this->reflection->getPrototype(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function getVariants(): array
    {
        return $this->reflection->getVariants(); // @phpstan-ignore phpstanApi.method
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
    public function isFinal(): TrinaryLogic
    {
        return $this->reflection->isFinal(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function isInternal(): TrinaryLogic
    {
        return $this->reflection->isInternal(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function getThrowType(): ?Type
    {
        return $this->reflection->getThrowType(); // @phpstan-ignore phpstanApi.method
    }

    #[\Override]
    public function hasSideEffects(): TrinaryLogic
    {
        return $this->reflection->hasSideEffects(); // @phpstan-ignore phpstanApi.method
    }
}
