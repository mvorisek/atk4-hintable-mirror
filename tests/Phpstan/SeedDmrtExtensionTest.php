<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Phpstan;

use Atk4\Core\Phpunit\TestCase;
use Mvorisek\Atk4\Hintable\Phpstan\AssertSamePhpstanTypeTrait;
use Mvorisek\Atk4\Hintable\Tests\Phpstan\SeedDemo\Bodyshop;
use Mvorisek\Atk4\Hintable\Tests\Phpstan\SeedDemo\Car;
use Mvorisek\Atk4\Hintable\Tests\Phpstan\SeedDemo\CarDefault;
use Mvorisek\Atk4\Hintable\Tests\Phpstan\SeedDemo\CarExtra;
use Mvorisek\Atk4\Hintable\Tests\Phpstan\SeedDemo\CarGeneric;

class SeedDmrtExtensionTest extends TestCase
{
    use AssertSamePhpstanTypeTrait;

    public function testSeedArray(): void
    {
        $car = Car::fromSeed([Car::class]);
        static::assertSamePhpstanType(Car::class, $car);
        static::assertSame(Car::class, get_class($car));

        $seed = [CarExtra::class];
        $car = Car::fromSeed($seed);
        static::assertSamePhpstanType(CarExtra::class, $car);
        static::assertSame(CarExtra::class, get_class($car));

        $bodyshop = new Bodyshop();
        $car = $bodyshop->acceptCar('a', [Car::class]);
        static::assertSamePhpstanType(Car::class, $car);
        static::assertSame(Car::class, get_class($car));

        $car = $bodyshop->acceptCar('b', [CarExtra::class]);
        static::assertSamePhpstanType(CarExtra::class, $car);
        static::assertSame(CarExtra::class, get_class($car));
    }

    public function testSeedArrayithGeneric(): void
    {
        /** @var array{0:class-string<CarGeneric<\DateTime>>} */
        $seed = [SeedDemo\CarGeneric::class];
        $car = Car::fromSeed($seed);
        static::assertSamePhpstanType(CarGeneric::class . '<DateTime>', $car);
        static::assertSame(CarGeneric::class, get_class($car));
    }

    public function testSeedObject(): void
    {
        $car = Car::fromSeed(new Car());
        static::assertSamePhpstanType(Car::class, $car);
        static::assertSamePhpstanType(Car::class, $car);
        static::assertSame(Car::class, get_class($car));

        $seed = new CarExtra();
        $car = Car::fromSeed($seed);
        static::assertSamePhpstanType(CarExtra::class, $car);
        static::assertSame($seed, $car);

        /** @var CarGeneric<\DateTime> */
        $seed = new SeedDemo\CarGeneric();
        $car = Car::fromSeed($seed);
        static::assertSamePhpstanType(CarGeneric::class . '<DateTime>', $car);
        static::assertSame(CarGeneric::class, get_class($car));
    }

    public function testSeedUndefined(): void
    {
        $car = Car::fromSeed();
        static::assertSamePhpstanType(Car::class, $car);
        static::assertSame(CarDefault::class, get_class($car));

        $bodyshop = new Bodyshop();
        $car = $bodyshop->acceptCar('a');
        static::assertSamePhpstanType(Car::class, $car);
        static::assertSame(CarDefault::class, get_class($car));
    }

    public function testSeedEmpty(): void
    {
        $car = Car::fromSeed(null);
        static::assertSamePhpstanType(Car::class, $car);
        static::assertSame(CarDefault::class, get_class($car));

        $car = Car::fromSeed([]);
        static::assertSamePhpstanType(Car::class, $car);
        static::assertSame(CarDefault::class, get_class($car));
    }

    public function testIntersectNever(): void
    {
        $this->expectException(\TypeError::class);
        $car = Car::fromSeed(new \stdClass());
        static::assertSamePhpstanType('*NEVER*', $car);
    }
}
