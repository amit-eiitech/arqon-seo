<?php

namespace Arqon\Seo\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    protected $table;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('arqon-seo.table', 'seo_metadata'));
    }

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForRouteName(Builder $query, string $routeName): Builder
    {
        return $query->where('route_name', $routeName)->whereNull('seoable_type');
    }

    public function scopeForModel(Builder $query, Model $model): Builder
    {
        return $query->where('seoable_type', $model->getMorphClass())
            ->where('seoable_id', $model->getKey());
    }

    public function getTargetLabelAttribute(): string
    {
        if ($this->route_name && ! $this->seoable_type) {
            return 'Route: ' . $this->route_name;
        }

        return $this->seoable_type
            ? class_basename($this->seoable_type) . ' #' . $this->seoable_id
            : 'Unassigned';
    }


    /** Get the public URL for this metadata target. */
    public function getUrl(): ?string
    {
        // Static route
        if ($this->route_name && ! $this->seoable_type) {
            try {
                return route($this->route_name);
            } catch (\Exception $e) {
                return null;
            }
        }

        // Model-backed
        if ($this->seoable_type && $this->seoable_id) {
            $model = $this->seoable;
            if (! $model) {
                return null;
            }

            // Use canonical_url if set
            if ($this->canonical_url) {
                return $this->canonical_url;
            }

            // Try to find a route by convention: look for routes with parameters
            // matching the model's class name (e.g., {blogPost}, {event})
            $paramName = \Illuminate\Support\Str::camel(class_basename($model->getMorphClass()));

            foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
                if (! $route->getName()) {
                    continue;
                }

                $uri = $route->uri();
                if (str_contains($uri, '{' . $paramName)) {
                    try {
                        return route($route->getName(), [$paramName => $model]);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        return null;
    }

    protected function casts(): array
    {
        return ['robots' => 'array'];
    }
}
