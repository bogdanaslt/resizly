<?php

namespace App\Enum;

enum Format: string
{
    case Auto = 'auto';
    case Jpeg = 'jpg';
    case Png = 'png';
    case Webp = 'webp';
    case Avif = 'avif';
}
