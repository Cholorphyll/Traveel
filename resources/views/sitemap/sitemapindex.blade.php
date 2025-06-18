<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9">
  @for($i=1; $i <= $hotcount; $i++)
  <sitemap> 
    <loc>{{ url('/') }}/hotel-listing{{ $i }}-en-us.xml</loc> 
  </sitemap>
  @endfor
  @for($i=1; $i <= $tpHdetailcount; $i++)
  <sitemap> 
    <loc>{{ url('/') }}/hotel-detail{{ $i }}-en-us.xml</loc> 
  </sitemap>
  @endfor
 @for($i = 1; $i <= $landingPagesPageCount; $i++)
    <sitemap>
        <loc>{{ url('landing-pages' . $i . '-en-us.xml') }}</loc>
    </sitemap>
@endfor
@for($i =1; $i <=$locationcount;$i++ )
  <sitemap> 
    <loc>{{ url('/') }}/explore-locations{{ $i }}-en-us.xml</loc> 
  </sitemap> 
  @endfor
<sitemap>
    <loc>{{ url('/sitemap/static/1') }}</loc>
</sitemap>
</sitemapindex>
