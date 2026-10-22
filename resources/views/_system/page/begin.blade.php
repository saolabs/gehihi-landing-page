@php
    $origin = rtrim(config('app.url'), '/');
    $slug = trim($__env->yieldContent('seo:path'), '/');
    $canonical = $origin.'/'.($slug === '' ? '' : $slug.'.html');
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta:title', 'Gehihi — chat AI nhiều model')</title>
    <meta name="description" content="@yield('meta:description')">
    <meta name="author" content="SaoLabs">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#f8fafc">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="@yield('meta:og_type', 'website')">
    <meta property="og:site_name" content="Gehihi">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:title" content="@yield('meta:title')">
    <meta property="og:description" content="@yield('meta:description')">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $origin }}/assets/img/og-gehihi.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta:title')">
    <meta name="twitter:description" content="@yield('meta:description')">
    <meta name="twitter:image" content="{{ $origin }}/assets/img/og-gehihi.png">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="/assets/css/site.css">
    @include($__system__.'partials.assets')
    {!! $__helper->renderHeadAssets('css') !!}
</head>
<body>
    <div id="app-root" data-server-rendered="true">
