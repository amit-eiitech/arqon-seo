<?php

namespace Arqon\Seo\Support;

use Arqon\Seo\Models\SeoMetadata;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;

class SeoMetadataWriter
{
    public function forRoute(string $routeName, array $attributes = []): SeoMetadata
    {
        if (! Route::has($routeName)) {
            throw new InvalidArgumentException("The named route [{$routeName}] does not exist.");
        }

        $metadata = SeoMetadata::query()->firstOrNew(['route_name' => $routeName, 'seoable_type' => null]);
        $metadata->fill($attributes);
        $metadata->route_name = $routeName;
        $metadata->save();

        return $metadata;
    }
}
