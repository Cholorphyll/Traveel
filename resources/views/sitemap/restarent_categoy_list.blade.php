<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
 xmlns:xhtml="http://www.w3.org/1999/xhtml"
 xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

 

 @foreach ($Location as $post)
@if($post->catids !="")
        <?php
        $categories = array_unique(explode(',', $post->catids));
    ?>  
   
    @foreach ($categories as $category)
      @if($category !="")
        <url>
            <loc>https://www.where2.co/searchr?q={{ $category }}&amp;location=</loc>
        </url>
        @endif
    @endforeach
@endif
@endforeach





@foreach ($Location as $post)
@if($post->catids !="")
        <?php
        $categories = array_unique(explode(',', $post->catids));
    ?>  
   
    @foreach ($categories as $category)
      @if($category !="")
        <url>
            <loc>https://www.where2.co/searchr?q={{ $category }}&amp;location={{ $post->slugid }}</loc>
        </url>
        @endif
    @endforeach
@endif
@endforeach


</urlset>
