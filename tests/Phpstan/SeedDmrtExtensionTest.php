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
        $this->assertSamePhpstanType(Car::class, $car);
        $this->assertSame(Car::class, get_class($car));

        $seed = [CarExtra::class];
        $car = Car::fromSeed($seed);
        $this->assertSamePhpstanType(CarExtra::class, $car);
        $this->assertSame(CarExtra::class, get_class($car));

        $bodyshop = new Bodyshop();
        $car = $bodyshop->acceptCar('a', [Car::class]);
        $this->assertSamePhpstanType(Car::class, $car);
        $this->assertSame(Car::class, get_class($car));

        $car = $bodyshop->acceptCar('b', [CarExtra::class]);
        $this->assertSamePhpstanType(CarExtra::class, $car);
        $this->assertSame(CarExtra::class, get_class($car));
    }

    public function testSeedArrayithGeneric(): void
    {
        /** @var array{0:class-string<CarGeneric<\DateTime>>} */
        $seed = [SeedDemo\CarGeneric::class];
        $car = Car::fromSeed($seed);
        $this->assertSamePhpstanType(CarGeneric::class . '<DateTime>', $car);
        $this->assertSame(CarGeneric::class, get_class($car));
    }

    public function testSeedObject(): void
    {
        $car = Car::fromSeed(new Car());
        $this->assertSamePhpstanType(Car::class, $car);
        $this->assertSamePhpstanType(Car::class, $car);
        $this->assertSame(Car::class, get_class($car));

        $seed = new CarExtra();
        $car = Car::fromSeed($seed);
        $this->assertSamePhpstanType(CarExtra::class, $car);
        $this->assertSame($seed, $car);

        /** @var CarGeneric<\DateTime> */
        $seed = new SeedDemo\CarGeneric();
        $car = Car::fromSeed($seed);
        $this->assertSamePhpstanType(CarGeneric::class . '<DateTime>', $car);
        $this->assertSame(CarGeneric::class, get_class($car));
    }

    public function testSeedUndefined(): void
    {
        $car = Car::fromSeed();
        $this->assertSamePhpstanType(Car::class, $car);
        $this->assertSame(CarDefault::class, get_class($car));

        $bodyshop = new Bodyshop();
        $car = $bodyshop->acceptCar('a');
        $this->assertSamePhpstanType(Car::class, $car);
        $this->assertSame(CarDefault::class, get_class($car));
    }

    public function testSeedEmpty(): void
    {
        $car = Car::fromSeed(null);
        $this->assertSamePhpstanType(Car::class, $car);
        $this->assertSame(CarDefault::class, get_class($car));

        $car = Car::fromSeed([]);
        $this->assertSamePhpstanType(Car::class, $car);
        $this->assertSame(CarDefault::class, get_class($car));
    }

    public function testIntersectNever(): void
    {
        $this->expectException(\TypeError::class);
        $car = Car::fromSeed(new \stdClass());
        $this->assertSamePhpstanType('*NEVER*', $car);
    }
}
