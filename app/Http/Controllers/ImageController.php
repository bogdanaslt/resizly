<?php

namespace App\Http\Controllers;

use App\Models\Parameters;
use App\Services\ImageManipulationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ImageController
{
    public function __construct(
        private readonly ImageManipulationService $imageManipulationService,
    ) {
    }

    public function __invoke(string $parameters, string $image)
    {
        $params = Parameters::fromString($parameters);
        $validator = Validator::make($params->toArray(), $params->getRules());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = Http::get($image);
        if (!$image->ok()) {
            return response()->json(['error' => 'Failed to get image, server returned: ' . $image->getStatusCode()], 422);
        }

        $converted = $this->imageManipulationService->handle($params, $image->body());
        return response($converted)->header('Content-Type', $converted->mediaType());
    }
}
