<?php

namespace App\Enum;

enum Fit: string
{
    case ScaleDown = 'scale-down';
    case Contain = 'contain';
    case Cover = 'cover';
    case Crop = 'crop';
    case Pad = 'pad';
    case Squeeze = 'squeeze';
}
