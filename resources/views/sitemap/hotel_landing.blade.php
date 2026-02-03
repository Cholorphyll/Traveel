<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   
    @foreach ($landing as $post)
    @for ($i = 1; $i <= 5; $i++)
        <url>
    <loc>https://www.where2.co/ho-{{ $post->slugid }}-{{ $post->Slug }}/st{{$i}}</loc>         
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>  
        </url>
    @endfor
    <?php
       // $amenities = explode(', ', $post->facilities);
     ?>
  @foreach($amenities as $amenity)
  <?php $amenity = str_replace(' ','_',$amenity)?>
        <url>
        <loc>https://www.where2.co/ho-{{ $post->slugid }}-{{ $post->Slug }}/{{$amenity}}</loc>         
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>  
        </url>       
        @endforeach 
       
    @endforeach

    
</urlset> 