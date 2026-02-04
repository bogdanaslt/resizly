<?php

namespace App\Enum;

enum ParametersType: string
{
    case Cloudflare = 'cloudflare';
    case Local = 'local';

    /**
     * Additional rules dependant on Parameters provider
     */
    public function rules(): array
    {
        return match ($this) {
            self::Cloudflare => [
                'blur' => ['min:1', 'max:250'],
                'brightness' => ['min:0', 'max:2'],
                'rotate' => ['in:0,90,180,270']
            ],
            default => [],
        };
    }

    public function modifiers(): array
    {
        return match ($this) {
            self::Cloudflare => [
                'blur' => ['vendor' => 250, 'local' => 100],
                'brightness' => ['local' => 100],
            ],
            default => [],
        };
    }
}
