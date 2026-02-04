<?php

namespace App\Enum;

enum Flip: string
{
    case Horizontal = 'h';
    case Vertical = 'v';
    case HorizontalVertical = 'hv';
}
