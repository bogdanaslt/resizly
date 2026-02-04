<?php

namespace App\Http\Controllers;

use App\Models\ImageResizeParameters;
use App\Services\ImageManipulationService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Psr\Http\Message\ResponseInterface;

class ImageController
{
    public function __construct(
        private readonly ImageManipulationService $imageManipulationService,
    ) {
    }

    public function __invoke(string $parameters, string $image): ResponseFactory|Response
    {
        $params = ImageResizeParameters::fromCloudflareString($parameters);
        if ($params->validate()->fails()) {
            return response($params->validate()->errors()->toArray(), 422);
        }

        $image = Http::get($image);
        if (!$image->ok()) {
            return response(['error' => sprintf('Failed downloading image, server returned: %d', $image->getStatusCode())], 422);
        }

        if (!in_array($image->header('Content-Type'), config('images.allowed_mime'))) {
            return response(['error' => sprintf('This filetype %s is not supported', $image->header('Content-Type'))], 422);
        }

        $converted = $this->imageManipulationService->handle($params, $image->body());
        return response($converted)->header('Content-Type', $converted->mediaType());
    }
}
