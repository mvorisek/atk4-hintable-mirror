<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Phpstan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\VerbosityLevel;

/**
 * @implements Rule<Node\Expr\MethodCall>
 */
class AssertSamePhpstanTypeRule implements Rule
{
    private ReflectionProvider $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return Node\Expr\MethodCall::class;
    }

    private function getTraitName(): string
    {
        return AssertSamePhpstanTypeTrait::class;
    }

    /** @var array<class-string, array<string, bool>> */
    private static $_hasTraitMap = [];

    /**
     * Copied from https://github.com/atk4/core/blob/3.0.0/src/TraitUtil.php#L22 .
     *
     * @param class-string $class
     */
    private function hasTrait(string $class, string $traitName): bool
    {
        if (!isset(self::$_hasTraitMap[$class][$traitName])) {
            $getUsesFunc = function (string $trait) use (&$getUsesFunc): array {
                $uses = class_uses($trait);
                foreach ($uses as $use) {
                    $uses += $getUsesFunc($use);
                }

                return $uses;
            };

            $uses = [];
            foreach (array_reverse(class_parents($class) ?: []) + [-1 => $class] as $class) {
                $uses += $getUsesFunc($class);
            }
            $uses = array_unique($uses);

            self::$_hasTraitMap[$class][$traitName] = in_array($traitName, $uses, true);
        }

        return self::$_hasTraitMap[$class][$traitName];
    }

    /**
     * Based on https://github.com/phpstan/phpstan-src/blob/03341cc6bf010faf1e99f1dbddf5cea66d56e3cf/src/Rules/Debug/DumpTypeRule.php
     * and https://github.com/phpstan/phpstan-src/blob/03341cc6bf010faf1e99f1dbddf5cea66d56e3cf/src/Rules/Debug/FileAssertRule.php .
     *
     * @param Node\Expr\MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Identifier || strcasecmp($node->name->name, 'assertSamePhpstanType') !== 0) {
            return [];
        }

        if (!self::hasTrait($scope->getClassReflection()->getName(), $this->getTraitName())) {
            return [];
        }

        if (count($node->args) !== 2) {
            return [
                RuleErrorBuilder::message(sprintf(
                    '%s() method call expects exactly 2 arguments.',
                    $this->getTraitName() . '::assertSamePhpstanType'
                ))
                    ->nonIgnorable()
                    ->build(),
            ];
        }

        $expectedTypeStringType = $scope->getType($node->args[0]->value);
        if (!$expectedTypeStringType instanceof ConstantStringType) {
            return [
                RuleErrorBuilder::message('Expected type must be a literal string.')->nonIgnorable()->build(),
            ];
        }

        $expectedTypeString = $expectedTypeStringType->getValue();
        $actualTypeString = $scope->getType($node->args[1]->value)->describe(VerbosityLevel::precise());
        if ($actualTypeString !== $expectedTypeString) {
            return [
                RuleErrorBuilder::message(sprintf('Expected type %s, actual: %s', $expectedTypeString, $actualTypeString))->nonIgnorable()->build(),
            ];
        }

        return [];
    }
}
