<?php

namespace Arqon\Seo\Support;

use Arqon\Seo\Services\SeoResolver;
use Illuminate\Database\Eloquent\Model;

class Seo
{
    public static function for(Model|string $target, array $attributes = []): mixed
    {
        if ($target instanceof Model) {
            return $target->updateSeoMetadata($attributes);
        }

        return app(SeoMetadataWriter::class)->forRoute($target, $attributes);
    }

    public static function current(): array
    {
        return app(SeoResolver::class)->resolve();
    }
}
