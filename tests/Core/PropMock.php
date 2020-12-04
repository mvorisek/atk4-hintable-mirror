<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\Hintable\Tests\Core;

use Mvorisek\Atk4\Hintable\Core\PropTrait;

class PropMock
{
    use PropTrait;

    /** @var string */
    public $x = 'xx';
    /** @var string */
    private $z = 'zz';
}
