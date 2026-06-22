<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderSource: string
{
    case Public = 'public';
    case Admin = 'admin';
}
