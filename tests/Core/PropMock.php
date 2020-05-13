<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use Mvorisek\Atk4\Hintable\Core\PropTrait;

class PropMock
{
    use PropTrait;

    public $x = 'xx';
    private $z = 'zz';

    protected function ignoreUnused(): string
    {
        return $this->z;
    }
}
