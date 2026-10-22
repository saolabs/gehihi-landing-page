{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach (config('site.pages') as $slug => $view)
    <url><loc>{{ $origin }}/{{ $slug === '' ? '' : $slug.'.html' }}</loc></url>
@endforeach
</urlset>
