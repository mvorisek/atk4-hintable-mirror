<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Phpstan;

use Atk4\Core\Phpunit\TestCase;
use Mvorisek\Atk4\Hintable\Phpstan\AssertSamePhpstanTypeTrait;

class AssertSamePhpstanTypeTest extends TestCase
{
    use AssertSamePhpstanTypeTrait;

    /**
     * @return \DateTimeInterface
     */
    private function demoReturnTypeSimple()
    {
        return new \DateTime();
    }

    /**
     * @return \DateTimeInterface|\stdClass
     */
    private function demoReturnTypeUnion()
    {
        return new \stdClass();
    }

    /**
     * @return \stdClass&\Traversable<\DateTime>
     */
    private function demoReturnTypeIntersect()
    {
        return new \stdClass(); // @phpstan-ignore-line
    }

    /**
     * @return class-string<\DateTimeInterface>
     */
    private function demoReturnTypeClassString()
    {
        return get_class(new \DateTime());
    }

    /**
     * @return array{1:positive-int}
     */
    private function demoReturnTypeArrayWithShape()
    {
        return [1 => 0, 'a' => 1]; // @phpstan-ignore-line
    }

    public function testFromExpression(): void
    {
        static::assertSamePhpstanType('null', null);
        $v = 0;
        static::assertSamePhpstanType('0', $v);
        static::assertSamePhpstanType('int<0, 10>', random_int(0, 10));
        static::assertSamePhpstanType(\DateTime::class, new \DateTime());
        static::assertSamePhpstanType('class-string<' . \DateTime::class . '>', get_class(new \DateTime()));
        static::assertSamePhpstanType('resource|false', fopen('php://memory', 'r'));
    }

    public function testFromPhpdoc(): void
    {
        static::assertSamePhpstanType('DateTimeInterface', $this->demoReturnTypeSimple());
        static::assertSamePhpstanType('DateTimeInterface|stdClass', $this->demoReturnTypeUnion());
        static::assertSamePhpstanType('stdClass&Traversable<mixed, DateTime>', $this->demoReturnTypeIntersect());
        static::assertSamePhpstanType('class-string<DateTimeInterface>', $this->demoReturnTypeClassString());
        static::assertSamePhpstanType('array{1: int<1, max>}', $this->demoReturnTypeArrayWithShape());
    }
}
