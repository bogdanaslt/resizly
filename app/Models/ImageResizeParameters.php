<?php

namespace App\Models;

use App\Enum\Fit;
use App\Enum\Flip;
use App\Enum\Format;
use App\Enum\ParametersType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImageResizeParameters
{
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
        public Format $format = Format::Auto,
        private ParametersType $type = ParametersType::Cloudflare,
        private bool $normalized = false,
    ) {
    }

    public static function fromCloudflareString(string $parameters): self
    {
        $params = collect(explode(',', $parameters))
            ->mapWithKeys(function (string $item) {
                [$key, $value] = explode('=', $item);
                return [$key => match ($key) {
                    'fit' => Fit::from($value),
                    'flip' => Flip::from($value),
                    'format' => Format::from($value),
                    default => $value ?: null,
                }];
            })
            ->filter(fn($item, $key) => property_exists(self::class, $key))
        ;

        return new self(...$params->toArray());
    }

    public function normalizeParameters(): self
    {
        if ($this->normalized || $this->type === ParametersType::Local) {
            return $this;
        }

        $this->blur = round($this->blur / ($this->type->modifiers()['blur']['vendor'] ?? 1) * ($this->type->modifiers()['blur']['local'] ?? 1));
        $this->brightness = $this->brightness ?
            round($this->brightness * ($this->type->modifiers()['blur']['local'] ?? 1) - ($this->type->modifiers()['blur']['local'] ?? 0)) : null;
        $this->normalized = true;

        return $this;
    }

    public function getRules(): array
    {
        $widthAndHeightRequired = Rule::requiredIf(function() {
            return in_array($this->fit, [Fit::Cover, Fit::Cover, Fit::Crop, Fit::Pad]);
        });

        return [
            'width' => ['nullable', 'integer', 'min:1', 'max:5000', $widthAndHeightRequired],
            'height' => ['nullable', 'integer', 'min:1', 'max:5000', $widthAndHeightRequired],
            'fit' => [Rule::enum(Fit::class)],
            'blur' => ['nullable', 'integer'],
            'brightness' => ['nullable', 'numeric'],
            'rotate' => ['nullable', 'integer']
        ];
    }

    public function toArray(): array
    {
        return (array)$this;
    }

    public function validate()
    {
        return Validator::make($this->toArray(), array_merge_recursive($this->getRules(), $this->type->rules()));
    }
}
