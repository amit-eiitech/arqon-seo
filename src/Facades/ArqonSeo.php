<?php

namespace Arqon\Seo\Facades;

use Illuminate\Support\Facades\Facade;

class ArqonSeo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'arqon-seo.resolver';
    }
}
