<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Vips\Driver as VipsDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageManagerInterface;

class ResizlyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Add Image manipulation backend drivers
        $this->app->alias(GdDriver::class, 'gd');
        $this->app->alias(ImagickDriver::class, 'imagick');
        $this->app->alias(VipsDriver::class, 'vips');
        $this->app->bind(
            ImageManagerInterface::class,
            fn(Application $app) => new ImageManager($this->app->get(config('images.driver'))),
            true,
        );
    }
}
