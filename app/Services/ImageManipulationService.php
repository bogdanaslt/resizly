<?php

namespace App\Services;

use App\Enum\Fit;
use App\Enum\Flip;
use App\Enum\Format;
use App\Models\ImageResizeParameters;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;

class ImageManipulationService
{
    public function __construct(
        private readonly ImageManagerInterface $manager,
    ) {
    }

    public function handle(ImageResizeParameters $parameters, $imageData): EncodedImageInterface
    {
        $parameters = $parameters->normalizeParameters();
        $image = $this->manager->read($imageData);

        $parameters->width *= $parameters->dpr;
        $parameters->height *= $parameters->dpr;

        if ($parameters->width || $parameters->height) {
            $image = match ($parameters->fit) {
                Fit::ScaleDown => $image->scaleDown($parameters->width, $parameters->height),
                Fit::Contain => $image->contain($parameters->width, $parameters->height, $parameters->background),
                Fit::Cover => $image->cover($parameters->width, $parameters->height),
                Fit::Crop => $image->crop($parameters->width, $parameters->height, 0, 0, $parameters->background),
                Fit::Pad => $image->pad($parameters->width, $parameters->height, $parameters->background),
                Fit::Squeeze => $image->resize($parameters->width, $parameters->height),
            };
        }

        if (!$parameters->width && !$parameters->height && $parameters->dpr != 1) {
            $image->resize(
                $image->width() * $parameters->dpr,
                $image->height() * $parameters->dpr
            );
        }

        if ($parameters->blur) {
            $image = $image->blur($parameters->blur);
        }

        if ($parameters->brightness) {
            $image = $image->brightness($parameters->brightness);
        }

        if ($parameters->flip) {
            $image = match ($parameters->flip) {
                Flip::Horizontal => $image->flop(),
                Flip::Vertical => $image->flip(),
                Flip::HorizontalVertical => $image->flip()->flop(),
            };
        }

        if ($parameters->rotate) {
            $image = $image->rotate($parameters->rotate);
        }

        if ($parameters->format) {
            $image = match ($parameters->format) {
                Format::Auto, Format::Webp => $image->toWebp(),
                Format::Avif => $image->toAvif(),
                Format::Jpeg => $image->toJpeg(),
                Format::Png => $image->toPng(),
            };
        }

        return $image;
    }
}
