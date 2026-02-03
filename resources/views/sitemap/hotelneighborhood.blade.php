<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   
    @foreach ($landing as $post)
        <url>
            <loc>https://www.where2.co/hn-{{ $post->LocationID  }}-{{ $post->NeighborhoodId }}-{{ $post->slug }}</loc>         
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>  
        </url>
    @endforeach
</urlset> 
