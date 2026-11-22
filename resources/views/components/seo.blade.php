@php
    $meta = $meta ?? [];
    $robots = !empty($meta['robots']) ? $meta['robots'] : ['index', 'follow'];
    $robotsContent = is_array($robots) ? implode(', ', $robots) : (string) $robots;
    $ogImage = $meta['og_image'] ?? null;
    if ($ogImage && !\Illuminate\Support\Str::startsWith($ogImage, ['http://', 'https://'])) {
        $ogImage = \Illuminate\Support\Facades\Storage::disk(config('arqon-seo.disk', 'public'))->url($ogImage);
    }
@endphp
<title>{{ $meta['title'] ?? config('app.name') }}</title>
@if (!empty($meta['description']))
    <meta name="description" content="{{ $meta['description'] }}">
@endif
@if (!empty($meta['canonical_url']))
    <link rel="canonical" href="{{ $meta['canonical_url'] }}">
@endif
<meta name="robots" content="{{ $robotsContent }}">
<meta property="og:type" content="{{ $meta['type'] ?? 'website' }}">
<meta property="og:title" content="{{ $meta['og_title'] ?? ($meta['title'] ?? config('app.name')) }}">
@if (!empty($meta['og_description']))
    <meta property="og:description" content="{{ $meta['og_description'] }}">
@endif
@if (!empty($meta['url']))
    <meta property="og:url" content="{{ $meta['url'] }}">
@endif
@if ($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
@endif
<meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $meta['og_title'] ?? ($meta['title'] ?? config('app.name')) }}">
@if (!empty($meta['og_description']))
    <meta name="twitter:description" content="{{ $meta['og_description'] }}">
@endif
@if ($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif
