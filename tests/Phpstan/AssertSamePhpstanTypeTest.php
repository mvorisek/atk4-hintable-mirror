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
     * @return array{0:positive-int}
     */
    private function demoReturnTypeArrayWithShape()
    {
        return [0, 'a' => 1]; // @phpstan-ignore-line
    }

    public function testFromExpression(): void
    {
        $this->assertSamePhpstanType('null', null);
        $v = 0;
        $this->assertSamePhpstanType('0', $v);
        $this->assertSamePhpstanType('int<0, 10>', random_int(0, 10));
        $this->assertSamePhpstanType(\DateTime::class, new \DateTime());
        $this->assertSamePhpstanType('class-string<' . \DateTime::class . '>', get_class(new \DateTime()));
        $this->assertSamePhpstanType('resource|false', fopen('php://memory', 'r'));
    }

    public function testFromPhpdoc(): void
    {
        $this->assertSamePhpstanType('DateTimeInterface', $this->demoReturnTypeSimple());
        $this->assertSamePhpstanType('DateTimeInterface|stdClass', $this->demoReturnTypeUnion());
        $this->assertSamePhpstanType('stdClass&Traversable<mixed, DateTime>', $this->demoReturnTypeIntersect());
        $this->assertSamePhpstanType('class-string<DateTimeInterface>', $this->demoReturnTypeClassString());
        $this->assertSamePhpstanType('array(int<1, max>)', $this->demoReturnTypeArrayWithShape());
    }
}
