<?php

namespace Arqon\Seo\View\Components;

use Arqon\Seo\Services\SeoResolver;
use Illuminate\View\Component;
use Illuminate\View\View;

class Seo extends Component
{
    public function __construct(protected ?string $title = null) {}

    public function render(): View
    {
        $resolved = app(SeoResolver::class)->resolve();
        if ($this->title) {
            $resolved['meta']['title'] = $this->title;
        }

        return view('arqon-seo::components.seo', [
            'meta' => $resolved['meta'],
            'record' => $resolved['record'],
            'defaults' => $resolved['defaults'],
        ]);
    }
}
