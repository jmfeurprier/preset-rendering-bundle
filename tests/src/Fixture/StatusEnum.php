<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Fixture;

enum StatusEnum: string
{
    case Alive = 'ALIVE';

    case Deceased = 'DECEASED';
}
