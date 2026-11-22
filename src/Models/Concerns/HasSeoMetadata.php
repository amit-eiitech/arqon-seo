<?php

namespace Arqon\Seo\Models\Concerns;

use Arqon\Seo\Models\SeoMetadata;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeoMetadata
{
    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /** Get existing metadata or create an empty metadata record for this model. */
    public function getOrCreateSeoMetadata(): SeoMetadata
    {
        return $this->seoMetadata()->firstOrCreate([]);
    }

    /** Update SEO metadata without requiring the host model to be mass assignable. */
    public function updateSeoMetadata(array $attributes): SeoMetadata
    {
        $metadata = $this->getOrCreateSeoMetadata();
        $metadata->fill($attributes);
        $metadata->save();

        return $metadata;
    }
}
