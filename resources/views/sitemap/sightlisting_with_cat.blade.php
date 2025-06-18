<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
 xmlns:xhtml="http://www.w3.org/1999/xhtml"
 xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($Location as $post)
    @php
        $categories = explode(',', $post->catids);
    @endphp
    @if($post->IsMustSee == 1)   <url>
            <loc>{{ url('/') }}/lo-{{ $post->slugid }}-{{ $post->Slug }}/mustsee</loc>
    </url>
    @endif
    @foreach ($categories as $category)
	 <?php $category_name = str_replace(' ','-',$category) ; ?>
        <url>
            <loc>{{ url('/') }}/lo-{{ $post->slugid }}-{{ $post->Slug }}/{{ $category_name }}</loc>
        </url>
    @endforeach
@endforeach
</urlset>
