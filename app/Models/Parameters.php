<?php

namespace App\Models;

use App\Enum\Fit;
use App\Enum\Flip;
use App\Enum\Format;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class Parameters
{
    private const int BLUR_CF_MAX = 250;
    private const int BLUR_LOCAL_MAX = 100;

    public function getRules(): array
    {
        $widthAndHeightRequired = Rule::requiredIf(function() {
            return in_array($this->fit, [Fit::Cover, Fit::Cover, Fit::Crop, Fit::Pad]);
        });

        return [
            'width' => ['nullable', 'integer', 'min:1', 'max:5000', $widthAndHeightRequired],
            'height' => ['nullable', 'integer', 'min:1', 'max:5000', $widthAndHeightRequired],
            'fit' => [Rule::enum(Fit::class)],
            'blur' => ['nullable', 'integer', 'min:0', 'max:' . self::BLUR_CF_MAX],
            'brightness' => ['nullable', 'numeric', 'min:-100', 'max:100'],
            'rotate' => ['nullable', 'integer', 'in:0,90,180,270']
        ];
    }

    public function __construct(
        public ?int $width = null,
        public ?int $height = null,
        public ?Fit $fit = Fit::ScaleDown,
        public ?int $blur = null,
        public ?float $brightness = null,
        public ?string $background = '#ffffff',
        public ?float $dpr = 1.0,
        public ?int $rotate = 0,
        public ?Flip $flip = null,
        public ?Format $format = Format::Auto,
    ) {
    }

    public static function fromString(string $parameters): self
    {
        $params = collect(explode(',', $parameters))
            ->mapWithKeys(function (string $item) {
                [$key, $value] = explode('=', $item);
                return [$key => match ($key) {
                    'fit' => Fit::from($value),
                    'flip' => Flip::from($value),
                    'blur' => round($value / self::BLUR_CF_MAX * self::BLUR_LOCAL_MAX),
                    'brightness' => round($value * 100 - 100),
                    'background' => 'fff',
                    'format' => Format::from($value),
                    default => $value ?: null,
                }];
            })
            ->filter(fn($item, $key) => property_exists(self::class, $key))
        ;

        return new self(...$params->toArray());
    }

    public function toArray(): array
    {
        return (array)$this;
    }

    public function validate()
    {
        $rules = self::getBaseRules();
    }
}
