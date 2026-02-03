<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

   <meta name="robots" content="index, follow">
	 <?php
        $currentUrl = url()->current();
        $path = parse_url($currentUrl, PHP_URL_PATH) ?? '';
        $isIndianDomain = str_contains($currentUrl, 'travell.co.in');
        $mainDomain = 'https://www.travell.co' . $path;
        $indianDomain = 'https://www.travell.co.in' . $path;
    ?>
    <link rel="canonical" href="<?php echo e($currentUrl); ?>" />
    <link rel="alternate" hrefLang="en-us" href="<?php echo e($mainDomain); ?>"/>
    <link rel="alternate" hrefLang="en-in" href="<?php echo e($indianDomain); ?>"/>
    <link rel="alternate" hrefLang="x-default" href="<?php echo e($mainDomain); ?>"/>

  <!-- Google Tag Manager - Deferred for better LCP -->
  <script>
   window.addEventListener('load', function() {
     (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-PTHP3JH4');
   });
  </script>
  <!-- End Google Tag Manager -->
  <?php if(!empty($locationPatent)): ?> <?php $locationPatent = array_reverse($locationPatent); ?> <?php endif; ?>
  <?php $month = date('F'); $year = date('Y'); $lname =""; ?>
  <?php if(!empty($searchresults)): ?> <?php $lname = $location_name ?> <?php endif; ?>
  <?php
    $title = '';

    // Check if attractions filter (xpat) is active
    if (request()->is('*sqx*') && $catheading != '') {
        $nearbyName = $catheading;
        $prefix = 'Attractions near ';
        if (strpos($nearbyName, $prefix) === 0) {
            $nearbyName = substr($nearbyName, strlen($prefix));
        }
        $title = 'Things to do near ' . $nearbyName . ' in ' . $lname;
    } elseif (request()->is('*xpat*')) {
        $title = 'Places to Visit in ' . $lname;
        if ($location_parent_name != "") {
            $title .= ', ' . $location_parent_name;
        }
    } elseif ($catheading != "" || !empty($category_name)) {
        // Use category_name if available (from URL-based categories), otherwise use catheading
        $categoryDisplayName = !empty($category_name) ? $category_name : $catheading;
        
        if (!empty($category_name)) {
            // For URL-based categories, use simple format: "Category in Location"
            $title = $categoryDisplayName . ' in ' . $lname;
        } else {
            // For legacy categories, keep existing format
            $title .= 'Top ';
            if ($totalCountResults != 0) {
                $title .= $totalCountResults . ' ';
            }

            if ($catheading == 'mustsee') {
                $title .= 'Attractions';
            } else {
                $title .= $catheading;
            }
            $title .= ' in ' . $lname;
            if ($location_parent_name != "") {
                $title .= ', ' . $location_parent_name;
            }
            $title .= ' with Travell';
        }
        
        if ($location_parent_name != "" && !empty($category_name)) {
            $title .= ', ' . $location_parent_name;
        }
    } else {
        $title .= 'Best Places to Visit in ' . $lname;
        if ($location_parent_name != "") {
            $title .= ', ' . $location_parent_name;
        }       
        $title .= '- A Complete Travel Guide - Travell (2025)';
    }
?>

<title><?php echo e($title); ?></title>


<?php
    $description = 'Explore';

    if ($catheading != "" && $catheading != "mustsee") {
        $description .= $catheading;
    } else {
        $description .= 'Attractions';
    }

    $description .= ' in ' . $lname;

    if ($location_parent_name != "") {
        $description .= ', ' . $location_parent_name;
    }

    $description .= '  like a local with our curated list of must-visit sites. From historic districts to natural wonders, find everything you need to plan your trip and create lasting memories.';
?>

<meta name="description" content="<?php echo e($description); ?>">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  <!-- Defer fonts for mobile, load normally for desktop -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet" media="print" onload="this.media='all';this.onload=null;">
  <noscript><link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet"></noscript>
  
  <!-- Preload critical resources for better LCP -->
  <link rel="preconnect" href="https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app" crossorigin>
  <link rel="preconnect" href="https://s3-us-west-2.amazonaws.com" crossorigin>
  <link rel="preconnect" href="https://d.basemaps.cartocdn.com" crossorigin>
  <link rel="preconnect" href="https://www.googletagmanager.com">
  <link rel="preconnect" href="https://cdnjs.cloudflare.com">
  <link rel="preconnect" href="https://unpkg.com">
  <link rel="dns-prefetch" href="//www.travell.co">
  
  <!-- Critical inline CSS to prevent layout shifts - Enhanced for mobile -->
  <style>
    /* Mobile-first critical CSS */
    .card__thumb img {
      width: 100%;
      height: auto;
      aspect-ratio: 460/300;
      object-fit: cover;
    }
    .tr-experience-card img {
      width: 100%;
      height: auto;
      aspect-ratio: 460/300;
      object-fit: cover;
    }
    .tr-map-card img {
      width: 100%;
      height: auto;
      aspect-ratio: 460/300;
      object-fit: cover;
    }
    .card__Container {
      min-height: 320px;
      contain: layout style;
    }
    .card__gallerySection {
      min-height: 300px;
    }
    /* Mobile-specific optimizations */
    @media (max-width: 768px) {
      .card__Container {
        min-height: 280px;
      }
      .card__gallerySection {
        min-height: 250px;
      }
      .card__thumb img,
      .tr-experience-card img,
      .tr-map-card img {
        aspect-ratio: 16/10;
      }
    }
    /* Prevent font flash */
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }
  </style>
  
  <?php if(isset($restaurants) && $restaurants->isNotEmpty()): ?>
    <?php
      $firstRestaurant = $restaurants->first();
      $firstImage = null;
      $mobileFirstImage = null;
      if (!empty($firstRestaurant->images)) {
        $images = json_decode($firstRestaurant->images, true);
        if (!empty($images) && is_array($images)) {
          // Desktop image
          $firstImage = 'https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/resize?url=' . urlencode($images[0]) . '&width=380&height=250&quality=80&format=webp';
          // Mobile-optimized image (smaller size)
          $mobileFirstImage = 'https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/resize?url=' . urlencode($images[0]) . '&width=320&height=200&quality=75&format=webp';
        }
      }
    ?>
    <?php if($mobileFirstImage): ?>
      <!-- Preload LCP image - mobile optimized -->
      <link rel="preload" as="image" href="<?php echo e($mobileFirstImage); ?>" fetchpriority="high" media="(max-width: 768px)">
      <link rel="preload" as="image" href="<?php echo e($firstImage); ?>" fetchpriority="high" media="(min-width: 769px)">
    <?php endif; ?>
  <?php endif; ?>
  <!-- Load Font Awesome asynchronously -->
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></noscript>
  
  <!-- Defer JS for mobile, load normally for desktop -->
   <script src="<?php echo e(asset('/frontend/hotel-detail/js/jquery.min.js')); ?>"></script>
   <script src="<?php echo e(asset('/frontend/hotel-detail/js/bootstrap.bundle.min.js')); ?>"></script>
   <script defer src="<?php echo e(asset('/frontend/hotel-detail/js/jquery-ui-datepicker.min.js')); ?>"></script>
   <script defer src="<?php echo e(asset('/frontend/hotel-detail/js/slick.min.js')); ?>"></script>

  <!-- Critical CSS loaded inline for mobile, external for desktop -->
  <link rel="preload" href="<?php echo e(asset('/frontend/hotel-detail/css/bootstrap.min.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'" media="(max-width: 768px)">
  <link rel="stylesheet" href="<?php echo e(asset('/frontend/hotel-detail/css/bootstrap.min.css')); ?>" media="(min-width: 769px)">
  
  <link rel="preload" href="<?php echo e(asset('/frontend/hotel-detail/css/style.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'" media="(max-width: 768px)">
  <link rel="stylesheet" href="<?php echo e(asset('/frontend/hotel-detail/css/style.css')); ?>" media="(min-width: 769px)">
  
  <link rel="preload" href="<?php echo e(asset('/frontend/hotel-detail/css/custom.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'" media="(max-width: 768px)">
  <link rel="stylesheet" href="<?php echo e(asset('/frontend/hotel-detail/css/custom.css')); ?>" media="(min-width: 769px)">
  
  <noscript>
    <link rel="stylesheet" href="<?php echo e(asset('/frontend/hotel-detail/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('/frontend/hotel-detail/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('/frontend/hotel-detail/css/custom.css')); ?>">
  </noscript>
  
  <!-- Non-critical CSS loaded asynchronously -->
  <link rel="preload" href="<?php echo e(asset('/frontend/hotel-detail/css/calendar.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="<?php echo e(asset('/frontend/hotel-detail/css/slick.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="<?php echo e(asset('/frontend/hotel-detail/css/responsive.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="<?php echo e(asset('css/cookie-consent.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="<?php echo e(asset('/css/map_leaflet.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="<?php echo e(asset('/css/custom.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  
  <!-- Preload LCP image if available - mobile optimized -->
  <?php if(!empty($searchresults) && count($searchresults) > 0): ?>
    <?php
      $firstItem = $searchresults[0];
      $lcpImage = asset('frontend/hotel-detail/images/Hotel lobby-image.png');
      $mobileLcpImage = $lcpImage;
      if (isset($sightImages) && !$sightImages->isEmpty()) {
        foreach ($sightImages as $sImage) {
          if ($sImage->Sightid == $firstItem->SightId && !str_contains($sImage->Image, 'vid')) {
            $lcpImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=460&height=300";
            // Mobile-optimized smaller image
            $mobileLcpImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=320&height=200&quality=75";
            break;
          }
        }
      }
    ?>
    <link rel="preload" as="image" href="<?php echo e($mobileLcpImage); ?>" fetchpriority="high" media="(max-width: 768px)">
    <link rel="preload" as="image" href="<?php echo e($lcpImage); ?>" fetchpriority="high" media="(min-width: 769px)">
  <?php endif; ?>
  
  <script defer src="<?php echo e(asset('js/cookie-consent.js')); ?>"></script>
  
  <!-- Also At & Must See Sections Styles -->

  	<!----New Explore Script And Css Start -->

    <link rel="preload" href="<?php echo e(asset('explore/css/bootstrap.min.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?php echo e(asset('explore/css/style.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?php echo e(asset('explore/css/calendar.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?php echo e(asset('explore/css/slick.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?php echo e(asset('explore/css/responsive.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
      <link rel="stylesheet" href="<?php echo e(asset('explore/css/bootstrap.min.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('explore/css/style.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('explore/css/calendar.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('explore/css/slick.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('explore/css/responsive.css')); ?>">
    </noscript>


	<!----New Explore Script And Css Start -->
 
 <script type="text/javascript">
  // Defer slider initialization for better LCP on mobile
  if (window.innerWidth <= 768) {
    window.addEventListener('load', function() {
      initSliders();
    });
  } else {
    $(document).ready(function() {
      initSliders();
    });
  }

  function initSliders() {
    if (typeof $ === 'undefined' || typeof $.fn.slick === 'undefined') {
      setTimeout(initSliders, 100);
      return;
    }
    $('.tr-search-by-category').slick({
      autoplay: true,
      autoplaySpeed: 2000,
      dots: false,
      arrows: false,
      infinite: false,
      slidesToShow: 1,
      slidesToScroll: 1,
      vertical: true,
    });
    $('.tr-tickets-silder').slick({
      autoplay: false,
      autoplaySpeed: 2000,
      dots: false,
      arrows: true,
      infinite: false,
      slidesToShow: 2.1,
      slidesToScroll: 1,
      responsive: [{
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 1.5,
          slidesToScroll: 1
        }
      }]
    });
    $('.tr-explore-filter-slider').slick({
      autoplay: false,
      autoplaySpeed: 2000,
      dots: false,
      arrows: true,
      infinite: false,
      slidesToShow: 3,
      slidesToScroll: 1,
      variableWidth: true,
      responsive: [{
        breakpoint: 768,
        settings: {
          arrows: false,
        }
      }]
    });
    $('.tr-experience-slider').slick({
      autoplay: false,
      autoplaySpeed: 2000,
      dots: true,
      arrows: false,
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1
    });
    // Initialize Also At slider
           $('.tr-market-slider').slick({
          autoplay: false,
          autoplaySpeed: 2000,  
          dots: false,           
          arrows: true,         
          infinite: false,       
          slidesToShow: 1.8,      
          slidesToScroll: 1,
          responsive: [
            {
              breakpoint: 768, 
              settings: {
                arrows: false, 
                slidesToShow: 1.3,  
                slidesToScroll: 1
              }
            }
          ]     
        });
    $('.tr-common-slider').slick({
          autoplay: false,
          autoplaySpeed: 2000,  
          dots: false,           
          arrows: true,         
          infinite: false,       
          slidesToShow: 3,      
          slidesToScroll: 1,
          responsive: [
            {
              breakpoint: 768, 
              settings: {
                arrows: false, 
                slidesToShow: 2.5,  
                slidesToScroll: 1
              }
            }
          ]     
        });
  }
  </script>
  <script>
// Dynamic Save handler for listing cards
(function(){
  try{ console.log('travell: save/unsave script loaded'); }catch(_){ }
  const ADD_ITEM_URL = "<?php echo e(route('trip.places.addItem')); ?>";
  const REMOVE_ITEM_URL = "<?php echo e(route('trip.places.removeItem')); ?>";
  const ACTIVE_TRIP_URL = "<?php echo e(route('trip.active')); ?>";
  const ENSURE_DEFAULT_URL = "<?php echo e(route('trip.places.ensureDefault')); ?>";
//   const LOGIN_URL = "<?php echo e(route('google.login')); ?>"; // kept for reference, not used for redirects

  // Minimal toast polyfill (only if not provided by theme)
  if(typeof window.toast !== 'function'){
    window.toast = function(message, type){
      try{ console.log('toast:', type || 'info', message); }catch(_){ }
      const id = 'travell-toast-stack';
      let stack = document.getElementById(id);
      if(!stack){
        stack = document.createElement('div');
        stack.id = id;
        stack.setAttribute('aria-live','polite');
        stack.style.position = 'fixed';
        stack.style.right = '16px';
        stack.style.bottom = '16px';
        stack.style.display = 'flex';
        stack.style.flexDirection = 'column-reverse';
        stack.style.gap = '8px';
        stack.style.zIndex = '2147483647';
        stack.style.pointerEvents = 'none';
        document.body.appendChild(stack);
      }
      const item = document.createElement('div');
      item.textContent = message;
      item.style.pointerEvents = 'auto';
      item.style.maxWidth = '320px';
      item.style.padding = '10px 12px';
      item.style.borderRadius = '8px';
      item.style.boxShadow = '0 6px 18px rgba(0,0,0,0.18)';
      item.style.color = '#fff';
      item.style.fontSize = '14px';
      item.style.lineHeight = '1.35';
      item.style.opacity = '0';
      item.style.transform = 'translateY(8px)';
      item.style.transition = 'opacity .2s ease, transform .2s ease';
      const map = { ok:'#2e7d32', warn:'#b26a00', error:'#c62828', info:'#1565c0' };
      const bg = map[(type||'ok')] || map.ok;
      item.style.background = bg;
      // Mount and animate
      stack.appendChild(item);
      requestAnimationFrame(()=>{ item.style.opacity = '1'; item.style.transform='translateY(0)'; });
      // Auto remove
      const ttl = 2500;
      setTimeout(()=>{
        item.style.opacity = '0'; item.style.transform='translateY(8px)';
        setTimeout(()=>{ item.remove(); }, 200);
      }, ttl);
    }
  }

  function getCsrf(){
    const t = document.querySelector('meta[name="csrf-token"]');
    return t ? t.getAttribute('content') : '';
  }

  function ensureToastRoot(){
    let root = document.getElementById('tr-toast-root');
    if(!root){
      root = document.createElement('div');
      root.id = 'tr-toast-root';
      root.style.cssText = 'position:fixed;z-index:9999;bottom:20px;left:50%;transform:translateX(-50%);display:flex;gap:8px;flex-direction:column;align-items:center;';
      document.body.appendChild(root);
    }
    return root;
  }

  function toast(msg, type='ok'){
    const root = ensureToastRoot();
    // Show only one toast at a time
    try { while(root.firstChild){ root.removeChild(root.firstChild); } } catch(_){ }
    const el = document.createElement('div');
    const bg = type==='ok' ? '#0ea5e9' : (type==='warn' ? '#fbbf24' : '#ef4444');
    el.style.cssText = `color:#fff;background:${bg};padding:10px 14px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.15);font-size:14px;max-width:80vw;`;
    el.textContent = msg;
    root.appendChild(el);
    setTimeout(()=>{ el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(), 300); }, 2200);
  }

  // Detect unauthenticated conditions from JSON payloads
  function isUnauthPayload(obj){
    if(!obj || typeof obj !== 'object') return false;
    const msg = String(obj.message || obj.error || '').toLowerCase();
    if(msg.includes('unauth')) return true; // matches 'unauthenticated'
    if(obj.code && String(obj.code).toLowerCase().includes('unauth')) return true;
    if(obj.status && String(obj.status).toLowerCase().includes('unauth')) return true;
    return false;
  }

  // Prefer opening the existing Explore sign-in modal instead of redirecting
  document.addEventListener('hidden.bs.modal', () => {
  document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  document.body.classList.remove('modal-open');
});
  function openSignInModal(){
    try{
      // 1) If user is logged in, the header shows .tr-logged which toggles account modal; skip in our case
      // 2) If user is logged OUT, header shows a button .tr-login with data-bs-target="#signInModal"
      const loginBtn = document.querySelector('.tr-login-section .tr-login');
      if(loginBtn){ loginBtn.click(); return true; }

      // Fallback: try to open the modal directly
      const modalEl = document.getElementById('signInModal');
      if(modalEl){
        try{
          if(window.bootstrap && window.bootstrap.Modal){
            const inst = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            inst.show();
          }else{
            // Minimal fallback in case Bootstrap JS is not globally available
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.removeAttribute('aria-hidden');
          }
          return true;
        }catch(_){ }
      }

      // As a last attempt, click any element that declares the target
      const anyTrigger = document.querySelector('[data-bs-target="#signInModal"], [data-target="#signInModal"]');
      if(anyTrigger){ anyTrigger.click(); return true; }
    }catch(_){ }
    return false;
  }

  async function resolveTripAndList(){
    // Try meta tags first
    let tripId = document.querySelector('meta[name="trip-id"]')?.getAttribute('content') || '';
    let listId = document.querySelector('meta[name="list-id"]')?.getAttribute('content') || '';

    if(tripId && listId){
      return { tripId, listId };
    }

    // Fetch active trip
    const tRes = await fetch(ACTIVE_TRIP_URL, { headers: { 'Accept': 'application/json', 'X-Requested-With':'XMLHttpRequest' }, credentials:'same-origin' });
    if(tRes.status === 401){ throw new Error('auth'); }
    if(tRes.status === 404){ throw new Error('no-trip'); }
    const tJson = await tRes.json();
    if(isUnauthPayload(tJson)){ throw new Error('auth'); }
    if(!tRes.ok || tJson.ok === false){ throw new Error(tJson.message || 'No active trip'); }
    tripId = tJson.trip_id;

    // Ensure default list
    const lRes = await fetch(ENSURE_DEFAULT_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrf(),
        'Accept': 'application/json',
        'X-Requested-With':'XMLHttpRequest'
      },
      body: JSON.stringify({ trip_id: tripId, name: 'Saved Items' }),
      credentials:'same-origin'
    });
    const lJson = await lRes.json();
    if(isUnauthPayload(lJson)){ throw new Error('auth'); }
    if(!lRes.ok || lJson.ok === false){ throw new Error(lJson.message || 'Cannot create list'); }
    listId = lJson.list?.id || '';

    // Write back meta to reuse for next saves
    const head = document.querySelector('head');
    if(head){
      if(!document.querySelector('meta[name="trip-id"]')){
        const mt = document.createElement('meta'); mt.setAttribute('name','trip-id'); mt.setAttribute('content', tripId); head.appendChild(mt);
      }else{ document.querySelector('meta[name="trip-id"]').setAttribute('content', tripId); }
      if(!document.querySelector('meta[name="list-id"]')){
        const ml = document.createElement('meta'); ml.setAttribute('name','list-id'); ml.setAttribute('content', listId); head.appendChild(ml);
      }else{ document.querySelector('meta[name="list-id"]').setAttribute('content', listId); }
    }

    return { tripId, listId };
  }

  function bumpSavedSummary(delta){
    const box = document.querySelector('.top__Save .details__Savetime');
    const wrap = document.querySelector('.top__Save');
    if(!box){
      if(wrap && delta > 0){ wrap.style.display = ''; }
      return;
    }
    const h = box.querySelector('h6');
    const p = box.querySelector('p');
    if(h && !h.textContent.trim()) h.textContent = 'Saved Items';
    if(p){
      const m = (p.textContent.match(/(\d+)/) || [null,'0'])[1];
      const next = Math.max(0, (parseInt(m,10)||0) + delta);
      p.textContent = next + ' saved pictures';
      // Show/hide entire Saved Items section based on count
      if(wrap){ wrap.style.display = next > 0 ? '' : 'none'; }
    }
  }

  // Load the Saved Items summary (title + count) on page load, no markup changes
  async function loadSavedSummary(){
    try{
      const wrap = document.querySelector('.top__Save');
      const box = document.querySelector('.top__Save .details__Savetime');
      if(!box){ if(wrap){ wrap.style.display='none'; } return; }
      const titleEl = box.querySelector('h6');
      const countEl = box.querySelector('p');
      const galleryRoot = document.querySelector('.top__Save .gallery-wrapper');
      const tRes = await fetch(ACTIVE_TRIP_URL, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }, credentials:'same-origin' });
      if(!tRes.ok) return;
      const tJson = await tRes.json();
      if(!tJson.ok) return;
      const lRes = await fetch(ENSURE_DEFAULT_URL, {
        method:'POST',
        headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN': getCsrf(),'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
        body: JSON.stringify({ trip_id: tJson.trip_id, name: 'Saved Items' }),
        credentials:'same-origin'
      });
      if(!lRes.ok) return;
      const lJson = await lRes.json();
      if(!lJson.ok) return;
      const list = lJson.list || {};
      const name = list.name || 'Saved Items';
      const items = dedupeItems(Array.isArray(list.items) ? list.items : []);
      // Cache globally for modal usage
      try{ window.SAVED_ITEMS = items; }catch(_){ }
      // If no items, keep whole section hidden
      if(wrap){ wrap.style.display = items.length > 0 ? '' : 'none'; }
      if(items.length === 0){ return; }
      // Populate when we have some items
      titleEl.textContent = name;
      countEl.textContent = items.length + ' saved pictures';
      // Also sync current cards' saved states based on list
      try{ syncSavedStates(items); }catch(_){ }
      // Render thumbnails in Saved Items gallery
      try{ renderSavedGallery(items, galleryRoot); }catch(_){ }
      // If section is already open, ensure the inline list reflects the items (replace any placeholder)
      try{
        if(wrap){
          const holder = ensureSavedCardsHolder(wrap);
          const key = JSON.stringify(dedupeItems(items).map(it => (it.id||it.Id||it.SightId||it.slug||it.Name||it.name||it.title||'')+':' + (getItemImage(it)||'')));
          renderSavedCardsList(holder, items);
          holder.dataset.renderKey = key;
          if(wrap.classList.contains('open')){ holder.style.display = 'block'; }
        }
      }catch(_){ }
    }catch(_){ /* silent */ }
  }

  // Page-level image map built from server-rendered results (ids and names -> image URL)
  try{
    if(!window.PAGE_ITEM_IMAGES){ window.PAGE_ITEM_IMAGES = { ids:{}, names:{} }; }
  }catch(_){ }
  // Inject known images from server-rendered results
  (function(){
    try{
      const map = window.PAGE_ITEM_IMAGES || (window.PAGE_ITEM_IMAGES = { ids:{}, names:{} });
      // Blade will render this list inline
      const list = [
        <?php
          $__imgs = [];
          if(!empty($searchresults)){
            foreach($searchresults as $sr){
              $id = '';
              if(isset($sr->SightId)) $id = $sr->SightId; elseif(isset($sr->HotelId)) $id = $sr->HotelId; elseif(isset($sr->RestaurantId)) $id = $sr->RestaurantId; elseif(isset($sr->id)) $id = $sr->id;
              $name = isset($sr->Name) ? $sr->Name : (isset($sr->name) ? $sr->name : (isset($sr->Title) ? $sr->Title : ''));
              $img = '';
              if(isset($sr->MainImage) && $sr->MainImage){ $img = asset('storage/sights/'.$sr->MainImage); }
              elseif(isset($sr->Image) && $sr->Image){ $img = asset('storage/sights/'.$sr->Image); }
              elseif(isset($sr->HotelImage) && $sr->HotelImage){ $img = asset('storage/hotels/'.$sr->HotelImage); }
              elseif(isset($sr->RestaurantImage) && $sr->RestaurantImage){ $img = asset('storage/restaurants/'.$sr->RestaurantImage); }
              if($img){ $__imgs[] = [ 'id' => $id, 'name' => $name, 'img' => $img ]; }
            }
          }
          echo implode(",\n        ", array_map(function($row){
            return json_encode($row, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
          }, $__imgs));
        ?>
      ];
      list.forEach(function(row){
        if(row.id){ map.ids[String(row.id)] = row.img; }
        if(row.name){ map.names[String(row.name).toLowerCase()] = row.img; }
      });
    }catch(_){ }
  })();

  // Helper to resolve a plausible image URL from a saved item (broad key support)
  function getItemImage(it){
    if(!it || typeof it !== 'object') return '';
    // 1) Direct URL fields
    const direct = (
      it.image_url || it.media_url || it.thumb || it.thumbnail || it.img || it.image || it.photo || it.photo_url || it.url ||
      (it.meta && (it.meta.image || it.meta.thumbnail)) || (it.data && (it.data.image || it.data.thumbnail)) || ''
    );
    if(typeof direct === 'string' && /^https?:\/\//i.test(direct)) return direct;

    // 2) Array fields like Images/photos (including nested containers)
    const nested = [it.item, it.Item, it.data, it.details, it.Details, it.payload, it.record].filter(v=>v&&typeof v==='object');
    const arrays = [it.Images, it.images, it.photos, it.Photos, it.gallery, it.Gallery, it.Media, it.media];
    nested.forEach(n => { arrays.push(n && n.Images, n && n.images, n && n.photos, n && n.Photos, n && n.gallery, n && n.Gallery, n && n.Media, n && n.media); });
    for(const arr of arrays){
      if(Array.isArray(arr) && arr.length){
        // Find first string URL or object with url/src
        for(const x of arr){
          const cand = typeof x === 'string' ? x : (x && (x.url || x.src || x.image_url || x.image || x.MainImage || x.Image || x.HotelImage || x.RestaurantImage));
          if(typeof cand === 'string' && cand){
            if(/^https?:\/\//i.test(cand)) return cand;
            const built = buildStorageUrl(it, cand);
            if(built) return built;
          }
        }
      }
    }

    // 3) Filename-style fields (no protocol), including nested
    const fromFields = [it, ...nested].map(o => o||{});
    let fileName = '';
    for(const o of fromFields){
      fileName = (
        (typeof o.MainImage === 'string' && o.MainImage) ||
        (typeof o.PrimaryImage === 'string' && o.PrimaryImage) ||
        (typeof o.FeaturedImage === 'string' && o.FeaturedImage) ||
        (typeof o.Image === 'string' && o.Image) ||
        (typeof o.HotelImage === 'string' && o.HotelImage) ||
        (typeof o.RestaurantImage === 'string' && o.RestaurantImage) ||
        (typeof o.main_image === 'string' && o.main_image) ||
        (typeof o.image === 'string' && o.image) || ''
      );
      if(fileName) break;
    }
    if(fileName){
      if(/^https?:\/\//i.test(fileName)) return fileName;
      const built = buildStorageUrl(it, fileName);
      if(built) return built;
    }

    // 4) Lookup from page-level mapping by id or name
    try{
      const map = window.PAGE_ITEM_IMAGES || { ids:{}, names:{} };
      const byId = it.SightId || it.sight_id || it.HotelId || it.hotel_id || it.RestaurantId || it.restaurant_id || it.id || it.Id;
      if(byId && map.ids[String(byId)]) return map.ids[String(byId)];
      const byName = (it.Name || it.name || it.title || '').toString().toLowerCase();
      if(byName && map.names[byName]) return map.names[byName];
    }catch(_){ }

    // 5) As a last resort, return any direct if string (may be relative)
    if(typeof direct === 'string' && direct) return direct;
    return '';
  }

  function buildStorageUrl(it, file){
    if(!file || typeof file !== 'string') return ''; 
    // If already a full URL
    if(/^https?:\/\//i.test(file)) return file;
    // Strip leading slashes
    const fname = file.replace(/^\/+/, '');
    // If filename already contains a storage subfolder, use generic storage
    if(/^sights\//i.test(fname) || /^hotels\//i.test(fname) || /^restaurants\//i.test(fname)){
      return "<?php echo e(asset('storage')); ?>/" + fname;
    }
    const base = guessStorageBase(it);
    return base ? (base + fname) : '';
  }

  function guessStorageBase(it){
    const type = (it.Type || it.type || it.Category || it.category || it.TypeName || it.CategoryName || '').toString().toLowerCase();
    if(type.includes('sight') || type.includes('attraction') || type.includes('place')) return "<?php echo e(asset('storage/sights')); ?>/";
    if(type.includes('hotel') || type.includes('stay') || type.includes('accommodation')) return "<?php echo e(asset('storage/hotels')); ?>/";
    if(type.includes('restaurant') || type.includes('food') || type.includes('dining')) return "<?php echo e(asset('storage/restaurants')); ?>/";
    // Heuristics using field presence
    if('MainImage' in it || 'SightId' in it || 'sight_id' in it) return "<?php echo e(asset('storage/sights')); ?>/";
    if('HotelImage' in it || 'HotelId' in it || 'hotel_id' in it) return "<?php echo e(asset('storage/hotels')); ?>/";
    if('RestaurantImage' in it || 'RestaurantId' in it || 'restaurant_id' in it) return "<?php echo e(asset('storage/restaurants')); ?>/";
    // Fallback generic storage
    return "<?php echo e(asset('storage')); ?>/";
  }

  // Helper to de-duplicate saved items using best-effort stable keys
  function dedupeItems(items){
    const out = [];
    const seen = new Set();
    (Array.isArray(items) ? items : []).forEach(it => {
      let key = '';
      try{
        key = it.id || it.Id || it.SightId || it.sight_id || it.slug || it.uid || it.uuid || it.guid || it.Name || it.name || it.title || '';
        if(!key){
          const img = getItemImage(it) || '';
          key = (it.Name || it.name || it.title || '') + '|' + img;
        }
        if(!key){ key = JSON.stringify(it); }
      }catch(_){ key = Math.random().toString(36).slice(2); }
      if(!seen.has(key)){
        seen.add(key);
        out.push(it);
      }
    });
    return out;
  }

  // Render up to 3 thumbnails inside the Saved Items gallery
  function renderSavedGallery(items, root){
    if(!root) return;
    // Clear existing
    while(root.firstChild){ root.removeChild(root.firstChild); }
    const fallback = "<?php echo e(asset('explore/images/experience-2.png')); ?>";
    const maxThumbs = 3;
    const thumbs = [];
    const unique = dedupeItems(items);
    for(let i=0;i<unique.length && thumbs.length<maxThumbs;i++){
      const src = getItemImage(unique[i]);
      if(src && typeof src === 'string' && !thumbs.includes(src)){ thumbs.push(src); }
    }
    // If no images available, keep graceful placeholders (3 blocks)
    if(thumbs.length === 0){
      for(let i=0;i<maxThumbs;i++){
        const box = document.createElement('div');
        box.className = 'containergallery';
        if(i===maxThumbs-1){
          const img = document.createElement('img');
          img.src = fallback; img.alt = '';
          box.appendChild(img);
        }
        root.appendChild(box);
      }
      return;
    }
    // Build UI with available thumbs (pad to 3 with placeholders)
    for(let i=0;i<maxThumbs;i++){
      const box = document.createElement('div');
      box.className = 'containergallery';
      const src = thumbs[i] || '';
      if(src){
        const img = document.createElement('img');
        img.src = src; img.alt='';
        img.loading = 'lazy';
        img.referrerPolicy = 'no-referrer';
        box.appendChild(img);
      } else if(i===maxThumbs-1){
        const img = document.createElement('img');
        img.src = fallback; img.alt='';
        box.appendChild(img);
      }
      root.appendChild(box);
    }
  }

  // Build and show a simple modal gallery with all saved item images
  async function openSavedGalleryModal(){
    try{
      // Use cached items if available for instant open
      let items = Array.isArray(window.SAVED_ITEMS) ? window.SAVED_ITEMS : [];
      if(!items.length){
        // Fallback: fetch from API
        const tRes = await fetch(ACTIVE_TRIP_URL, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }, credentials:'same-origin' });
        if(!tRes.ok) throw new Error('auth_or_trip');
        const tJson = await tRes.json();
        if(!tJson.ok) throw new Error('auth_or_trip');
        const lRes = await fetch(ENSURE_DEFAULT_URL, {
          method:'POST',
          headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN': getCsrf(),'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
          body: JSON.stringify({ trip_id: tJson.trip_id, name: 'Saved Items' }),
          credentials:'same-origin'
        });
        if(!lRes.ok) throw new Error('no_list');
        const lJson = await lRes.json();
        if(!lJson.ok) throw new Error('no_list');
        items = Array.isArray(lJson.list?.items) ? lJson.list.items : [];
      }
      let imgs = dedupeItems(items).map(getItemImage).filter(Boolean);
      // If still no images, show placeholders instead of error to avoid confusion
      const fallback = "<?php echo e(asset('explore/images/experience-2.png')); ?>";
      if(imgs.length === 0){
        imgs = items.length ? Array(Math.min(items.length, 12)).fill(fallback) : [fallback, fallback, fallback];
      }

      // Create overlay root if missing
      let overlay = document.getElementById('saved-gallery-overlay');
      if(!overlay){
        overlay = document.createElement('div');
        overlay.id = 'saved-gallery-overlay';
        overlay.style.cssText = 'position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.8);display:flex;align-items:center;justify-content:center;padding:20px;';
        const inner = document.createElement('div');
        inner.style.cssText = 'max-width:90vw;max-height:85vh;overflow:auto;display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;';
        overlay.appendChild(inner);
        overlay.addEventListener('click', function(e){ if(e.target===overlay){ document.body.removeChild(overlay); } });
        document.addEventListener('keydown', function esc(e){ if(e.key==='Escape' && document.getElementById('saved-gallery-overlay')){ document.body.removeChild(overlay); } }, { once:true });
        document.body.appendChild(overlay);
      }
      const grid = overlay.firstElementChild;
      // Clear previous
      while(grid.firstChild){ grid.removeChild(grid.firstChild); }
      // Populate
      const frag = document.createDocumentFragment();
      imgs.forEach(src => {
        const figure = document.createElement('figure');
        figure.style.cssText = 'margin:0;background:#111;border-radius:10px;overflow:hidden;border:1px solid rgba(255,255,255,.08)';
        const img = document.createElement('img');
        img.src = src; img.alt = '';
        img.style.cssText = 'width:100%;height:160px;object-fit:cover;display:block;';
        img.loading = 'lazy';
        img.referrerPolicy = 'no-referrer';
        figure.appendChild(img);
        frag.appendChild(figure);
      });
      grid.appendChild(frag);
      overlay.style.display = 'flex';
    }catch(err){
      if(err && err.message==='auth_or_trip'){ toast('Please sign in to view saved items.','warn'); } else { toast('Could not open gallery.','error'); }
    }
  }

  // Sync visual state of bookmark buttons to backend list
  function syncSavedStates(listItems){
    if(!Array.isArray(listItems)) return;
    // Build quick lookup by IDs and also keep titles as a fallback
    const savedIds = new Set();
    const savedTitles = new Set();
    listItems.forEach(it => {
      if(!it || typeof it !== 'object') return;
      const ids = [
        it.item_id, it.id, it.Id,
        it.SightId, it.sight_id,
        it.HotelId, it.hotel_id,
        it.RestaurantId, it.restaurant_id
      ].filter(Boolean).map(String);
      ids.forEach(v => savedIds.add(v));
      const t = (it.title || it.Name || it.name || '').toString().trim();
      if(t) savedTitles.add(t);
    });
    document.querySelectorAll('.bookmarkbtn').forEach(btn => {
      const card = btn.closest('.card__Container') || btn.closest('.card__Box') || btn.closest('.tr-must-see-item') || btn.closest('.tr-also-at-item');
      const titleNode = card && card.querySelector('.card__Subtitle a, .card__Subtitle h5, h5');
      const title = titleNode ? titleNode.textContent.trim() : '';
      const img = btn.querySelector('img');
      if(img && !btn.dataset.origSrc){ btn.dataset.origSrc = img.src; }

      // Try to resolve a stable item id from DOM
      const domIds = [];
      if(btn.dataset.itemId) domIds.push(btn.dataset.itemId);
      if(card){
        const attrs = ['data-item-id','data-id','data-sight-id','data-hotel-id','data-restaurant-id'];
        attrs.forEach(a => { const v = card.getAttribute(a); if(v) domIds.push(v); });
      }
      const matchedById = domIds.map(String).some(v => savedIds.has(v));

      if(matchedById || (title && savedTitles.has(title))){
        btn.classList.add('saved');
        if(img){ img.src = 'https://www.travell.co/explore/images/icons/check-icon.svg'; }
      } else {
        btn.classList.remove('saved');
        if(img && btn.dataset.origSrc){ img.src = btn.dataset.origSrc; }
      }
    });
  }

  // Safety: delegate clicks in case inline onclick is blocked by browser/optimizer
  document.addEventListener('click', function(e){
    const btn = e.target.closest && e.target.closest('.bookmarkbtn');
    if(btn){
      e.preventDefault();
      e.stopPropagation();
      try{ window.toggleBookmark(btn); }catch(_){ /* ignore */ }
    }
  }, true);

  // Initialize Saved Items section: hide by default, then load summary
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', function(){
      const w = document.querySelector('.top__Save');
      if(w){ w.style.display = 'none'; }
      loadSavedSummary();
    });
  } else {
    const w = document.querySelector('.top__Save');
    if(w){ w.style.display = 'none'; }
    loadSavedSummary();
  }

  // Arrow toggle for Saved Items (hide/show images row)
  document.addEventListener('DOMContentLoaded', function(){
    const wrap = document.querySelector('.top__Save');
    if(!wrap) return;
    // Prevent double-binding if this script is included twice
    if(wrap.dataset.savedSetup === '1') return;
    wrap.dataset.savedSetup = '1';
    const arrowBox = wrap.querySelector('.Arrow__icon');
    const arrowImg = arrowBox && arrowBox.querySelector('img');
    const content = wrap.querySelector('.save__timeLeft');

    // Initial state: collapsed (hidden)
    if(content){ content.style.display = 'none'; }
    wrap.classList.remove('open');
    if(arrowBox){
      arrowBox.style.cursor = 'pointer';
      arrowBox.setAttribute('role','button');
      arrowBox.setAttribute('tabindex','0');
      arrowBox.setAttribute('aria-expanded','false');
    }
    if(arrowImg){ arrowImg.style.transition = 'transform 150ms ease'; arrowImg.style.transform = 'rotate(0deg)'; }

    function setState(open){
      if(!content) return;
      if(open){
        content.style.display = 'flex';
        wrap.classList.add('open');
        if(arrowImg){ arrowImg.style.transform = 'rotate(180deg)'; }
        if(arrowBox){ arrowBox.setAttribute('aria-expanded','true'); }
      } else {
        content.style.display = 'none';
        wrap.classList.remove('open');
        if(arrowImg){ arrowImg.style.transform = 'rotate(0deg)'; }
        if(arrowBox){ arrowBox.setAttribute('aria-expanded','false'); }
      }
    }

    function toggle(){
      const opening = !wrap.classList.contains('open');
      setState(opening);
      // When opening via arrow/header click, show inline cards list (not modal)
      try {
        const holder = ensureSavedCardsHolder(wrap);
        if(opening){
          // Build a stable key from current items; skip re-render if same
          const items = Array.isArray(window.SAVED_ITEMS) ? window.SAVED_ITEMS : [];
          const key = JSON.stringify(dedupeItems(items).map(it => (it.id||it.Id||it.SightId||it.slug||it.Name||it.name||it.title||'')+':'+(getItemImage(it)||'')));
          if(holder.dataset.renderKey !== key){
            renderSavedCardsList(holder, items);
            holder.dataset.renderKey = key;
          }
          holder.style.display = 'block';
        } else {
          // Also clear children to avoid any ghost duplicates from earlier sessions
          while(holder.firstChild){ holder.removeChild(holder.firstChild); }
          holder.dataset.renderKey = '';
          holder.style.display = 'none';
        }
      } catch(_){ }
    }

    // Only the arrow toggles open/close
    const headerClickTargets = [arrowBox].filter(Boolean);
    headerClickTargets.forEach(el => {
      el.style.cursor = 'pointer';
      el.addEventListener('click', function(e){
        e.stopPropagation();
        // If this Arrow is an anchor with href, allow navigation instead of toggling
        if (el.tagName === 'A' && el.hasAttribute('href')) {
          return; // let the browser follow the link
        }
        // If clicking inside content while it's already open, don't toggle.
        // But allow click to toggle when collapsed.
        if(wrap.classList.contains('open') && content && content.contains(e.target)) return;
        e.preventDefault();
        toggle();
      });
      el.addEventListener('keydown', function(e){ if(e.key==='Enter' || e.key===' '){ e.preventDefault(); toggle(); } });
    });

    // Extra safety: event delegation if DOM updates later
    document.addEventListener('click', function(e){
      const icon = e.target.closest && e.target.closest('.top__Save .Arrow__icon');
      if(icon){
        // If the icon is an anchor link, do not intercept
        if (icon.tagName === 'A' && icon.hasAttribute('href')) { return; }
        e.preventDefault();
        toggle();
      }
    });

    // Allow clicking the Saved Items gallery thumbnails to toggle too
    const gallery = wrap.querySelector('.gallery-wrapper');
    if(gallery){
      gallery.style.cursor = 'pointer';
      gallery.addEventListener('click', function(e){
        if(e.target.closest('a')) return;
        e.preventDefault();
        // Do NOT toggle when collapsed; only arrow controls open/close.
        if(!wrap.classList.contains('open')){ return; }
        // When already open, clicking gallery opens modal
        try{ openSavedGalleryModal(); }catch(_){ toast('Could not open gallery.','error'); }
      });
    }
  });

  // Ensure a holder exists below the Saved Items header to show card-wise list
  function ensureSavedCardsHolder(wrap){
    let holder = wrap.querySelector('.saved-cards-list');
    if(!holder){
      holder = document.createElement('div');
      holder.className = 'saved-cards-list';
      holder.style.cssText = 'display:none;margin:10px 0 0 0;padding:8px 0;border-top:1px solid #eee;';
      // Remove any stray holders that might have been inserted outside wrap previously
      try{
        document.querySelectorAll('.saved-cards-list').forEach(el => {
          if(el.closest('.top__Save') !== wrap){ el.parentNode && el.parentNode.removeChild(el); }
        });
      }catch(_){ }
      // Insert INSIDE wrap, immediately after the header row (.space-topbttom)
      const headerRow = wrap.querySelector('.space-topbttom');
      if(headerRow){
        headerRow.insertAdjacentElement('afterend', holder);
      } else {
        wrap.appendChild(holder);
      }
    }
    return holder;
  }

  // Render saved items as small cards within the section
  function renderSavedCardsList(holder, items){
    if(!holder) return;
    // Clear existing
    while(holder.firstChild){ holder.removeChild(holder.firstChild); }
    const list = dedupeItems(Array.isArray(items) ? items : []);
    if(list.length === 0){
      const p = document.createElement('p'); p.textContent = 'No saved items yet.'; p.style.margin = '8px 0 0 0'; p.style.color = '#666';
      holder.appendChild(p); return;
    }
    const grid = document.createElement('div');
    grid.style.cssText = 'display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;';
    const makeCard = (it)=>{
      const card = document.createElement('div');
      card.style.cssText = 'display:flex;gap:10px;align-items:center;background:#fff;border:1px solid #eee;border-radius:10px;padding:8px;box-shadow:0 1px 2px rgba(0,0,0,.03)';
      const imgWrap = document.createElement('div'); imgWrap.style.cssText='width:64px;height:64px;flex:0 0 64px;border-radius:8px;overflow:hidden;background:#f3f4f6;border:1px solid #eee;';
      const img = document.createElement('img');
      const resolved = getItemImage(it);
      img.src = resolved || "<?php echo e(asset('explore/images/experience-2.png')); ?>"; img.alt='';
      img.style.cssText='width:100%;height:100%;object-fit:cover;display:block;';
      img.loading='lazy'; img.referrerPolicy='no-referrer';
      imgWrap.appendChild(img);
      const meta = document.createElement('div'); meta.style.cssText='min-width:0;';
      const title = document.createElement('div'); title.textContent = (it && (it.title || it.Name || it.name)) ? (it.title || it.Name || it.name) : 'Saved item'; title.style.cssText='font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
      meta.appendChild(title);
      card.appendChild(imgWrap); card.appendChild(meta);
      return card;
    };
    list.forEach(it => grid.appendChild(makeCard(it)));
    holder.appendChild(grid);
    try { console.debug('Saved inline cards rendered', list.map(i=>({title:i?.title||i?.Name||i?.name, img:getItemImage(i)}))); } catch(_){ }
  }

  window.toggleBookmark = async function(btn){
    try{ console.log('travell: toggleBookmark clicked'); }catch(_){ try{ alert('Saving...'); }catch(__){} }
    try{
      let card = btn.closest('.card__Container');
      if(!card){
        // Fallbacks: different templates may wrap differently
        card = btn.closest('.card__Box') || btn.closest('.tr-must-see-item') || btn.closest('.tr-also-at-item') || btn.parentElement?.parentElement || btn.parentElement;
      }
      if(!card){
        toast('Could not find card container.','error');
        return;
      }
      let tripId = document.querySelector('meta[name="trip-id"]')?.getAttribute('content') || '';
      let listId = document.querySelector('meta[name="list-id"]')?.getAttribute('content') || '';

      btn.disabled = true;
      btn.classList.add('saving');

      if(!tripId || !listId){
        try {
          const resolved = await resolveTripAndList();
          tripId = resolved.tripId; listId = resolved.listId;
        } catch(err){
          if(err && err.message === 'auth'){
            toast('Please sign in to save items.','warn');
            if(!openSignInModal()){
              /* fallback no-op if modal not available */
            }
          }else if(err && err.message === 'no-trip'){
            toast('Create a trip first to save items.','warn');
          }else{
            toast('Create a trip first to save items.','warn');
          }
          return false;
        }
      }

      const titleNode = card.querySelector('.card__Subtitle a, .card__Subtitle h5, h5');
      const title = (titleNode ? titleNode.textContent.trim() : 'Item');
      const lat = card.getAttribute('data-lat');
      const lng = card.getAttribute('data-lng');
      // Extract identifiers from DOM
      const domIds = {
        item_id: btn.dataset.itemId || card.getAttribute('data-item-id') || card.getAttribute('data-id') || null,
        sight_id: card.getAttribute('data-sight-id') || null,
        hotel_id: card.getAttribute('data-hotel-id') || null,
        restaurant_id: card.getAttribute('data-restaurant-id') || null,
      };

      const isSaved = btn.classList.contains('saved');

      if(!isSaved){
        // SAVE (optimistic UI)
        const origImg = btn.querySelector('img');
        if(origImg && !btn.dataset.origSrc){ btn.dataset.origSrc = origImg.src; }
        // optimistic visual feedback
        const img = btn.querySelector('img');
        if(img){ img.src = 'https://www.travell.co/explore/images/icons/check-icon.svg'; }
        btn.classList.add('saved');
        const payload = {
          trip_id: tripId,
          list_id: listId,
          // Prefer IDs for server-side uniqueness; keep title for display
          item_id: domIds.item_id || null,
          sight_id: domIds.sight_id || null,
          hotel_id: domIds.hotel_id || null,
          restaurant_id: domIds.restaurant_id || null,
          title: title,
          address: null,
          lat: lat ? Number(lat) : null,
          lng: lng ? Number(lng) : null,
        };
        fetch(ADD_ITEM_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': getCsrf(),'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
          body: JSON.stringify(payload),
          credentials:'same-origin'
        }).then(async (r)=>{
          let data={}; try{ data = await r.json(); }catch(e){}
          return { ok:r.ok, status:r.status, data };
        }).then(({ok,status,data})=>{
          if(isUnauthPayload(data)){
            // revert optimistic and trigger auth
            const img = btn.querySelector('img');
            if(img && btn.dataset.origSrc){ img.src = btn.dataset.origSrc; }
            btn.classList.remove('saved');
            toast('Please sign in to save items.','warn');
            if(!openSignInModal()){}
          } else if(ok && data && data.ok){
            // Capture canonical item id returned by backend
            if(data.item){
              const iid = data.item.item_id || data.item.id || data.item.Id || null;
              if(iid){ btn.dataset.itemId = iid; }
            }
            bumpSavedSummary(+1);
            toast('Saved to trip','ok');
            // Update in-memory items and UI immediately
            try{
              const wrap = document.querySelector('.top__Save');
              let items = Array.isArray(window.SAVED_ITEMS) ? window.SAVED_ITEMS.slice() : [];
              const cardImg = card.querySelector('img');
              const imgSrc = cardImg ? cardImg.src : '';
              const newItem = { title: title, image_url: imgSrc, item_id: btn.dataset.itemId || domIds.item_id || domIds.sight_id || domIds.hotel_id || domIds.restaurant_id };
              const exists = items.some(it => {
                const itId = it?.item_id || it?.id || it?.Id || it?.SightId || it?.HotelId || it?.RestaurantId;
                if(itId && newItem.item_id){ return String(itId) === String(newItem.item_id); }
                return (it && (it.title||it.Name||it.name)) === title;
              });
              if(!exists){ items.push(newItem); }
              window.SAVED_ITEMS = items;
              if(wrap){
                wrap.style.display = '';
                const holder = ensureSavedCardsHolder(wrap);
                if(wrap.classList.contains('open')){ renderSavedCardsList(holder, items); }
                try{ renderSavedGallery(items, wrap.querySelector('.gallery-wrapper')); }catch(_){ }
              }
            }catch(_){ }

          }else if(status===401){
            // revert optimistic
            const img = btn.querySelector('img');
            if(img && btn.dataset.origSrc){ img.src = btn.dataset.origSrc; }
            btn.classList.remove('saved');
            toast('Please sign in to save items.','warn');
            if(!openSignInModal()){
              /* fallback no-op if modal not available */
            }
          }else if(status===419){
            const img = btn.querySelector('img');
            if(img && btn.dataset.origSrc){ img.src = btn.dataset.origSrc; }
            btn.classList.remove('saved');
            toast('Session expired. Refresh the page.','error');
          }else if(status===404){
            const img = btn.querySelector('img');
            if(img && btn.dataset.origSrc){ img.src = btn.dataset.origSrc; }
            btn.classList.remove('saved');
            toast((data && data.message) ? data.message : 'Trip/List not found.','error');
          }else{
            const img = btn.querySelector('img');
            if(img && btn.dataset.origSrc){ img.src = btn.dataset.origSrc; }
            btn.classList.remove('saved');
            toast((data && data.message) ? data.message : 'Could not save item.','error');
          }
        }).catch(()=>{ toast('Network error. Try again.','error'); })
        .finally(()=>{ btn.disabled=false; btn.classList.remove('saving'); });
      } else {
        // UNSAVE (optimistic UI)
        const img = btn.querySelector('img');
        // optimistic revert to original icon
        if(img && btn.dataset.origSrc){ img.src = btn.dataset.origSrc; }
        btn.classList.remove('saved');
        const payload = {
          trip_id: tripId,
          list_id: listId,
          // Prefer the exact id used when saving
          item_id: btn.dataset.itemId || domIds.item_id || domIds.sight_id || domIds.hotel_id || domIds.restaurant_id || null,
          title: title
        };
        fetch(REMOVE_ITEM_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': getCsrf(),'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
          body: JSON.stringify(payload),
          credentials:'same-origin'
        }).then(async (r)=>{ let data={}; try{ data=await r.json(); }catch(e){} return { ok:r.ok, status:r.status, data }; })
        .then(({ok,status,data})=>{
          if(isUnauthPayload(data)){
            // flip back to saved and prompt login
            const img = btn.querySelector('img');
            if(img){ img.src = 'https://www.travell.co/explore/images/icons/check-icon.svg'; }
            btn.classList.add('saved');
            toast('Please sign in.','warn');
            if(!openSignInModal()){}
          } else if(ok && data && data.ok){
            bumpSavedSummary(-1);
            toast('Removed from saved','ok');
            // Update in-memory items and UI immediately
            try{
              const wrap = document.querySelector('.top__Save');
              let items = Array.isArray(window.SAVED_ITEMS) ? window.SAVED_ITEMS.slice() : [];
              const removeId = payload.item_id ? String(payload.item_id) : null;
              items = items.filter(it => {
                const itId = it?.item_id || it?.id || it?.Id || it?.SightId || it?.HotelId || it?.RestaurantId;
                if(removeId && itId){ return String(itId) !== removeId; }
                return (it && (it.title||it.Name||it.name)) !== title;
              });
              window.SAVED_ITEMS = items;
              if(wrap){
                const holder = ensureSavedCardsHolder(wrap);
                if(wrap.classList.contains('open')){ renderSavedCardsList(holder, items); }
                try{ renderSavedGallery(items, wrap.querySelector('.gallery-wrapper')); }catch(_){ }
              }
            }catch(_){ }
          } else if(status===401){
            // revert back to saved state since request failed
            const img = btn.querySelector('img');
            if(img){ img.src = 'https://www.travell.co/explore/images/icons/check-icon.svg'; }
            btn.classList.add('saved');
            toast('Please sign in.','warn');
            if(!openSignInModal()){
              /* fallback no-op if modal not available */
            }
          } else if(status===419){
            const img = btn.querySelector('img');
            if(img){ img.src = 'https://www.travell.co/explore/images/icons/check-icon.svg'; }
            btn.classList.add('saved');
            toast('Session expired. Refresh the page.','error');
          } else if(status===404){
            const img = btn.querySelector('img');
            if(img){ img.src = 'https://www.travell.co/explore/images/icons/check-icon.svg'; }
            btn.classList.add('saved');
            toast((data && data.message) ? data.message : 'Item not found.','error');
          } else {
            const img = btn.querySelector('img');
            if(img){ img.src = 'https://www.travell.co/explore/images/icons/check-icon.svg'; }
            btn.classList.add('saved');
            toast((data && data.message) ? data.message : 'Could not remove item.','error');
          }
        }).catch(()=>{ toast('Network error. Try again.','error'); })
        .finally(()=>{ btn.disabled=false; btn.classList.remove('saving'); });
      }

    }catch(e){
      toast('Unexpected error.','error');
    }
    return false;
  }
})();
</script>
<!-- Note: We intentionally removed the auto-fetch summary on page load per requirement. -->
  <style>
    /* Results Loader Styles *
    /* .tr-results-loader {
      min-height: 300px;
      width: 100%;
      background-color: rgba(255, 255, 255, 0.8);
      z-index: 999;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 20px 0;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .tr-loader-container {
      text-align: center;
    }
    
    .tr-spinner {
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid #28965A;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    } */
    
    /* Hide the loader when results are loaded */
    .results-loaded .tr-results-loader {
      display: none;
    }
  </style>
 
 <script type="application/ld+json">
<?php
      // Define $currentUrl if it doesn't exist
if (!isset($currentUrl)) {
    $currentUrl = url()->current();
}

// Define $homeUrl if it doesn't exist
if (!isset($homeUrl)) {
    $homeUrl = url('/');
}
// Pre-build the JSON structure to avoid syntax errors with Blade conditionals
$jsonData = [
    "@context" => "https://schema.org",
    "@graph" => [
        // WebPage
        [
            "@type" => "WebPage",
            "@id" => $currentUrl . "#webpage",
            "name" => $title,
            "url" => $currentUrl,
            "isPartOf" => [
                "@id" => "https://www.travell.co/#website"
            ],
            "breadcrumb" => [
                "@id" => $currentUrl . "#breadcrumb"
            ],
            "inLanguage" => "en"
        ],
        
        // BreadcrumbList
        [
            "@type" => "BreadcrumbList",
            "@id" => $currentUrl . "#breadcrumb",
            "itemListElement" => []
        ],
        
        // TouristDestination
        [
            "@type" => "LocalBusiness",
            "name" => !empty($breadcumb) ? $breadcumb[0]->LName : $lname,
            "url" => $currentUrl
        ],
        
        // WebSite
        [
            "@type" => "WebSite",
            "@id" => "https://www.travell.co/#website",
            "url" => $homeUrl,
            "name" => "Travell",
            "publisher" => [
                "@id" => "https://www.travell.co/#organization"
            ]
        ],
        
        // Organization
        [
            "@type" => "Organization",
            "@id" => "https://www.travell.co/#organization",
            "name" => "Travell",
            "url" => $homeUrl,
            "logo" => asset('frontend/images/logo.png')
        ]
    ]
];

// Build breadcrumb items
$breadcrumbItems = [];

// Home
$breadcrumbItems[] = [
    "@type" => "ListItem",
    "position" => 1,
    "name" => "Travell",
    "item" => $homeUrl
];

// Continent (if available)
$position = 2;
if (!empty($breadcumb) && isset($breadcumb[0]->ccName) && $breadcumb[0]->ccName != "") {
    $breadcrumbItems[] = [
        "@type" => "ListItem",
        "position" => $position++,
        "name" => $breadcumb[0]->ccName,
        "item" => route('explore_continent_list', [$breadcumb[0]->contid, $breadcumb[0]->ccName])
    ];
}

// Country
if (!empty($breadcumb) && isset($breadcumb[0]->CountryName) && $breadcumb[0]->CountryName != "") {
    $breadcrumbItems[] = [
        "@type" => "ListItem",
        "position" => $position++,
        "name" => $breadcumb[0]->CountryName,
        "item" => route('explore_country_list', [$breadcumb[0]->CountryId, $breadcumb[0]->cslug])
    ];
}

// Parent locations
if (!empty($locationPatents)) {
    foreach ($locationPatents as $location) {
        $breadcrumbItems[] = [
            "@type" => "ListItem",
            "position" => $position++,
            "name" => $location['Name'],
            "item" => route('search.results', [$location['LocationId'] . '-' . strtolower($location['slug'])])
        ];
    }
}

// Current location
$breadcrumbItems[] = [
    "@type" => "ListItem",
    "position" => $position,
    "name" => !empty($breadcumb) ? $breadcumb[0]->LName : $lname,
    "item" => $currentUrl
];

// Add breadcrumb items to the graph
$jsonData['@graph'][1]['itemListElement'] = $breadcrumbItems;

// Add geo coordinates if available
if (isset($lat) && isset($lng)) {
    $jsonData['@graph'][2]['geo'] = [
        "@type" => "GeoCoordinates",
        "latitude" => $lat,
        "longitude" => $lng
    ];
}

// Add description if available
if (isset($description)) {
    $jsonData['@graph'][2]['description'] = $description;
}

// Add containedIn for country (using containedIn instead of containedInPlace)
if (!empty($breadcumb) && isset($breadcumb[0]->CountryName)) {
    $jsonData['@graph'][2]['containedIn'] = [
        "@type" => "Country",
        "name" => $breadcumb[0]->CountryName
    ];
}

// Add ItemList if searchresults exist
if (isset($searchresults) && count($searchresults) > 0) {
    $itemList = [
        "@type" => "ItemList",
        "name" => "Things to Do in " . (!empty($breadcumb) ? $breadcumb[0]->LName : $lname),
        "itemListOrder" => "https://schema.org/ItemListOrderAscending",
        "numberOfItems" => count($searchresults),
        "itemListElement" => []
    ];
    
    foreach ($searchresults as $key => $attraction) {
        // Get attraction name - use Title property if available
        $attractionName = isset($attraction->Title) ? $attraction->Title : 
                         (isset($attraction->Name) ? $attraction->Name : 
                         (isset($attraction->name) ? $attraction->name : 
                         (!empty($breadcumb) ? $breadcumb[0]->LName . ' Attraction' : 'Tourist Attraction')));
        
        // Get attraction URL using the appropriate format
        $attractionUrl = isset($attraction->slugid) && isset($attraction->SightId) && isset($attraction->Slug) ? 
                        asset('at-'.$attraction->slugid.'-'.$attraction->SightId.'-'.strtolower($attraction->Slug)) : 
                        (isset($attraction->SightId) && isset($attraction->slug) ? 
                        route('sight.details', [$attraction->SightId, $attraction->slug]) : 
                        (isset($attraction->sightId) && isset($attraction->Slug) ? 
                        route('sight.details', [$attraction->sightId, $attraction->Slug]) : $currentUrl));
        
        $item = [
            "@type" => "ListItem",
            "position" => $key + 1,
            "item" => [
                "@type" => "LocalBusiness",
                "name" => $attractionName,
                "url" => $attractionUrl,
                "image" => isset($attraction->MainImage) ? asset('storage/sights/' . $attraction->MainImage) : '',
                "address" => [
                    "@type" => "PostalAddress",
                    "addressLocality" => !empty($breadcumb) ? $breadcumb[0]->LName : $lname,
                    "addressCountry" => !empty($breadcumb) && isset($breadcumb[0]->CountryName) ? $breadcumb[0]->CountryName : ''
                ],
                "priceRange" => "$$",
                "telephone" => isset($attraction->Phone) ? $attraction->Phone : ''
            ]
        ];
        
        if (isset($attraction->Latitude) && isset($attraction->Longitude)) {
            $item['item']['geo'] = [
                "@type" => "GeoCoordinates",
                "latitude" => $attraction->Latitude,
                "longitude" => $attraction->Longitude
            ];
        }
        
        $itemList['itemListElement'][] = $item;
    }
    
    $jsonData['@graph'][] = $itemList;
}

// Add FAQPage only if FAQs section is visible and FAQs exist
$faqSectionVisible = isset($showFAQs) ? $showFAQs : false;

if ($faqSectionVisible && isset($faqs) && count($faqs) > 0) {
    $faqPage = [
        "@type" => "FAQPage",
        "mainEntity" => []
    ];
    
    foreach ($faqs as $faq) {
        $faqPage['mainEntity'][] = [
            "@type" => "Question",
            "name" => $faq->question,
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => $faq->answer
            ]
        ];
    }
    
    $jsonData['@graph'][] = $faqPage;
}

// Output the JSON with proper encoding
echo json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>
</script>

</head>

<body>
  <!-- No full-page preloader -->
  <!--HEADER-->
  <?php echo $__env->make('frontend.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <!-- Mobile Navigation-->
  <?php echo $__env->make('frontend.mobile_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="tr-explore-listing-section">
    <div class="container">
        <div class="tr-explore-listing">
            <!-- Dynamic Content Section -->
        
            <!-- End Dynamic Content Section -->
            
            <div class="tr-explore-left-section">
                <!-- Hidden inputs and spans for dynamic data -->
                <input type="hidden" id="shown-attraction-ids" value="<?php echo e(!empty($searchresults) ? (is_object($searchresults) && method_exists($searchresults, 'pluck') ? implode(',', $searchresults->pluck('SightId')->toArray()) : implode(',', collect($searchresults)->pluck('SightId')->toArray())) : ''); ?>">
                <?php
                    $url = request()->route('id');
                    $parts = explode('-', $url, 2);
                    $lastPart = $parts[1] ?? '';
                    
                    // Define location_id
                    $location_id = null;
                    if (!empty($searchresults) && count($searchresults) > 0 && isset($searchresults[0]->LocationId)) {
                        $location_id = $searchresults[0]->LocationId;
                    } elseif (!empty($locationID)) {
                        $location_id = $locationID;
                    }

                    $location_slugid = null;
                    if (!empty($lastPart) && preg_match('/^(\d+)/', $lastPart, $m)) {
                        $location_slugid = $m[1];
                    }

                    if (empty($location_id) && !empty($location_slugid)) {
                        try {
                            $location_id = DB::table('Location')->where('slugid', $location_slugid)->value('LocationId');
                        } catch (\Throwable $e) {
                            $location_id = $location_id;
                        }
                    }
                    $current_location_id = $location_id;
                    
                    // Define location name - with proper priority and no static fallbacks
                    $lname = '';
                    
                    // Priority 1: Use location_name from controller if available
                    if (isset($location_name) && !empty($location_name)) {
                        $lname = $location_name;
                    }
                    // Priority 2: Get from neighborhoods table by slugid
                    elseif (isset($neighborhoods) && !empty($lastPart)) {
                        $neighborhoodMatch = $neighborhoods->where('slugid', $lastPart)->first();
                        if ($neighborhoodMatch && isset($neighborhoodMatch->Name)) {
                            $lname = $neighborhoodMatch->Name;
                        }
                    }
                    // Priority 3: Get from search results Area/Location
                    elseif (!empty($searchresults) && count($searchresults) > 0) {
                        if (isset($searchresults[0]->Area) && !empty($searchresults[0]->Area)) {
                            $lname = $searchresults[0]->Area;
                        } elseif (isset($searchresults[0]->Location) && !empty($searchresults[0]->Location)) {
                            $lname = $searchresults[0]->Location;
                        }
                    }
                    // Priority 4: Get from URL
                    elseif (!empty($lastPart)) {
                        // Convert slug to readable name (replace hyphens with spaces and capitalize words)
                        $lname = ucwords(str_replace('-', ' ', $lastPart));
                    }
                    
                    // If still empty (which should never happen with the above checks), use the current route name
                    if (empty($lname)) {
                        $lname = ucwords(str_replace('-', ' ', request()->route()->getName() ?? 'Current Location'));
                    }
                ?>
                <span id="locid" class="d-none"><?php echo e($location_id); ?></span>
       			<span id="location_name" class="d-none"><?php echo e($lname); ?></span>
                <span id="slug" class="d-none"><?php echo e($lastPart); ?></span>
                <span class="d-none sightlist">sightlist</span>

                <div class="tr-title-section">
                    <h2 class="tr-title">
                        <?php if(request()->is('*xpat*')): ?>
                            Places to Visit in <br/><?php echo e($lname); ?>

                        <?php elseif($top_attractions == 1): ?>
                            Top Attractions in <?php echo e($lname); ?>

                        <?php elseif(!empty($category_name)): ?>
                            <?php echo e($category_name); ?> in <?php echo e($lname); ?>

                        <?php elseif(request()->is('*sqx*') && $catheading != ''): ?>
                            <?php
                                $nearbyName = $catheading;
                                $prefix = 'Attractions near ';
                                if (strpos($nearbyName, $prefix) === 0) {
                                    $nearbyName = substr($nearbyName, strlen($prefix));
                                }
                            ?>
                            Things to do near <?php echo e($nearbyName); ?> in <?php echo e($lname); ?>

                        <?php elseif($catheading != ''): ?>
                            Top <?php if($totalCountResults != 0): ?> <?php echo e($totalCountResults); ?> <?php endif; ?>
                            <?php if($catheading == 'mustsee'): ?> Attractions <?php else: ?> <?php echo e($catheading); ?> <?php endif; ?> in <?php echo e($lname); ?>

                            <?php if($location_parent_name != ''): ?>, <?php echo e($location_parent_name); ?> <?php endif; ?> with Travell
                        <?php else: ?>
                            Things to do in <br/><?php echo e($lname); ?> <?php if($location_parent_name != ''): ?>, <?php echo e($location_parent_name); ?> <?php endif; ?>
                        <?php endif; ?>
                    </h2>
                    
                </div>
                <?php if($totalCountResults != 0): ?>
                    <div class="tr-total-search">
                        <?php if(request()->is('*sqx*') && $catheading != ''): ?>
                            <?php
                                $nearbyName = $catheading;
                                $prefix = 'Attractions near ';
                                if (strpos($nearbyName, $prefix) === 0) {
                                    $nearbyName = substr($nearbyName, strlen($prefix));
                                }
                            ?>
                            We found<p><?php echo e($totalCountResults); ?> Attractions</p> near <?php echo e($nearbyName); ?> in <?php echo e($lname); ?>

                        <?php else: ?>
                            We found<p><?php echo e($totalCountResults); ?> Attractions</p> found in <?php echo e($lname); ?>

                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="tr-total-search tr-explore-listing-search" style="display: none;">
                    <button type="button" class="btn-close back-page-btn">Back Page Button</button>
                    <div>Showing results for <strong>Pizza</strong></div>
                </div>

                <!-- No data message -->
                

                <div class="tr-explore-search">
                    <div class="tr-explore-search-feild">
                        <div class="tr-search-field">
                            Search for
                            <div class="tr-search-by-category">
                                <?php if(!empty($getSightCat)): ?>
                                    <?php $__currentLoopData = $getSightCat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $getSightCats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>“<?php echo e($getSightCats->Title); ?>”</div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div>“Restaurants”</div>
                                    <div>“Museum”</div>
                                    <div>“Historical Monument”</div>
                                    <div>“Experience”</div>
                                    <div>“Market”</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button type="button" class="tr-btn">Search</button>
                    </div>
                    <div class="tr-explore-search-filter" style="display: none;">
                        <button type="button" class="tr-filter-btn">
                            <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.0837 2.875H1.91699L9.58366 11.9408V18.2083L13.417 20.125V11.9408L21.0837 2.875Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <div class="tr-explore-search-modal">
                        <button type="button" class="btn-close">Back Button</button>
                        <div class="tr-explore-search-feild">
                            <input type="text" name="" class="tr-search-field serch_sights" id="serch_sights" placeholder="Search for “Restaurants”" autocomplete="off">
                            <span class="res_type d-none" id="res_type"></span>
                            <button type="submit" class="tr-btn tr-search-icon" id="serch_sightsdata">Search</button>
                        </div>
                        <div class="tr-recent-searchs-modal tr-custom-scrollbar search-result">
                            <div class="tr-search-lists">
                                <?php if(!empty($searchresults) && count($searchresults) > 0): ?>
                                    <?php $__currentLoopData = $searchresults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                       <?php
                                            $recentItemImage = asset('frontend/hotel-detail/images/Hotel lobby-image.png');
                                            if (isset($sightImages) && !$sightImages->isEmpty()) {
                                                foreach ($sightImages as $sImage) {
                                                    if (isset($recentItem->SightId) && $sImage->Sightid == $recentItem->SightId && !str_contains($sImage->Image, 'vid')) {
                                                        $recentItemImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=460&height=300";
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <div class="tr-search-list">
                                            <div class="tr-image">
                                                <img 
                                                    src="<?php echo e($recentItemImage); ?>" 
                                                    srcset="<?php echo e(str_replace('width=460', 'width=320', $recentItemImage)); ?> 320w,
                                                            <?php echo e(str_replace('width=460', 'width=480', $recentItemImage)); ?> 480w,
                                                            <?php echo e($recentItemImage); ?> 460w"
                                                    sizes="(max-width: 480px) 320px, (max-width: 768px) 480px, 460px"
                                                    alt="<?php echo e($recentItem->Title ?? 'Attraction'); ?>" 
                                                    title="<?php echo e($recentItem->Title ?? 'Attraction'); ?>" 
                                                    <?php if($loop->first): ?>
                                                    fetchpriority="high" 
                                                    loading="eager"
                                                    <?php else: ?>
                                                    loading="lazy"
                                                    <?php endif; ?>
                                                    width="460" 
                                                    height="300">
                                            </div>
                                            <div class="tr-details">
                                                <div class="tr-category">Attraction</div>
                                                <div class="tr-title"><?php echo e($recentItem->Title ?? 'Attraction'); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                            <div class="tr-popular-attractions">
                                <h4>Popular Attractions</h4>
                                <div class="tr-popular-attractions-lists">
                                    <?php if(!empty($searchresults) && count($searchresults) > 0): ?>
                                        <?php $__currentLoopData = array_slice($searchresults, 0, 6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $popItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $popItemImage = asset('frontend/hotel-detail/images/Hotel lobby-image.png');
                                                if (isset($sightImages) && !$sightImages->isEmpty()) {
                                                    foreach ($sightImages as $sImage) {
                                                        if ($sImage->Sightid == $popItem->SightId && !str_contains($sImage->Image, 'vid')) {
                                                            $popItemImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=460&height=300";
                                                            break;
                                                        }
                                                    }
                                                }
                                            ?>
                                            <div class="tr-popular-attractions-list">
                                                <div class="tr-details">
                                                    <div class="tr-image">
                                                        <a href="<?php echo e(asset('at-'.$popItem->slugid.'-'.$popItem->SightId.'-'.strtolower($popItem->Slug))); ?>">
                                                            <img 
                                                                src="<?php echo e($popItemImage); ?>" 
                                                                srcset="<?php echo e(str_replace('width=460', 'width=320', $popItemImage)); ?> 320w,
                                                                        <?php echo e(str_replace('width=460', 'width=480', $popItemImage)); ?> 480w,
                                                                        <?php echo e($popItemImage); ?> 460w"
                                                                sizes="(max-width: 480px) 320px, (max-width: 768px) 480px, 460px"
                                                                alt="<?php echo e($popItem->Title); ?>" 
                                                                title="<?php echo e($popItem->Title); ?>" 
                                                                <?php if($loop->first): ?>
                                                                fetchpriority="high" 
                                                                loading="eager"
                                                                <?php else: ?>
                                                                loading="lazy"
                                                                <?php endif; ?>
                                                                width="460" 
                                                                height="300">
                                                        </a>
                                                    </div>
                                                    <div class="tr-title">
                                                        <a href="<?php echo e(asset('at-'.$popItem->slugid.'-'.$popItem->SightId.'-'.strtolower($popItem->Slug))); ?>" title="<?php echo e($popItem->Title); ?>"><?php echo e($popItem->Title); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-container">
                    <div class="tab-buttons">
                        <button class="tab-btn <?php echo e(!request()->is('*xpat*') ? 'active' : ''); ?>" onclick="openTab('tab4')">For You</button>
                        <button class="tab-btn <?php echo e(request()->is('*xpat*') ? 'active' : ''); ?>" onclick="filterAttractions()">Attractions</button>
                        <button class="tab-btn" onclick="openTab('tab2')">Activities</button>
                        <button class="tab-btn" onclick="openTab('tab3')">Restaurants</button>
                    </div>
                    <div id="tab1" class="tab-content active">
                        <div class="tr-explore-filter">
                            <form class="tr-filter-lists tr-explore-filter-slider">
                                <?php if($ismustsee == 1): ?>
                                    <div class="tr-filter-list filter_sightbycat" data-catid="mustsee">
                                        <input type="checkbox" name="" id="filter1" class="filter" value="category1" <?php if(request()->session()->has('mustSee')): ?> checked <?php endif; ?>>
                                        <label for="filter1"><span>Must See</span></label>
                                    </div>
                                <?php endif; ?>
                                <?php if($rest_avail == 1): ?>
                                    <div class="tr-filter-list filter_sightbycat" data-catid="isrestaurant">
                                        <input type="checkbox" name="" id="filter_restaurant" class="filter" value="category1" <?php if(request()->session()->has('IsRestaurant')): ?> checked <?php endif; ?>>
                                        <label for="filter_restaurant"><span>Restaurants</span></label>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($getSightCat)): ?>
                                    <?php $__currentLoopData = $getSightCat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $getSightCatval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $categoryArray = [];
                                            foreach (request()->session()->all() as $key => $value) {
                                                if (str_starts_with($key, 'cat_')) {
                                                    $catInfo = explode('_', $value);
                                                    if (count($catInfo) === 2 && !empty($catInfo[0]) && !empty($catInfo[1])) {
                                                        $categoryArray[] = [
                                                            'name' => $catInfo[0],
                                                            'id' => $catInfo[1],
                                                        ];
                                                    }
                                                }
                                            }
                                        ?>
                                        <div class="tr-filter-list filter_sightbycat" data-name="<?php echo e($getSightCatval->Title); ?>" data-catid="<?php echo e($getSightCatval->CategoryId); ?>">
                                            <input type="checkbox" name="" id="filter_<?php echo e($getSightCatval->CategoryId); ?>" class="filter" value="category1"
                                                <?php if(!empty($categoryArray)): ?>
                                                    <?php $__currentLoopData = $categoryArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($category['name'] != '' && $category['name'] == $getSightCatval->Title): ?> checked <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>>
                                            <label for="filter_<?php echo e($getSightCatval->CategoryId); ?>"><span><?php echo e($getSightCatval->Title); ?></span></label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    <div class="top__Save px-3">
                        <h5>Saved Items</h5>
                    <div class="d-flex justify-content-between align-items-center space-topbttom">
                        <div class="save__timeLeft d-flex align-items-center">
                  <div class="gallery-wrapper"></div>
                    <div class="details__Savetime">
                        <h6><?php echo e($lname); ?></h6>
                        <p></p>
                    </div>
                    </div>
                    <a href="<?php echo e(route('trip.index')); ?>" class="Arrow__icon" title="Plan your trip">
                        <img width="20" src="<?php echo e(asset('explore/images/arrow-down-sign-to-navigate.png')); ?>" alt="Plan trip" loading="lazy">
                    </a>
                    </div>
                    </div>
                    <div id="tab2" class="tab-content">
                        <h2>Content for Tab 2</h2>
                        <p>This is the content displayed when Tab 2 is selected.</p>
                    </div>
                    <div id="tab3" class="tab-content">
                        <h2>Coming soon</h2>
                        <p>This is the content displayed when Tab 3 is selected.</p>
                    </div>
                    <div id="tab4" class="tab-content">
                        <h2>Coming soon</h2>
                        <p>This is the content displayed when Tab 4 is selected.</p>
                    </div>
                </div>

                <!-- Bottom Section (Unchanged from Current Code) -->
                <?php
                    // First, identify the must-see attraction to exclude it from the regular list
                    $mustSeeItem = collect($searchresults)
                        ->filter(function($item) use ($current_location_id) {
                            $isMustSee = (isset($item->MustSee) && $item->MustSee == 1) || (isset($item->IsMustSee) && $item->IsMustSee == 1);
                            return $current_location_id && isset($item->LocationId) ? $isMustSee && $item->LocationId == $current_location_id : $isMustSee;
                        })
                        ->first();
                    if (!$mustSeeItem) {
                        $mustSeeItem = $current_location_id ? collect($searchresults)
                            ->filter(function($item) use ($current_location_id) {
                                return isset($item->LocationId) && $item->LocationId == $current_location_id;
                            })
                            ->sortByDesc(function($item) {
                                return $item->Averagerating ?? 0;
                            })
                            ->first() : collect($searchresults)
                            ->sortByDesc(function($item) {
                                return $item->Averagerating ?? 0;
                            })
                            ->first();
                    }
                    $mustSeeItemId = isset($mustSeeItem) ? $mustSeeItem->SightId : null;
                ?>

                
                <?php if(!empty($structuredFeed) && count($structuredFeed) > 0): ?>
                    <?php $__currentLoopData = $structuredFeed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($feedItem['type'] === 'transit'): ?>
                            
                            <div class="tr-transit-card" style="padding: 16px; margin: 16px 0; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#007bff" stroke-width="2">
                                        <path d="M9 18l6-6-6-6"/>
                                    </svg>
                                    <span style="color: #495057; font-weight: 500;"><?php echo e($feedItem['data']['message'] ?? $feedItem['routing_note']); ?></span>
                                </div>
                            </div>
                        <?php elseif(in_array($feedItem['type'], ['attraction', 'experience', 'restaurant'])): ?>
                            <?php
                                $itemData = $feedItem['data'];
                                $stepOrder = $feedItem['step_order'];
                                $itemType = $feedItem['type']; // 'attraction', 'experience', or 'restaurant'
                                $displayType = $feedItem['display_type'] ?? 'card_large';
                                
                                // Get image - use from data or fetch from sightImages
                                $displayImage = asset('frontend/hotel-detail/images/Hotel lobby-image.png');
                                
                                // For experiences, use Img1 if available
                                if ($itemType === 'experience' && !empty($itemData['image'])) {
                                    $displayImage = $itemData['image'];
                                }
                                // For attractions, fetch from sightImages collection
                                elseif ($itemType === 'attraction' && isset($sightImages) && !$sightImages->isEmpty()) {
                                    foreach ($sightImages as $sImage) {
                                        if ($sImage->Sightid == $itemData['id'] && !str_contains($sImage->Image, 'vid')) {
                                            $displayImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=460&height=300";
                                            break;
                                        }
                                    }
                                }
                                // Fallback: use image from data if available
                                elseif (!empty($itemData['image'])) {
                                    $displayImage = $itemData['image'];
                                }
                            ?>
                            
                            <?php if($displayType === 'card_small'): ?>
                                
                                <?php
                                    $smallCardHref = $itemType === 'experience'
                                        ? route('experince', [($itemData['slugid'] ?? '').'-'.str_replace('exp_', '', $itemData['id']).'-'.($itemData['slug'] ?? '')])
                                        : ($itemType === 'restaurant'
                                            ? url('rd-'.($itemData['slugid'] ?? '').'-'.preg_replace('/[^0-9]/', '', $itemData['id']).'-'.($itemData['slug'] ?? ''))
                                            : url('at-'.($itemData['slugid'] ?? '').'-'.$itemData['id'].'-'.strtolower($itemData['slug'] ?? '')));

                                    $ratingRaw = $itemData['rating'] ?? null;
                                    $ratingPercent = null;
                                    if (is_numeric($ratingRaw)) {
                                        $r = (float) $ratingRaw;
                                        $ratingPercent = ($r <= 5) ? (int) round($r * 20) : (int) round($r);
                                    }
                                ?>

                                <div class="card__Container tier3__card" onmouseover="highlightMarker(this)" onmouseout="unhighlightMarker(this)" data-sid="<?php echo e($itemData['id']); ?>" data-type="<?php echo e($itemType); ?>" data-lat="<?php echo e($itemData['latitude'] ?? ''); ?>" data-lng="<?php echo e($itemData['longitude'] ?? ''); ?>" data-item-id="<?php echo e($itemData['id']); ?>" style="margin-bottom: 8px;">
                                    <div class="card__Box no__border" style="min-height: auto !important;">
                                        <div class="small__container" style="display: flex; gap: 12px; align-items: stretch;">
                                            <div class="img__Section" style="flex: 0 0 150px; max-width: 150px;">
                                                <a href="<?php echo e($smallCardHref); ?>" aria-label="<?php echo e($itemData['title']); ?>">
                                                    <img src="<?php echo e($displayImage); ?>" alt="<?php echo e($itemData['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                                                </a>
                                            </div>
                                            <div class="content__Section" style="flex: 1 1 auto; display: flex; justify-content: space-between; gap: 12px;">
                                                <div class="left__Content" style="min-width: 0;">
                                                    <p style="margin: 0 0 6px 0; font-size: 12px; color: #6c757d;"><?php echo e($itemData['category_title'] ?? 'Tickets'); ?></p>
                                                    <h4 style="margin: 0 0 8px 0; font-size: 16px; line-height: 1.2; font-weight: 600;">
                                                        <a href="<?php echo e($smallCardHref); ?>" style="color: inherit; text-decoration: none;"><?php echo e($itemData['title']); ?></a>
                                                    </h4>
                                                    <?php if(!empty($itemData['short_description'])): ?>
                                                        <?php
                                                            $words = str_word_count($itemData['short_description'], 1);
                                                            $truncated = implode(' ', array_slice($words, 0, 6)) . (count($words) > 6 ? '...' : '');
                                                        ?>
                                                        <p style="margin: 0; font-size: 12px; color: #6c757d;"><?php echo e($truncated); ?></p>
                                                    <?php else: ?>
                                                        <p style="margin: 0; font-size: 12px; color: #6c757d;">Time Needed 3-4 hours</p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="right__Content" style="flex: 0 0 auto; display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between;">
                                                    <div class="img" style="display: flex; gap: 8px;">
                                                        <button type="button" class="bookmarkbtn" onclick="toggleBookmark(this)" data-item-id="<?php echo e($itemData['id']); ?>" style="background: transparent; border: 0; padding: 0;">
                                                            <img src="<?php echo e(asset('explore/images/icons/plusnew.svg')); ?>" alt="Save" loading="lazy">
                                                        </button>
                                                    </div>
                                                    <div class="img__Inner" style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #111; font-weight: 600;">
                                                        <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="Rating" loading="lazy"></span>
                                                        <span><?php echo e($ratingPercent !== null ? $ratingPercent : 89); ?>%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                
                                <div class="card__Container" onmouseover="highlightMarker(this)" onmouseout="unhighlightMarker(this)" data-sid="<?php echo e($itemData['id']); ?>" data-type="<?php echo e($itemType); ?>" data-lat="<?php echo e($itemData['latitude'] ?? ''); ?>" data-lng="<?php echo e($itemData['longitude'] ?? ''); ?>" data-item-id="<?php echo e($itemData['id']); ?>">
                                <div class="card__Box">
                                    <div class="card__title">
                                      <h6><?php echo e($itemData['category_title'] ?? 'Attraction'); ?></h6>
                                    </div>
                                    <div class="card__Subtitle">
                                        <h5><?php echo e($itemData['title']); ?></h5>
                                        <button type="button" class="bookmarkbtn" onclick="toggleBookmark(this)" data-item-id="<?php echo e($itemData['id']); ?>">
                                            <img src="<?php echo e(asset('explore/images/icons/plusnew.svg')); ?>" alt="" loading="lazy">
                                        </button>
                                    </div>
                                    
                                  
                                    
                                    <div class="MustSeeAttraction">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <a href="<?php echo e($itemType === 'experience' ? route('experince', [($itemData['slugid'] ?? '').'-'.str_replace('exp_', '', $itemData['id']).'-'.($itemData['slug'] ?? '')]) : url('at-'.($itemData['slugid'] ?? '').'-'.$itemData['id'].'-'.strtolower($itemData['slug'] ?? ''))); ?>">
                                                    <img src="<?php echo e($displayImage); ?>" alt="<?php echo e($itemData['title']); ?>" onerror="this.onerror=null; this.src='<?php echo e(asset('frontend/hotel-detail/images/Hotel lobby-image.png')); ?>';" <?php if($loop->first): ?> fetchpriority="high" <?php else: ?> loading="lazy" <?php endif; ?>>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                      <div class="card__listSection">
                                        <div class="card__details">
                                            <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="" loading="lazy"></span>
                                            <span><?php echo e($itemData['rating'] ?? '89'); ?>%</span>
                                        </div>
                                        <div class="card__details">
                                            <span><img src="<?php echo e(asset('explore/images/icons/clock.svg')); ?>" alt="" loading="lazy"></span>
                                            <span>Open</span>
                                        </div>
                                        <div class="card__details">
                                            <span><img src="<?php echo e(asset('explore/images/icons/location.svg')); ?>" alt="" loading="lazy"></span>
                                            <span><?php echo e($lname ?? 'Location'); ?></span>
                                        </div>
                                    </div>

                                    <?php if(!empty($itemData['short_description'])): ?>
                                        <?php
                                            $words = str_word_count($itemData['short_description'], 1);
                                            $truncated = implode(' ', array_slice($words, 0, 15)) . (count($words) > 15 ? '...' : '');
                                        ?>
                                        <p class="card__description"><?php echo e($truncated); ?></p>
                                    <?php endif; ?>
                            
                            
                            <?php if(!empty($feedItem['also_at'])): ?>
                                <div class="also__AtSection">
                                    <div class="divider">
                                        <span>Also at</span>
                                    </div>
                                    <div class="tr-market-slider sliderNew">
                                        
                                        <?php $__currentLoopData = $feedItem['also_at']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alsoAtItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $itemEntityType = $alsoAtItem['entity_type'] ?? 'sight';
                                            ?>
                                            
                                            
                                            <?php if($itemEntityType === 'sight' || $itemEntityType === 'attraction'): ?>
                                                <?php
                                                    $nearbySight = $alsoAtItem;
                                                    $sightImage = 'https://www.travell.co.in/frontend/hotel-detail/images/Hotel%20lobby-image.png';
                                                    
                                                    // Use optimized lookup array for performance
                                                    if (isset($sightImageLookup) && isset($sightImageLookup[$nearbySight['id']])) {
                                                        foreach ($sightImageLookup[$nearbySight['id']] as $imgPath) {
                                                            if (!str_contains($imgPath, 'vid')) {
                                                                $sightImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$imgPath}&width=320&height=300";
                                                                break;
                                                            }
                                                        }
                                                    }
                                                ?>
                                                    <div class="tr-store">
                                                        <a href="<?php echo e(url('at-'.($nearbySight['slugid'] ?? '').'-'.$nearbySight['id'].'-'.strtolower($nearbySight['slug'] ?? ''))); ?>">
                                                            <img src="<?php echo e($sightImage); ?>" alt="<?php echo e($nearbySight['title']); ?>" onerror="this.onerror=null; this.src='https://www.travell.co.in/frontend/hotel-detail/images/Hotel%20lobby-image.png';" loading="lazy">
                                                            <div class="icon__slider">
                                                                <img src="<?php echo e(asset('explore/images/icons/bookmark.svg')); ?>" alt="Bookmark" loading="lazy">
                                                            </div>
                                                        </a>
                                                        <div class="also__AtSliderDetails">
                                                            <p>Nearby Attraction</p>
                                                            <div class="also__title">
                                                                <h4><?php echo e($nearbySight['title']); ?></h4>
                                                            </div>
                                                            <div class="card__listSection">
                                                                <div class="card__details">
                                                                    <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="Rating" loading="lazy"></span>
                                                                    <span><?php echo e($nearbySight['rating'] ?? '85'); ?>%</span>
                                                                </div>
                                                            </div>
                                                            <ul class="list__also">
                                                                <li>Tier <?php echo e($nearbySight['tier'] ?? 3); ?></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                            <?php endif; ?>
                                            
                                            
                                            <?php if($itemEntityType === 'experience'): ?>
                                                <?php
                                                    $nearbyExp = $alsoAtItem;
                                                    $expImage = 'https://www.travell.co.in/frontend/hotel-detail/images/Hotel%20lobby-image.png';
                                                    
                                                    // Experiences usually have Img1 directly
                                                    if (!empty($nearbyExp['image'])) {
                                                        $expImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url={$nearbyExp['image']}&width=320&height=300";
                                                    }
                                                ?>
                                                <div class="tr-store">
                                                    <a href="<?php echo e(route('experince', [($nearbyExp['slugid'] ?? '').'-'.str_replace('exp_', '', $nearbyExp['id']).'-'.($nearbyExp['slug'] ?? '')])); ?>">
                                                        <img src="<?php echo e($expImage); ?>" alt="<?php echo e($nearbyExp['title']); ?>" onerror="this.onerror=null; this.src='https://www.travell.co.in/frontend/hotel-detail/images/Hotel%20lobby-image.png';" loading="lazy">
                                                        <div class="icon__slider">
                                                            <img src="<?php echo e(asset('explore/images/icons/bookmark.svg')); ?>" alt="Bookmark" loading="lazy">
                                                        </div>
                                                    </a>
                                                    <div class="also__AtSliderDetails">
                                                        <p>Activity</p>
                                                        <div class="also__title">
                                                            <h4><?php echo e($nearbyExp['title']); ?></h4>
                                                        </div>
                                                        <div class="card__listSection">
                                                            <div class="card__details">
                                                                <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="Rating" loading="lazy"></span>
                                                                <span><?php echo e($nearbyExp['rating'] ?? '85'); ?>%</span>
                                                            </div>
                                                        </div>
                                                        <ul class="list__also">
                                                            <li>Experience</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            
                                            <?php if($itemEntityType === 'restaurant'): ?>
                                                <?php
                                                    $nearbyRest = $alsoAtItem;
                                                    $restImage = 'https://www.travell.co.in/images/Group 1171275916.png';
                                                ?>
                                                <div class="tr-store">
                                                    <a href="<?php echo e(url('rd-'.($nearbyRest['slugid'] ?? '').'-'.preg_replace('/[^0-9]/', '', $nearbyRest['id']).'-'.($nearbyRest['slug'] ?? ''))); ?>">
                                                        <img src="<?php echo e($restImage); ?>" alt="<?php echo e($nearbyRest['title']); ?>" onerror="this.onerror=null; this.src='<?php echo e(asset('explore/images/default-shopping.jpg')); ?>';" loading="lazy">
                                                        <div class="icon__slider">
                                                            <img src="<?php echo e(asset('explore/images/icons/bookmark.svg')); ?>" alt="Bookmark" loading="lazy">
                                                        </div>
                                                    </a>
                                                    <div class="also__AtSliderDetails">
                                                        <p>Dining</p>
                                                        <div class="also__title">
                                                            <h4><?php echo e($nearbyRest['title']); ?></h4>
                                                        </div>
                                                        <div class="card__listSection">
                                                            <div class="card__details">
                                                                <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="Rating" loading="lazy"></span>
                                                                <span><?php echo e($nearbyRest['rating'] ?? '85'); ?>%</span>
                                                            </div>
                                                        </div>
                                                        <ul class="list__also">
                                                            <?php if(!empty($nearbyRest['cuisines'])): ?>
                                                                <li><?php echo e($nearbyRest['cuisines']); ?></li>
                                                            <?php else: ?>
                                                                <li>Restaurant</li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php elseif(!empty($searchresults) && count($searchresults) > 0): ?>
                    <?php $__currentLoopData = $searchresults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!isset($mustSeeItemId) || $item->SightId != $mustSeeItemId): ?>
                            <?php
                                // Determine item type
                                $itemType = 'attraction';
                                if (isset($item->SightId)) {
                                    if (strpos($item->SightId, 'rest_') === 0) {
                                        $itemType = 'restaurant';
                                    } elseif (strpos($item->SightId, 'exp_') === 0) {
                                        $itemType = 'experience';
                                    }
                                }
                                // Get media URL with resize API
                                $mediaUrls = [];
                                $mainMediaUrl = asset('frontend/hotel-detail/images/Hotel lobby-image.png');
                                 // Special handling for experience type
                                if ($itemType === 'experience' && isset($item->Img1) && !empty($item->Img1)) {
                                    // For experiences, use the direct image URLs stored in the item
                                    $mainMediaUrl = $item->Img1;
                                    $mediaUrls[] = $item->Img1;
                                    if (isset($item->Img2) && !empty($item->Img2)) {
                                        $mediaUrls[] = $item->Img2;
                                    }
                                    if (isset($item->Img3) && !empty($item->Img3)) {
                                        $mediaUrls[] = $item->Img3;
                                    }
                                } 
                                // Standard handling for other attraction types
                                else if (isset($sightImages) && !$sightImages->isEmpty()) {
                                    foreach ($sightImages as $sImage) {
                                        if ($sImage->Sightid == $item->SightId && !str_contains($sImage->Image, 'vid')) {
                                            $imageUrl = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=460&height=300";
                                            $mediaUrls[] = $imageUrl;
                                            if (empty($mainMediaUrl) || $mainMediaUrl == asset('frontend/hotel-detail/images/Hotel lobby-image.png')) {
                                                $mainMediaUrl = $imageUrl;
                                            }
                                        }
                                    }
                                }
                                $displayImage = $mainMediaUrl ?? (!empty($mediaUrls[0]) ? $mediaUrls[0] : asset('frontend/hotel-detail/images/Hotel lobby-image.png'));
                            ?>
                            <?php if($itemType == 'restaurant'): ?>
                                
                                <div class="card__Container" onmouseover="highlightMarker(this)" onmouseout="unhighlightMarker(this)" data-sid="<?php echo e($item->SightId); ?>" data-type="<?php echo e($itemType); ?>" data-lat="<?php echo e($item->Latitude ?? ''); ?>" data-lng="<?php echo e($item->Longitude ?? ''); ?>" data-item-id="<?php echo e($item->SightId); ?>">
                                    <div class="card__Box">
                                        <div class="card__title">
                                            <h6><?php echo e(ucfirst($itemType)); ?></h6>
                                        </div>
                                        <div class="card__Subtitle">
                                            <h5>
                                                <a href="<?php echo e(url('rd-'.$item->slugid.'-'.preg_replace('/[^0-9]/', '', $item->SightId).'-'.$item->Slug)); ?>" target="_blank">
                                                    <?php echo e($item->Title); ?>

                                                </a>
                                            </h5>
                                            <div class="img">
                                                <button type="button" class="bookmarkbtn" onclick="toggleBookmark(this)" data-item-id="<?php echo e($item->SightId); ?>">
                                                    <img src="<?php echo e(asset('explore/images/icons/book.svg')); ?>" alt="" loading="lazy" width="16" height="16">
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card__listSection">
                                            <div class="card__details">
                                                <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="" loading="lazy" width="16" height="16"></span>
                                                <span><?php echo e($item->Averagerating ?? '89'); ?>%</span>
                                            </div>
                                            <?php if(!empty($item->timing)): ?>
                                                <?php
                                                    $currentDay = strtolower(date('D'));
                                                    $timingDisplayed = false;
                                                ?>
                                                <?php $__currentLoopData = $item->timing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($tm->day == $currentDay && !$timingDisplayed): ?>
                                                        <div class="card__details">
                                                            <span><img src="<?php echo e(asset('explore/images/icons/clock.svg')); ?>" alt="" loading="lazy" width="16" height="16"></span>
                                                            <span>Open Until <?php echo e(date('g:i A', strtotime($tm->endTime))); ?></span>
                                                        </div>
                                                        <?php $timingDisplayed = true; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <div class="card__details">
                                                    <span><img src="<?php echo e(asset('explore/images/icons/clock.svg')); ?>" alt="" loading="lazy" width="16" height="16"></span>
                                                    <span>Open Until 5 PM</span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="card__details">
                                                <span><img src="<?php echo e(asset('explore/images/icons/location.svg')); ?>" alt="" loading="lazy" width="16" height="16"></span>
                                                <span>
                                                    <?php
                                                        $locationName = null;
                                                        $locationDistance = null;
                                                        $locationCoordinates = null;
                                                        if (isset($item->slugid)) {
                                                            $neighborhood = \App\Models\Neighborhood::where('slug', $item->slugid)->first();
                                                            if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                                                $locationName = $neighborhood->Name;
                                                                $locationCoordinates = [
                                                                    'lat' => $neighborhood->Latitude,
                                                                    'lng' => $neighborhood->Longitude
                                                                ];
                                                            }
                                                        }
                                                        if (empty($locationName) && isset($cityName) && !empty($cityName)) {
                                                            $locationName = $cityName;
                                                        }
                                                        if (empty($locationCoordinates) && isset($item->LocationId)) {
                                                            $neighborhood = \App\Models\Neighborhood::where('LocationId', $item->LocationId)->first();
                                                            if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                                                if (empty($locationName)) {
                                                                    $locationName = $neighborhood->Name;
                                                                }
                                                                $locationCoordinates = [
                                                                    'lat' => $neighborhood->Latitude,
                                                                    'lng' => $neighborhood->Longitude
                                                                ];
                                                            }
                                                        }
                                                        if (empty($locationName) && isset($item->City) && !empty($item->City)) {
                                                            $locationName = $item->City;
                                                        } elseif (empty($locationName) && isset($item->Area) && !empty($item->Area)) {
                                                            $locationName = $item->Area;
                                                        }
                                                        if (empty($locationName) && isset($location) && isset($location->Name)) {
                                                            $locationName = $location->Name;
                                                            if (empty($locationCoordinates) && isset($location->loc_latitude) && isset($location->loc_longitude)) {
                                                                $locationCoordinates = [
                                                                    'lat' => $location->loc_latitude,
                                                                    'lng' => $location->loc_longitude
                                                                ];
                                                            }
                                                        }
                                                        if (!empty($locationCoordinates) && isset($item->Latitude) && isset($item->Longitude)) {
                                                            $earthRadius = 6371;
                                                            $latFrom = deg2rad((float)$item->Latitude);
                                                            $lngFrom = deg2rad((float)$item->Longitude);
                                                            $latTo = deg2rad((float)$locationCoordinates['lat']);
                                                            $lngTo = deg2rad((float)$locationCoordinates['lng']);
                                                            $latDelta = $latTo - $latFrom;
                                                            $lngDelta = $lngTo - $lngFrom;
                                                            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));
                                                            $distance = $angle * $earthRadius;
                                                            $locationDistance = round($distance, 1) . ' km from ' . $lname;
                                                            echo $locationDistance;
                                                        } else {
                                                            echo $lname ?: 'City of London';
                                                        }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card__gallerySection">
                                            <?php
                                                $restaurantImages = [];
                                                $hasRestaurantImages = false;
                                                
                                                if (isset($sightImages) && !$sightImages->isEmpty()) {
                                                    $imageCount = 0;
                                                    foreach ($sightImages as $sImage) {
                                                        if ($sImage->Sightid == $item->SightId && !str_contains($sImage->Image, 'vid') && $imageCount < 4) {
                                                            $restaurantImages[] = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=380";
                                                            $imageCount++;
                                                            $hasRestaurantImages = true;
                                                        }
                                                    }
                                                }
                                                
                                                // If no restaurant images available, use placeholder with resize API
                                                if (!$hasRestaurantImages) {
                                                    $placeholderUrl = asset('/images/Group 1171275916.png');
                                                    $resizedPlaceholder = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=" . urlencode($placeholderUrl) . "&width=380";
                                                    for ($i = 0; $i < 4; $i++) {
                                                        $restaurantImages[] = $resizedPlaceholder;
                                                    }
                                                } else {
                                                    // Fill remaining slots with placeholder if needed
                                                    while (count($restaurantImages) < 4) {
                                                        $placeholderUrl = asset('/images/Group 1171275916.png');
                                                        $restaurantImages[] = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=" . urlencode($placeholderUrl) . "&width=380";
                                                    }
                                                }
                                            ?>
                                            <?php $__currentLoopData = $restaurantImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurantImage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="card__thumb">
                                                    <?php if($loop->parent->first && $loop->first): ?>
                                                    <img 
                                                        src="<?php echo e(str_replace('width=380', 'width=320', $restaurantImage)); ?>" 
                                                        srcset="<?php echo e(str_replace('width=380', 'width=320', $restaurantImage)); ?> 320w,
                                                                <?php echo e($restaurantImage); ?> 380w"
                                                        sizes="(max-width: 768px) 100vw, 380px"
                                                        alt="<?php echo e($item->Title); ?>" 
                                                        fetchpriority="high" 
                                                        loading="eager" 
                                                        width="380" 
                                                        height="250">
                                                    <?php else: ?>
                                                    <img 
                                                        src="<?php echo e(str_replace('width=380', 'width=320', $restaurantImage)); ?>" 
                                                        srcset="<?php echo e(str_replace('width=380', 'width=320', $restaurantImage)); ?> 320w,
                                                                <?php echo e($restaurantImage); ?> 380w"
                                                        sizes="(max-width: 768px) 100vw, 380px"
                                                        alt="<?php echo e($item->Title); ?>" 
                                                        loading="lazy" 
                                                        width="380" 
                                                        height="250">
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <?php if(!empty($item->short_description)): ?>
                                            <?php
                                                $words = str_word_count($item->short_description, 1);
                                                $truncated = implode(' ', array_slice($words, 0, 15)) . (count($words) > 15 ? '...' : '');
                                            ?>
                                            <p class="card__description"><?php echo e($truncated); ?></p>
                                        <?php else: ?>
                                            <p class="card__description"></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                
                                <div class="card__Container" onmouseover="highlightMarker(this)" onmouseout="unhighlightMarker(this)" data-sid="<?php echo e($item->SightId); ?>" data-type="<?php echo e($itemType); ?>" data-lat="<?php echo e($item->Latitude ?? ''); ?>" data-lng="<?php echo e($item->Longitude ?? ''); ?>" data-item-id="<?php echo e($item->SightId); ?>">
                                    <div class="card__Box">
                                        <div class="card__title">
                                            <h6><?php echo e(ucfirst($itemType)); ?></h6>
                                        </div>
                                        <div class="card__Subtitle">
                                            <h5>
                                                <a href="<?php echo e($itemType === 'attraction' ? url('at-'.$item->slugid.'-'.$item->SightId.'-'.strtolower($item->Slug)) : 
                                                    ($itemType === 'restaurant' ? url('rd-'.$item->slugid.'-'.preg_replace('/[^0-9]/', '', $item->SightId).'-'.$item->Slug) : 
                                                    (!empty($item->viator_url) ? $item->viator_url : route('experince', [$item->slugid.'-'.str_replace('exp_', '', $item->SightId).'-'.$item->Slug])))); ?>" target="_blank">
                                                    <?php echo e($item->Title); ?>

                                                </a>
                                            </h5>
                                           <button type="button" class="bookmarkbtn" onclick="toggleBookmark(this)" data-item-id="<?php echo e($item->SightId); ?>">
                                             <img src="https://www.travell.co/explore/images/icons/plusnew.svg" alt="" loading="lazy" width="16" height="16">
                                           </button>
                                        </div>
                                      
                                        <div class="card__gallerySection">
                                            <div class="card__thumb">
                                                <img
                                                    src="<?php echo e($displayImage); ?>"
                                                    srcset="
                                                        <?php echo e(str_replace('width=460', 'width=320', $displayImage)); ?> 320w,
                                                        <?php echo e(str_replace('width=460', 'width=480', $displayImage)); ?> 480w,
                                                        <?php echo e($displayImage); ?> 460w
                                                    "
                                                    sizes="(max-width: 768px) 100vw, 50vw"
                                                    alt="<?php echo e($item->Title); ?>"
                                                    onerror="this.onerror=null; this.src='<?php echo e(asset('frontend/hotel-detail/images/Hotel lobby-image.png')); ?>';"
                                                    <?php if($loop->first): ?>
                                                    fetchpriority="high"
                                                    loading="eager"
                                                    <?php else: ?>
                                                    loading="lazy"
                                                    <?php endif; ?>
                                                    width="460"
                                                    height="300"
                                                >
                                            </div>
                                        </div>
                                          <div class="card__listSection">
                                            <div class="card__details">
                                                <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="" loading="lazy" width="16" height="16"></span>
                                                <span><?php echo e($item->Averagerating ?? '--'); ?>%</span>
                                            </div>
                                            <?php if(!empty($item->timing)): ?>
                                                <?php
                                                    $currentDay = strtolower(date('D'));
                                                    $timingDisplayed = false;
                                                ?>
                                                <?php $__currentLoopData = $item->timing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($tm->day == $currentDay && !$timingDisplayed): ?>
                                                        <div class="card__details">
                                                            <span><img src="<?php echo e(asset('explore/images/icons/clock.svg')); ?>" alt="" loading="lazy" width="16" height="16"></span>
                                                            <span>Open Until <?php echo e(date('g:i A', strtotime($tm->endTime))); ?></span>
                                                        </div>
                                                        <?php $timingDisplayed = true; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                            <div class="card__details">
                                                <span><img src="<?php echo e(asset('explore/images/icons/location.svg')); ?>" alt="" loading="lazy" width="16" height="16"></span>
                                                <span>
                                                    <?php
                                                        $locationName = null;
                                                        $locationDistance = null;
                                                        $locationCoordinates = null;
                                                        if (isset($item->slugid)) {
                                                            $neighborhood = \App\Models\Neighborhood::where('slug', $item->slugid)->first();
                                                            if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                                                $locationName = $neighborhood->Name;
                                                                $locationCoordinates = [
                                                                    'lat' => $neighborhood->Latitude,
                                                                    'lng' => $neighborhood->Longitude
                                                                ];
                                                            }
                                                        }
                                                        if (empty($locationName) && isset($cityName) && !empty($cityName)) {
                                                            $locationName = $cityName;
                                                        }
                                                        if (empty($locationCoordinates) && isset($item->LocationId)) {
                                                            $neighborhood = \App\Models\Neighborhood::where('LocationId', $item->LocationId)->first();
                                                            if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                                                if (empty($locationName)) {
                                                                    $locationName = $neighborhood->Name;
                                                                }
                                                                $locationCoordinates = [
                                                                    'lat' => $neighborhood->Latitude,
                                                                    'lng' => $neighborhood->Longitude
                                                                ];
                                                            }
                                                        }
                                                        if (empty($locationName) && isset($item->City) && !empty($item->City)) {
                                                            $locationName = $item->City;
                                                        } elseif (empty($locationName) && isset($item->Area) && !empty($item->Area)) {
                                                            $locationName = $item->Area;
                                                        }
                                                        if (empty($locationName) && isset($location) && isset($location->Name)) {
                                                            $locationName = $location->Name;
                                                            if (empty($locationCoordinates) && isset($location->loc_latitude) && isset($location->loc_longitude)) {
                                                                $locationCoordinates = [
                                                                    'lat' => $location->loc_latitude,
                                                                    'lng' => $location->loc_longitude
                                                                ];
                                                            }
                                                        }
                                                        if (!empty($locationCoordinates) && isset($item->Latitude) && isset($item->Longitude)) {
                                                            $earthRadius = 6371;
                                                            $latFrom = deg2rad((float)$item->Latitude);
                                                            $lngFrom = deg2rad((float)$item->Longitude);
                                                            $latTo = deg2rad((float)$locationCoordinates['lat']);
                                                            $lngTo = deg2rad((float)$locationCoordinates['lng']);
                                                            $latDelta = $latTo - $latFrom;
                                                            $lngDelta = $lngTo - $lngFrom;
                                                            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));
                                                            $distance = $angle * $earthRadius;
                                                            $locationDistance = round($distance, 1) . ' km from ' . $lname;
                                                            echo $locationDistance;
                                                        } else {
                                                            echo $lname;
                                                        }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>

                                        <?php if(!empty($item->short_description)): ?>
                                            <?php
                                                $words = str_word_count($item->short_description, 1);
                                                $truncated = implode(' ', array_slice($words, 0, 15)) . (count($words) > 15 ? '...' : '');
                                            ?>
                                            <p class="card__description"><?php echo e($truncated); ?></p>
                                        <?php endif; ?>
                                        <?php if($itemType == 'experience'): ?>
                                            <div class="view__button update__button">
                                                <a href="#"><img src="<?php echo e(asset('explore/images/London/button.svg')); ?>" alt="" loading="lazy"></a>
                                                <a href="<?php echo e(asset('at-'.$item->slugid.'-'.$item->SightId.'-'.strtolower($item->Slug))); ?>" class="btn__user">$<?php echo e(rand(10, 99)); ?>/ <span><img src="<?php echo e(asset('explore/images/icons/user.svg')); ?>" alt="" loading="lazy"></span> <span><img src="<?php echo e(asset('explore/images/icons/arrow-right.svg')); ?>" alt="" loading="lazy"></span></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                

                <!-- Shopping Section -->
              <?php
                    // Simple direct approach - find items related to shopping
                    $allItems = collect($searchresults);
                    $shoppingItems = collect();
                    
                    // First pass: Find Nexus Elante mall specifically
                    foreach ($allItems as $item) {
                        if (isset($item->Title) && stripos($item->Title, 'Nexus Elante') !== false) {
                            $shoppingItems->push($item);
                            // Debug output
                            echo "<!-- Found Nexus Elante mall: SightId={$item->SightId}, CategoryId={$item->CategoryId}, Title={$item->Title} -->";
                            break;
                        }
                    }
                    
                    // Second pass: Find items with CategoryId 1295 (Nexus Elante mall)
                    foreach ($allItems as $item) {
                        if (isset($item->CategoryId) && $item->CategoryId == 1295 && 
                            !$shoppingItems->contains('SightId', $item->SightId)) {
                            $shoppingItems->push($item);
                            // Debug output
                            echo "<!-- Found item with CategoryId 1295: SightId={$item->SightId}, Title={$item->Title} -->";
                        }
                    }
                    
                    // Third pass: Find items with shopping-related categories
                    $shoppingCategoryIds = [1285, 1333]; // Known shopping categories
                    
                    foreach ($allItems as $item) {
                        // Skip if already added
                        if ($shoppingItems->contains('SightId', $item->SightId)) {
                            continue;
                        }
                        
                        // Check direct category match
                        if (isset($item->CategoryId) && in_array($item->CategoryId, $shoppingCategoryIds)) {
                            $shoppingItems->push($item);
                            continue;
                        }
                        
                        // Check parent category match
                        if (isset($item->ParentId) && in_array($item->ParentId, $shoppingCategoryIds)) {
                            $shoppingItems->push($item);
                            continue;
                        }
                        
                        // Check CategoryParentId match
                        if (isset($item->CategoryParentId) && in_array($item->CategoryParentId, $shoppingCategoryIds)) {
                            $shoppingItems->push($item);
                            continue;
                        }
                    }
                    
                    // Fourth pass: Find items with shopping-related keywords in title or category
                    $shoppingKeywords = ['mall', 'shop', 'market', 'retail', 'store'];
                    
                    foreach ($allItems as $item) {
                        // Skip if already added
                        if ($shoppingItems->contains('SightId', $item->SightId)) {
                            continue;
                        }
                        
                        // Check title
                        if (isset($item->Title)) {
                            $title = strtolower($item->Title);
                            foreach ($shoppingKeywords as $keyword) {
                                if (strpos($title, $keyword) !== false) {
                                    $shoppingItems->push($item);
                                    break 2; // Break both loops
                                }
                            }
                        }
                        
                        // Check category
                        if (isset($item->Category)) {
                            $category = strtolower($item->Category);
                            foreach ($shoppingKeywords as $keyword) {
                                if (strpos($category, $keyword) !== false) {
                                    $shoppingItems->push($item);
                                    break 2; // Break both loops
                                }
                            }
                        }
                    }
                    
                    // Debug output
                    echo "<!-- Total shopping items found: " . $shoppingItems->count() . " -->";
                    foreach ($shoppingItems as $item) {
                        echo "<!-- Shopping item: SightId={$item->SightId}, CategoryId={$item->CategoryId}, Title={$item->Title} -->";
                    }
                ?>
                <?php $__currentLoopData = $shoppingItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $mediaUrls = [];
                        if (isset($item->media) && is_array($item->media)) {
                            $mediaUrls = array_map(function($media) {
                                return str_starts_with($media->MediaUrl, 'https://image-resize') 
                                    ? $media->MediaUrl 
                                    : "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url={$media->MediaUrl}&width=460&height=300";
                            }, $item->media);
                        } elseif (isset($sightImages) && !$sightImages->isEmpty()) {
                            foreach ($sightImages as $sImage) {
                                if ($sImage->Sightid == $item->SightId && !str_contains($sImage->Image, 'vid')) {
                                    $mediaUrls[] = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=460&height=300";
                                }
                            }
                        }
                        $mainMediaUrl = !empty($mediaUrls) ? $mediaUrls[0] : asset('frontend/hotel-detail/images/Hotel lobby-image.png');
                    ?>
                    <div class="card__Container" onmouseover="highlightMarker(this)" onmouseout="unhighlightMarker(this)" data-sid="<?php echo e($item->SightId); ?>" data-type="shopping">
                        <div class="card__Box">
                            <div class="card__title">
                                <h6>SHOPPING</h6>
                            </div>
                            <div class="Container__Section">
                                <div class="left__sideSection">
                                    <div class="card__Subtitle">
                                        <h5><?php echo e($item->Title); ?></h5>
                                    </div>
                                    <div class="card__listSection">
                                        <div class="card__details">
                                            <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="" loading="lazy"></span>
                                            <span><?php echo e($item->Averagerating ?? '--'); ?>%</span>
                                        </div>
                                        <div class="card__details">
                                            <span><img src="<?php echo e(asset('explore/images/icons/location.svg')); ?>" alt="" loading="lazy"></span>
                                            <span><?php
                                                $displayLocation = $item->Area ?? $item->Location ?? $lname;
                                                echo $displayLocation;
                                            ?></span>
                                        </div>
                                    </div>
                                    <div class="card__listSection">
                                        <?php if(isset($item->timing) && is_array($item->timing)): ?>
                                            <?php
                                                $currentDay = strtolower(date('D'));
                                                $timingDisplayed = false;
                                            ?>
                                            <?php $__currentLoopData = $item->timing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(isset($tm->day) && $tm->day == $currentDay && !$timingDisplayed): ?>
                                                    <div class="card__details update_details">
                                                        <span class="tagName">Open</span>
                                                        <span>till about <?php echo e(date('g:i A', strtotime($tm->endTime))); ?></span>
                                                    </div>
                                                    <?php $timingDisplayed = true; ?>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="also__AtSection">
                                <div class="divider">
                                    <span>Also at</span>
                                </div>
                                <div class="tr-market-slider sliderNew">
                                    <?php
                                        $relatedShops = [];
                                        if (isset($searchresults) && !empty($searchresults)) {
                                            $relatedShopsFromData = collect($searchresults)
                                                ->filter(function($relItem) use ($item) {
                                                    return isset($relItem->SightId) && 
                                                           $relItem->SightId != $item->SightId && 
                                                           (
                                                               (isset($relItem->Category) && strtolower($relItem->Category) === 'shopping') ||
                                                               (isset($relItem->Title) && stripos($relItem->Title, 'shop') !== false) ||
                                                               (isset($relItem->Title) && stripos($relItem->Title, 'mall') !== false) ||
                                                               (isset($relItem->Title) && stripos($relItem->Title, 'market') !== false)
                                                           );
                                                })
                                                ->take(4);
                                            if ($relatedShopsFromData->count() > 0) {
                                                foreach ($relatedShopsFromData as $relItem) {
                                                    $relItemImage = asset('explore/images/placeholder.jpg');
                                                    if (isset($sightImages) && !$sightImages->isEmpty()) {
                                                        foreach ($sightImages as $sImage) {
                                                            if ($sImage->Sightid == $relItem->SightId && !str_contains($sImage->Image, 'vid')) {
                                                                $relItemImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=320&height=300";
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    $relatedShops[] = [
                                                        'title' => $relItem->Title ?? 'Unknown',
                                                        'image' => $relItemImage,
                                                        'type' => $relItem->Category ?? 'Attraction',
                                                        'rating' => $relItem->Averagerating ?? 85,
                                                        'distance' => isset($relItem->distance) ? $relItem->distance . ' km away' : ($relItem->Area ?? $relItem->Location ?? $lname),
                                                        'is_numerical_distance' => isset($relItem->distance),
                                                        'tags' => isset($relItem->Tags) ? explode(',', $relItem->Tags) : ['Shopping'],
                                                        'openTime' => null,
                                                        'closeTime' => null,
                                                        'duration' => null
                                                    ];
                                                }
                                            }
                                        }
                                    ?>
                                    <?php if(!empty($relatedShops)): ?>
                                        <?php $__currentLoopData = $relatedShops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="tr-store">
                                                <a href="javascript:void(0);">
                                                    <img
                                                        src="<?php echo e($shop['image']); ?>"
                                                        srcset="
                                                            <?php echo e(str_replace('width=320', 'width=200', $shop['image'])); ?> 200w,
                                                            <?php echo e($shop['image']); ?> 320w,
                                                            <?php echo e(str_replace('width=320', 'width=640', $shop['image'])); ?> 640w
                                                        "
                                                        sizes="(max-width: 768px) 100vw, 33vw"
                                                        alt="<?php echo e($shop['title']); ?>"
                                                        onerror="this.onerror=null; this.src='<?php echo e(asset('explore/images/placeholder.jpg')); ?>';"
                                                        loading="lazy"
                                                    >
                                                    <div class="icon__slider">
                                                        <img src="<?php echo e(asset('explore/images/icons/bookmark.svg')); ?>" alt="Bookmark" loading="lazy">
                                                    </div>
                                                </a>
                                                <div class="also__AtSliderDetails">
                                                    <p><?php echo e($shop['type']); ?></p>
                                                    <div class="also__title">
                                                        <h4><?php echo e($shop['title']); ?></h4>
                                                    </div>
                                                    <div class="card__listSection">
                                                        <div class="card__details">
                                                            <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="Rating" loading="lazy"></span>
                                                            <span><?php echo e($shop['rating']); ?>%</span>
                                                        </div>
                                                        <div class="card__details">
                                                            <span><img src="<?php echo e(asset('explore/images/icons/location.svg')); ?>" alt="Location" loading="lazy"></span>
                                                            <span>
                                                                <?php if($shop['is_numerical_distance']): ?>
                                                                    <?php echo e($shop['distance']); ?>

                                                                <?php else: ?>
                                                                    Location: <?php echo e($shop['distance']); ?>

                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <?php if(isset($shop['openTime']) && isset($shop['closeTime'])): ?>
                                                        <div class="card__listSection">
                                                            <div class="card__details update_details">
                                                                <span class="tagName">Open</span>
                                                                <span>till about <?php echo e($shop['closeTime']); ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <ul class="list__also">
                                                        <?php $__currentLoopData = $shop['tags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li><?php echo e($tag); ?><?php echo e(!$loop->last ? ',' : ''); ?></li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Must See Attraction -->
                <?php if(!empty($searchresults) && count($searchresults) > 0 && isset($mustSeeItem)): ?>
                    <?php
                        $mustSeeImages = [];
                        if (isset($sightImages) && !$sightImages->isEmpty()) {
                            $imgCount = 0;
                            foreach ($sightImages as $sImage) {
                                if ($sImage->Sightid == $mustSeeItem->SightId && !str_contains($sImage->Image, 'vid')) {
                                     $mustSeeImages[] = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=435&height=420";
                                    $imgCount++;
                                    if ($imgCount >= 3) break;
                                }
                            }
                        }
                        if (empty($mustSeeImages)) {
                            $mustSeeImages = array_fill(0, 3, asset('frontend/hotel-detail/images/Hotel lobby-image.png'));
                        }
                        $rating = isset($mustSeeItem->Averagerating) ? number_format($mustSeeItem->Averagerating, 1) : '4.5';
                    ?>
                    <div class="card__Container">
                        <div class="card__Box">
                            <div class="card__title">
                                <h6>MUST SEE ATTRACTION</h6>
                            </div>
                            <div class="card__Subtitle">
                                <h5><?php echo e($mustSeeItem->Title); ?></h5>
                            <button type="button" class="bookmarkbtn" onclick="toggleBookmark(this)">
                            <img src="https://www.travell.co/explore/images/icons/plusnew.svg" alt="" loading="lazy">
                            </button>
                            </div>
                            <div class="card__listSection">
                                <div class="card__details">
                                    <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="" loading="lazy"></span>
                                    <span><?php echo e($rating * 20); ?>%</span>
                                </div>
                                <div class="card__details">
                                    <span><img src="<?php echo e(asset('explore/images/icons/clock.svg')); ?>" alt="" loading="lazy"></span>
                                    <span>
                                        <?php
                                            $timingDisplayed = false;
                                            $openingTime = 'Open';
                                            if (!empty($mustSeeItem->timing)) {
                                                $currentDay = strtolower(date('D'));
                                                foreach ($mustSeeItem->timing as $tm) {
                                                    if ($tm->day == $currentDay && !$timingDisplayed) {
                                                        $openingTime = 'Open Until ' . date('g:i A', strtotime($tm->endTime));
                                                        $timingDisplayed = true;
                                                        break;
                                                    }
                                                }
                                                if (!$timingDisplayed) {
                                                    $openingTime = 'Hours vary';
                                                }
                                            }
                                            echo $openingTime;
                                        ?>
                                    </span>
                                </div>
                                <div class="card__details">
                                    <span><img src="<?php echo e(asset('explore/images/icons/location.svg')); ?>" alt="" loading="lazy"></span>
                                    <span><?php echo e($cityName ?? $locn ?? ($mustSeeItem->Area ?? $mustSeeItem->Location ?? '')); ?></span>
                                </div>
                            </div>
                            <div class="MustSeeAttraction">
                                <div id="SliderMustSee" class="carousel slide" data-bs-touch="false" data-bs-interval="false">
                                    <div class="carousel-inner">
                                        <?php $__currentLoopData = $mustSeeImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="carousel-item <?php echo e($index == 0 ? 'active' : ''); ?>">
                                                <a href="<?php echo e(asset('at-'.$mustSeeItem->slugid.'-'.$mustSeeItem->SightId.'-'.strtolower($mustSeeItem->Slug))); ?>">
                                                    <img
                                                        src="<?php echo e($image); ?>"
                                                        srcset="
                                                            <?php echo e(str_replace('width=475', 'width=320', $image)); ?> 320w,
                                                            <?php echo e(str_replace('width=475', 'width=640', $image)); ?> 640w,
                                                            <?php echo e($image); ?> 1280w
                                                        "
                                                        sizes="(max-width: 768px) 100vw, 33vw"
                                                        alt="<?php echo e($mustSeeItem->Title); ?> <?php echo e($index + 1); ?>"
                                                        onerror="this.onerror=null; this.src='<?php echo e(asset('frontend/hotel-detail/images/Hotel lobby-image.png')); ?>';"
                                                        loading="lazy"
                                                    >
                                                </a>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <?php if(count($mustSeeImages) > 1): ?>
                                        <div class="carousel-controls">
                                            <button class="carousel-control-prev" type="button" data-bs-target="#SliderMustSee" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon"></span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#SliderMustSee" data-bs-slide="next">
                                                <span class="carousel-control-next-icon"></span>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="also__AtSection">
                                <div class="divider">
                                    <span>Also at</span>
                                </div>
                                <div class="tr-market-slider sliderNew">
                                    <?php
                                        $mustSeeItemId = isset($mustSeeItem) ? $mustSeeItem->SightId : null;
                                        $otherItems = collect($searchresults);
                                        
                                        // Filter out the must-see item
                                        if ($mustSeeItemId) {
                                            $otherItems = $otherItems->where('SightId', '!=', $mustSeeItemId);
                                        }
                                        
                                        // Prioritize items from the same location
                                        $locationItems = collect();
                                        if ($current_location_id) {
                                            $locationItems = $otherItems->filter(function($item) use ($current_location_id) {
                                                return isset($item->LocationId) && $item->LocationId == $current_location_id;
                                            });
                                        }
                                        
                                        // Ensure we have a diverse mix of item types
                                        $attractionItems = $otherItems->filter(function($item) {
                                            return isset($item->Type) && $item->Type == 'attraction';
                                        })->take(2);
                                        
                                        $experienceItems = $otherItems->filter(function($item) {
                                            return isset($item->Type) && $item->Type == 'experience';
                                        })->take(2);
                                        
                                        $restaurantItems = $otherItems->filter(function($item) {
                                            return isset($item->Type) && $item->Type == 'restaurant';
                                        })->take(2);
                                        
                                        // Combine all items and ensure uniqueness
                                        $finalItems = collect();
                                        
                                        // Add location items first (if available)
                                        if ($locationItems->count() > 0) {
                                            $finalItems = $finalItems->merge($locationItems->take(2));
                                        }
                                        
                                        // Add one of each type to ensure diversity
                                        if ($attractionItems->count() > 0) {
                                            $finalItems = $finalItems->merge($attractionItems->take(1));
                                        }
                                        
                                        if ($experienceItems->count() > 0) {
                                            $finalItems = $finalItems->merge($experienceItems->take(1));
                                        }
                                        
                                        if ($restaurantItems->count() > 0) {
                                            $finalItems = $finalItems->merge($restaurantItems->take(1));
                                        }
                                        
                                        // If we still need more items, add from the general collection
                                        if ($finalItems->count() < 4) {
                                            // Get items not already in finalItems
                                            $remainingItems = $otherItems->filter(function($item) use ($finalItems) {
                                                return !$finalItems->contains('SightId', $item->SightId);
                                            });
                                            
                                            $finalItems = $finalItems->merge($remainingItems->take(4 - $finalItems->count()));
                                        }
                                        
                                        // Ensure we only take 4 items maximum
                                        $finalItems = $finalItems->take(4);
                                    ?>
                                    <?php $__currentLoopData = $finalItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $itemImage = asset('explore/images/default-shopping.jpg');
                                            
                                            // Special handling for experience type
                                            if (isset($item->Type) && $item->Type === 'experience' && isset($item->Img1) && !empty($item->Img1)) {
                                                $itemImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url={$item->Img1}&width=320&height=300";
                                            }
                                            // Check for media property
                                            elseif (isset($item->media) && is_array($item->media) && !empty($item->media)) {
                                                foreach ($item->media as $media) {
                                                    if (isset($media->MediaUrl) && !empty($media->MediaUrl)) {
                                                        $itemImage = str_starts_with($media->MediaUrl, 'https://image-resize') 
                                                            ? $media->MediaUrl 
                                                            : "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url={$media->MediaUrl}&width=320&height=300";
                                                        break;
                                                    }
                                                }
                                            }
                                            // Check sightImages as fallback
                                            elseif (isset($sightImages) && !$sightImages->isEmpty()) {
                                                foreach ($sightImages as $sImage) {
                                                    if ($sImage->Sightid == $item->SightId && !str_contains($sImage->Image, 'vid')) {
                                                        $itemImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=320&height=300";
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            $rating = isset($item->Averagerating) ? number_format($item->Averagerating, 1) : '4.5';
                                            $price = '$' . rand(10, 99) . ' per person';
                                            $itemType = isset($item->Type) ? ucfirst($item->Type) : 'Experience';
                                            
                                            // Set appropriate categories based on item type
                                            $categories = [];
                                            if ($itemType == 'Restaurant') {
                                                $categories = ['Italian', 'Chinese', 'French', 'Japanese', 'Indian', 'Mexican'];
                                            } elseif ($itemType == 'Attraction') {
                                                $categories = ['Historic', 'Museum', 'Park', 'Monument', 'Landmark'];
                                            } elseif ($itemType == 'Experience') {
                                                $categories = ['Adventure', 'Cultural', 'Food', 'Guided Tour', 'Workshop'];
                                            } else {
                                                $categories = ['Popular', 'Featured', 'Top Rated'];
                                            }
                                            
                                            $randomCategories = array_rand(array_flip($categories), min(2, count($categories)));
                                            if (!is_array($randomCategories)) {
                                                $randomCategories = [$randomCategories];
                                            }
                                        ?>
                                        <div class="tr-store">
                                            <a href="<?php echo e(asset('at-'.$item->slugid.'-'.$item->SightId.'-'.strtolower($item->Slug))); ?>">
                                                <img
                                                    class="slider__thumb"
                                                    src="<?php echo e($itemImage); ?>"
                                                    srcset="
                                                        <?php echo e(str_replace('width=320', 'width=200', $itemImage)); ?> 200w,
                                                        <?php echo e($itemImage); ?> 320w,
                                                        <?php echo e(str_replace('width=320', 'width=640', $itemImage)); ?> 640w
                                                    "
                                                    sizes="(max-width: 768px) 100vw, 33vw"
                                                    alt="<?php echo e($item->Title); ?>"
                                                    onerror="this.onerror=null; this.src='<?php echo e(asset('frontend/hotel-detail/images/Hotel lobby-image.png')); ?>';"
                                                    loading="lazy"
                                                >
                                                <div class="icon__slider">
                                                    <img src="<?php echo e(asset('explore/images/icons/bookmark.svg')); ?>" alt="Bookmark" loading="lazy">
                                                </div>
                                            </a>
                                            <div class="also__AtSliderDetails">
                                                <p><?php echo e($itemType); ?></p>
                                                <div class="also__title">
                                                    <h4>
                                                        <?php echo e($item->Title); ?>

                                                        <img src="<?php echo e(asset('explore/images/icons/External_Link.svg')); ?>" alt="external link" width="16" height="16" style="vertical-align: middle; margin-left: 5px" loading="lazy">
                                                    </h4>
                                                </div>
                                                <div class="card__listSection">
                                                    <div class="card__details update_details">
                                                        <span class="tagName"><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="Rating" loading="lazy"></span>
                                                        <span><?php echo e($rating * 20); ?>%</span>
                                                    </div>
                                                    <div class="card__details update_details">
                                                        <span><img src="<?php echo e(asset('explore/images/icons/point.svg')); ?>" alt="Price" loading="lazy"></span>
                                                        <span><?php echo e($price); ?></span>
                                                    </div>
                                                </div>
                                                <ul class="list__also">
                                                    <?php $__currentLoopData = $randomCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li><?php echo e($category); ?><?php echo e(!$loop->last ? ',' : ''); ?></li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div id="loading" class="tr-page-loader" style="display: none;">
                    <div class="tr-loader-container">
                        <div class="tr-spinner"></div>
                        <p>Finding the best attractions...</p>
                    </div>
                </div>
                <button type="button" class="tr-btn tr-load-more">Load More</button>
            </div>

            <div class="tr-map-and-filter">
                <button type="button" class="tr-explore-map-btn map">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2464_12970)">
                            <path d="M0.583984 3.4974V12.8307L4.66732 10.4974L9.33398 12.8307L13.4173 10.4974V1.16406L9.33398 3.4974L4.66732 1.16406L0.583984 3.4974Z" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M4.66602 1.16406V10.4974" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M9.33398 3.5V12.8333" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </g>
                        <defs>
                            <clipPath id="clip0_2464_12970">
                                <rect width="14" height="14" fill="white"></rect>
                            </clipPath>
                        </defs>
                    </svg>
                    Map
                </button>
            </div>

            <div class="tr-explore-right-section">
                <div class="tr-map-section">
                    <button type="button" class="btn-close"></button>
                    <div class="tr-hotel-on-map">
                        <form>
                            <input type="text" class="form-control" id="" placeholder="Search on map" name="" autocomplete="off">
                            <div class="tr-recent-searchs-modal" id="">
                                <div class="tr-enable-location">Around Current Location</div>
                                <h5>Recent searches</h5>
                                <ul>
                                    <li>
                                        <div class="tr-place-info">
                                            <div class="tr-location-icon"></div>
                                            <div class="tr-location-info">
                                                <div class="tr-hotel-name">London Hotels</div>
                                                <div class="tr-hotel-city">England, United Kingdom</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="tr-place-info">
                                            <div class="tr-location-icon"></div>
                                            <div class="tr-location-info">
                                                <div class="tr-hotel-name">Morocco</div>
                                                <div class="tr-hotel-city">North Africa</div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <button type="button" hidden="" class="tr-btn">Countinue</button>
                        </form>
                    </div>
                    <?php if(!empty($structuredFeed) || !empty($searchresults)): ?>
                        <div class="tr-map-tooltip tr-explore-listing">
                            <div class="tr-historical-monument">
                                <?php
                                    // Get a filtered item for the map based on proper category matching
                                    if (!empty($structuredFeed) && count($structuredFeed) > 0) {
                                        // Use structured feed data and map to expected format
                                        $mapItems = collect($structuredFeed)->filter(function($item) {
                                            return isset($item['type']) && $item['type'] !== 'transit' && isset($item['data']);
                                        })->map(function($item) {
                                            $data = $item['data'];
                                            return (object)[
                                                'SightId' => $data['id'] ?? null,
                                                'Title' => $data['title'] ?? null,
                                                'Slug' => $data['slug'] ?? null,
                                                'slugid' => $data['slugid'] ?? null,
                                                'Latitude' => $data['latitude'] ?? null,
                                                'Longitude' => $data['longitude'] ?? null,
                                                'Averagerating' => $data['rating'] ?? null,
                                                'CategoryId' => null,
                                                'ParentId' => null,
                                                'Type' => ucfirst($item['type'] ?? 'attraction'),
                                                'Category' => ucfirst($item['type'] ?? 'attraction')
                                            ];
                                        });
                                    } else {
                                        $mapItems = collect($searchresults);
                                    }
                                    
                                    // First try to find items with CategoryId 1285 or 1333 (shopping)
                                    $categoryMatches = $mapItems->filter(function($item) {
                                        return isset($item->CategoryId) && in_array($item->CategoryId, [1285, 1333]);
                                    });
                                    
                                    if (!$categoryMatches->isEmpty()) {
                                        $mapItem = $categoryMatches->first();
                                    } else {
                                        // If no category matches, try ParentId
                                        $parentMatches = $mapItems->filter(function($item) {
                                            return isset($item->ParentId) && in_array($item->ParentId, [1285, 1333]);
                                        });
                                        
                                        if (!$parentMatches->isEmpty()) {
                                            $mapItem = $parentMatches->first();
                                        } else {
                                            // Last resort: use the first item from search results
                                            $mapItem = $mapItems->first();
                                        }
                                    }
                                    
                                    // Get image for the map item
                                    $mapImage = asset('frontend/hotel-detail/images/Hotel lobby-image.png');
                                    if (isset($sightImages) && !$sightImages->isEmpty() && isset($mapItem->SightId)) {
                                        foreach ($sightImages as $sImage) {
                                            if ($sImage->Sightid == $mapItem->SightId && !str_contains($sImage->Image, 'vid')) {
                                                $mapImage = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sImage->Image}&width=460&height=300";
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <div class="tr-heading-with-distance">
                                    <div class="tr-category"><?php echo e(isset($mapItem->Type) ? $mapItem->Type : (isset($mapItem->Category) ? $mapItem->Category : 'Shopping Area')); ?></div>
                                    <div class="tr-distance">
                                        <?php
                                            $distance = '';
                                            if (isset($mapItem->distance)) {
                                                $distance = $mapItem->distance . ' km from ';
                                            }
                                        ?>
                                        <?php echo e($distance); ?><?php echo e($mapItem->Title ?? $lname); ?>

                                    </div>
                                </div>
                                <div class="tr-image">
                                    <a href="<?php echo e(asset('at-'.$mapItem->slugid.'-'.$mapItem->SightId.'-'.strtolower($mapItem->Slug))); ?>">
                                        <img
                                            src="<?php echo e($mapImage); ?>"
                                            srcset="
                                                <?php echo e(str_replace('width=460', 'width=320', $mapImage)); ?> 320w,
                                                <?php echo e(str_replace('width=460', 'width=640', $mapImage)); ?> 640w,
                                                <?php echo e($mapImage); ?> 1280w
                                            "
                                            sizes="(max-width: 768px) 100vw, 50vw"
                                            alt="<?php echo e($mapItem->Title); ?>"
                                            onerror="this.onerror=null; this.src='<?php echo e(asset('frontend/hotel-detail/images/Hotel lobby-image.png')); ?>';"
                                            fetchpriority="high"
                                            loading="lazy"
                                            width="460"
                                            height="300"
                                        >
                                    </a>
                                </div>
                                <div class="tr-details">
                                    <h3>
                                        <a href="<?php echo e(asset('at-'.$mapItem->slugid.'-'.$mapItem->SightId.'-'.strtolower($mapItem->Slug))); ?>" target="_blank"><?php echo e($mapItem->Title ?? 'Oxford Street'); ?></a>
                                    </h3>
                                    <div class="tr-location">
                                        <?php
                                            $locationName = $lname;
                                            // Priority 1: Get neighborhood name by slugid from Neighborhood table
                                            if (isset($mapItem->slugid) && isset($neighborhoods)) {
                                                $neighborhoodMatch = $neighborhoods->where('slugid', $mapItem->slugid)->first();
                                                if ($neighborhoodMatch) {
                                                    $locationName = $neighborhoodMatch->Name;
                                                }
                                            }
                                            // Priority 2: Use cityName from controller if available
                                            elseif (isset($cityName) && !empty($cityName)) {
                                                $locationName = $cityName;
                                            }
                                            // Priority 3: Get neighborhood name by LocationId from Neighborhood table
                                            elseif (isset($mapItem->LocationId) && isset($neighborhoods)) {
                                                $neighborhoodMatch = $neighborhoods->where('LocationId', $mapItem->LocationId)->first();
                                                if ($neighborhoodMatch) {
                                                    $locationName = $neighborhoodMatch->Name;
                                                }
                                            }
                                            // Priority 4: Fallback to item's City or Area properties
                                            elseif (isset($mapItem->City) && !empty($mapItem->City)) {
                                                $locationName = $mapItem->City;
                                            }
                                            elseif (isset($mapItem->Area) && !empty($mapItem->Area)) {
                                                $locationName = $mapItem->Area;
                                            }
                                            elseif (isset($mapItem->Location) && !empty($mapItem->Location)) {
                                                $locationName = $mapItem->Location;
                                            }
                                        ?>
                                        <?php echo e($locationName); ?>

                                    </div>
                                    <div class="tr-like-review">
                                        <div class="tr-heart">
                                            <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99604 2.28959C5.02968 1.20745 3.41823 0.916356 2.20745 1.90727C0.996677 2.89818 0.826217 4.55494 1.77704 5.7269L5.99604 9.63412L10.215 5.7269C11.1659 4.55494 11.0162 2.88776 9.78463 1.90727C8.55304 0.92678 6.96239 1.20745 5.99604 2.28959Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </div>
                                        <div class="tr-ranting-percent"><?php echo e(isset($mapItem->Averagerating) ? ($mapItem->Averagerating * 20) : '89'); ?>%</div>
                                    </div>
                                </div>
                                <div class="tr-more-inform">
                                    <ul>
                                        <?php if(!empty($mapItem->timing)): ?>
                                            <?php
                                                $currentDay = strtolower(date('D'));
                                                $timingDisplayed = false;
                                            ?>
                                            <?php $__currentLoopData = $mapItem->timing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($tm->day == $currentDay && !$timingDisplayed): ?>
                                                    <li><span>Open</span>until <?php echo e(date('g:i A', strtotime($tm->endTime))); ?></li>
                                                    <?php $timingDisplayed = true; ?>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(!$timingDisplayed): ?>
                                                <li><span>Closed Today</span></li>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <li><span>Open</span>until 5 PM</li>
                                        <?php endif; ?>
                                        <li>5 hours.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="map1" class="explore-listing-map"></div>
                    <?php else: ?>
                        <img src="<?php echo e(asset('explore/images/map-2.png')); ?>" class="tr-temp-img" alt="map" loading="lazy" width="100%" height="400"/>
                    <?php endif; ?>
                </div>
                <div class="tr-explore-overlay"></div>
            </div>
        </div>
    </div>
</div>
     
  <?php if(isset($currentWeather) && is_array($currentWeather) && (isset($currentWeather['temp_f']) || isset($currentWeather['temp_c']))): ?>
  <div class="container" style="margin-top: 30px;">
      <div class="weather__section" style="margin-top: 0;">
          <h3>Weather near you</h3>
          <table>
              <thead>
                  <tr>
                      <th>Now</th>
                      <th>Temp</th>
                      <th>Rain</th>
                      <th>Wind</th>
                  </tr>
              </thead>
              <tbody>
                  <tr>
                      <td data-label="Now"><?php echo e($currentWeather['time'] ?? 'Now'); ?></td>
                      <td data-label="Temp">
                          <?php if(isset($currentWeather['temp_f'])): ?>
                              <?php echo e($currentWeather['temp_f']); ?>&deg;F
                          <?php elseif(isset($currentWeather['temp_c'])): ?>
                              <?php echo e(round($currentWeather['temp_c'])); ?>&deg;C
                          <?php else: ?>
                              -
                          <?php endif; ?>
                      </td>
                      <td data-label="Rain">
                          <?php if(isset($currentWeather['precip_mm'])): ?>
                              <?php echo e($currentWeather['precip_mm']); ?> mm
                          <?php else: ?>
                              -
                          <?php endif; ?>
                      </td>
                      <td data-label="Wind">
                          <?php if(isset($currentWeather['wind_kmh'])): ?>
                              <?php echo e($currentWeather['wind_kmh']); ?> km/h
                          <?php else: ?>
                              -
                          <?php endif; ?>
                      </td>
                  </tr>
              </tbody>
          </table>
      </div>
  </div>
  <?php endif; ?>       
       
  <?php if(isset($weatherData) && count($weatherData) > 0): ?>                                                   
 <div class="container" style="margin-top: 80px;">
        <div class="weather__section">
            <h3><?php echo e($lname ?? 'Location'); ?> Weather</h3>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Avg.<br/> Temp<br/> (&deg;F)</th>
                        <th>Rainy<br/> Days</th>
                        <th>Clothing</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $weatherData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td data-label="Month"><?php echo e($month['month']); ?>

                            <?php if($month['avg_temp_high'] >= 80): ?>
                                <span class="dot red"></span>
                            <?php elseif($month['avg_temp_high'] >= 65): ?>
                                <span class="dot yellow"></span>
                            <?php else: ?>
                                <span class="dot green"></span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Avg. Temp (&deg;F)" class="temp">
                            <span><?php echo e($month['avg_temp_high']); ?></span>
                            <hr class="temp-divider" />
                            <span><?php echo e($month['avg_temp_low']); ?></span>
                        </td>
                        <td data-label="Rainy Days"><?php echo e($month['num_rainy_days']); ?> days</td>
                        <td data-label="Clothing" class="clothing"><?php echo e($month['condition_text']); ?><br>
                            <?php if($month['avg_temp_high'] >= 80): ?>
                                Light clothing
                            <?php elseif($month['avg_temp_high'] >= 65): ?>
                                Comfortable clothing
                            <?php else: ?>
                                Warm clothing
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
    <!-- <?php echo e($lname ?? 'Location'); ?> Weather section -->

    <?php if(!empty($location) && !empty($location->About)): ?>
    <div class="tr-home-page">
      <div class="container">
        <div class="row">
    <div class="tr-single-page">
    <div class="tr-about-us-home-section">
                        <?php echo $location->About; ?>

                    </div>
    </div>
    </div>
    </div>
    </div>
    <?php endif; ?>
                        
  <!--Quick Portrait>
  <div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="tr-single-page">
                <div class="tr-terms-and-conditions-section">
                    <h3 style="font-weight: bold; margin-bottom: 20px; font-size: 24px;">

                    </h3>
                    <p style="margin-top: 20px;">

                    </p>
                </div>
            </div>
        </div>
    </div>
	</div>
    <div class="container">
        <div class="row">
            <div class="tr-single-page">
                    <h2 class="section-title mb-16 font-bold"><?php echo e($cityName ?? ($locn ?? 'City')); ?>: Quick Portrait
                    </h2>
                    <div class="about-section mb-32">
                    <?php
                        $currentCity = $cityName ?? ($locn ?? 'City');
                        $aboutContent = null;
                        
                        // Check if location exists and extract About content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $aboutContent = isset($location->About) && !empty($location->About) ? $location->About : null;
                            } elseif (is_array($location)) {
                                $aboutContent = isset($location['About']) && !empty($location['About']) ? $location['About'] : null;
                            }
                        }
                    ?>
                    <?php if(!is_null($aboutContent) && !empty($aboutContent)): ?>
                        <?php echo $aboutContent; ?>

                    <?php else: ?>
                        <?php if($currentCity == 'Dubai'): ?>
                            <p>A destination where every sunrise is a golden promise and every sunset a glittering spectacle, Dubai is not just a place to visit – it's a place to be endlessly inspired." Dubai is the UAE's dazzling metropolis – where desert sands meet futuristic skylines. It's a city of contrast and ambition, offering both rich Arabian heritage and ultra-modern experiences. Dubai is the shimmering crown of the United Arab</p>
                            <p>Emirates – a futuristic oasis where golden deserts meet glass skyscrapers that seem to touch the very edge of the sky. It is a place where ancient Bedouin traditions breathe alongside ultramodern lifestyles, where you can stroll through bustling spice-scented souks in the morning and dine atop the world's tallest building by evening.</p>
                            <p>Shopping malls are not just for shopping here; they house indoor ski slopes, giant aquariums, and virtual reality parks, making each visit an adventure of its own. Dubai is a destination that redefines the meaning of luxury and leisure, blending thrilling desert safaris with beach relaxation, Michelin-starred dining with</p>
                            <p>street food delights, and cultural immersions with futuristic innovations. It is a city that promises limitless possibilities, inviting you to witness human ambition carved into architectural marvels while still feeling the timeless soul of Arabia echoing through its warm winds and golden sands.</p>
                        <?php else: ?>
                            <p><?php echo e($currentCity); ?> is a captivating destination that offers visitors a unique blend of cultural experiences, scenic beauty, and unforgettable adventures. From its distinctive landmarks to its local cuisine, <?php echo e($currentCity); ?> presents travelers with countless opportunities to explore and discover.</p>
                            <p>Whether you're interested in historical sites, natural wonders, or vibrant city life, <?php echo e($currentCity); ?> has something to offer every type of traveler. The city's rich heritage and modern developments create a fascinating contrast that makes it a must-visit destination.</p>
                            <p>Visitors to <?php echo e($currentCity); ?> can enjoy a diverse range of activities, from exploring museums and galleries to sampling local delicacies at restaurants and street food stalls. The city's unique atmosphere and welcoming locals make every experience memorable.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="info-section mb-32">
                    <h3 class="mb-8">Best Time to Visit</h3>
                    <?php
                        $bestTimeContent = null;
                        
                        // Check if location exists and extract BestTimeToVisit content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $bestTimeContent = isset($location->BestTimeToVisit) && !empty($location->BestTimeToVisit) ? $location->BestTimeToVisit : null;
                            } elseif (is_array($location)) {
                                $bestTimeContent = isset($location['BestTimeToVisit']) && !empty($location['BestTimeToVisit']) ? $location['BestTimeToVisit'] : null;
                            }
                        }
                    ?>
                    <?php if(!is_null($bestTimeContent) && !empty($bestTimeContent)): ?>
                        <?php echo $bestTimeContent; ?>

                    <?php else: ?>
                        <?php if($currentCity == 'Dubai'): ?>
                            <p>November – March | 20°C – 30°C (68°F – 86°F)</p>
                            <p>Pleasant weather for outdoor activities, beach days, and desert safaris.</p>
                            <h4 class="mb-8">Avoid:</h4>
                            <p>June – August | Above 40°C (104°F+)</p>
                            <p>Extreme heat limits outdoor plans.</p>
                            <p>Ramadan (Variable Dates): Reduced daytime dining and shorter attraction hours.</p>
                        <?php else: ?>
                            <p>The ideal time to visit <?php echo e($currentCity); ?> depends on your preferences for weather and activities.</p>
                            <p>Generally, the most comfortable seasons offer moderate temperatures and fewer crowds.</p>
                            <p>Check local seasonal events and festivals when planning your trip to <?php echo e($currentCity); ?>.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="info-section mb-32">
                    <h3 class="mb-8">Top Reasons to Visit</h3>
                    <?php
                        $topReasonsContent = null;
                        
                        // Check if location exists and extract TopReasonsToVisit content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $topReasonsContent = isset($location->TopReasonsToVisit) && !empty($location->TopReasonsToVisit) ? $location->TopReasonsToVisit : null;
                            } elseif (is_array($location)) {
                                $topReasonsContent = isset($location['TopReasonsToVisit']) && !empty($location['TopReasonsToVisit']) ? $location['TopReasonsToVisit'] : null;
                            }
                        }
                    ?>
                    <?php if(!is_null($topReasonsContent) && !empty($topReasonsContent)): ?>
                        <?php echo $topReasonsContent; ?>

                    <?php else: ?>
                        <?php if($currentCity == 'Dubai'): ?>
                            <ul class="styled-list mb-12">
                                <li>Burj Khalifa: World's tallest building with panoramic views</li>
                                <li>Desert Safaris: Dune bashing, camel rides, Bedouin camps</li>
                                <li>Shopping: Dubai Mall, Mall of the Emirates, traditional souks</li>
                                <li>Palm Jumeirah: Iconic man-made island with luxury resorts</li>
                                <li>Cultural Heritage: Al Fahidi Neighbourhood, Dubai Museum</li>
                                <li>Family Fun: Aquaventure Waterpark, IMG Worlds of Adventure</li>
                                <li>Cuisine: From street shawarma to Michelin-star dining</li>
                            </ul>
                        <?php else: ?>
                            <ul class="styled-list mb-12">
                                <li>Cultural Experiences: Discover the unique heritage of <?php echo e($currentCity); ?></li>
                                <li>Local Cuisine: Sample the distinctive flavors and dishes of the region</li>
                                <li>Scenic Beauty: Explore the natural landscapes and viewpoints</li>
                                <li>Historical Sites: Visit landmarks that tell the story of <?php echo e($currentCity); ?></li>
                                <li>Shopping: Find local crafts, souvenirs, and specialty items</li>
                                <li>Entertainment: Experience the local arts, music, and nightlife</li>
                                <li>Outdoor Activities: Enjoy recreational opportunities in and around <?php echo e($currentCity); ?></li>
                            </ul>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="info-section mb-32">
                    <h3 class="mb-8">Getting Around</h3>
                    <?php
                        $gettingAroundContent = null;
                        
                        // Check if location exists and extract GettingAround content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $gettingAroundContent = isset($location->GettingAround) && !empty($location->GettingAround) ? $location->GettingAround : null;
                            } elseif (is_array($location)) {
                                $gettingAroundContent = isset($location['GettingAround']) && !empty($location['GettingAround']) ? $location['GettingAround'] : null;
                            }
                        }
                    ?>
                    <?php if(!is_null($gettingAroundContent) && !empty($gettingAroundContent)): ?>
                        <?php echo $gettingAroundContent; ?>

                    <?php else: ?>
                        <?php if($currentCity == 'Dubai'): ?>
                            <ul class="styled-list mb-12">
                                <li>Metro: Fast, affordable, connects major sights</li>
                                <li>Taxis & Ride Apps (Careem, Uber): Readily available</li>
                                <li>Hop-on Hop-off Buses: Great for first-time visitors</li>
                                <li>Car Rentals: Convenient but requires confident city driving</li>
                            </ul>
                        <?php else: ?>
                            <ul class="styled-list mb-12">
                                <li>Public Transportation: Explore the local transit options in <?php echo e($currentCity); ?></li>
                                <li>Taxis & Ride Services: Convenient for direct point-to-point travel</li>
                                <li>Walking Tours: Discover <?php echo e($currentCity); ?> on foot for a more intimate experience</li>
                                <li>Rental Options: Consider bikes, scooters, or cars depending on your needs</li>
                            </ul>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="info-section mb-32">
                    <h3 class="mb-8">Insider Tips</h3>
                    <?php
                        $insiderTipsContent = null;
                        
                        // Check if location exists and extract InsiderTips content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $insiderTipsContent = isset($location->InsiderTips) && !empty($location->InsiderTips) ? $location->InsiderTips : null;
                            } elseif (is_array($location)) {
                                $insiderTipsContent = isset($location['InsiderTips']) && !empty($location['InsiderTips']) ? $location['InsiderTips'] : null;
                            }
                        }
                    ?>
                    <?php if(!is_null($insiderTipsContent) && !empty($insiderTipsContent)): ?>
                        <?php echo $insiderTipsContent; ?>

                    <?php else: ?>
                        <?php if($currentCity == 'Dubai'): ?>
                            <ul class="styled-list mb-12">
                                <li>Dress Code: Respectful attire in public areas; swimwear only at pools and beaches.</li>
                                <li>Tipping: Not mandatory but appreciated; ~10% in restaurants is common.</li>
                                <li>WiFi & Connectivity: Free WiFi at most public spaces and malls; tourist SIM cards widely available at airports.</li>
                                <li>Cultural Etiquette: Avoid public displays of affection, especially during Ramadan.</li>
                            </ul>
                        <?php else: ?>
                            <ul class="styled-list mb-12">
                                <li>Local Customs: Familiarize yourself with cultural norms and practices in <?php echo e($currentCity); ?></li>
                                <li>Best Deals: Look for city passes or discount cards for attractions in <?php echo e($currentCity); ?></li>
                                <li>Connectivity: Check mobile network coverage and WiFi availability for travelers</li>
                                <li>Safety Tips: Be aware of common tourist concerns and how to stay safe in <?php echo e($currentCity); ?></li>
                            </ul>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
    </div>
    < Quick Portrait END -->  
    
  <!--BREADCRUMB - START-->
            <?php if(!empty($breadcumb)): ?>
            <div class="tr-breadcrumb-section">
            <div class="container">
              <ul class="tr-breadcrumb">
       		<li><a href="https://www.travell.co">Travell</a></li>              
              <?php if($breadcumb[0]->ccName !=""): ?>
                <li><a href="<?php echo e(route('explore_continent_list',[$breadcumb[0]->contid,$breadcumb[0]->ccName])); ?>"><?php echo e($breadcumb[0]->ccName); ?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo e(route('explore_country_list',[$breadcumb[0]->CountryId,$breadcumb[0]->cslug])); ?>"><?php if(!empty($breadcumb)): ?> <?php echo e($breadcumb[0]->CountryName); ?> <?php endif; ?></a></li>
                <?php if(!empty($locationPatent)): ?>
                <?php
                $locationPatent = $locationPatent;

                ?>
                  <?php $__currentLoopData = $locationPatent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><a href="<?php echo e(route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])])); ?>"><?php echo e($location['Name']); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                <li><?php echo e($breadcumb[0]->LName); ?></li>
              </ul>
			</div>
            </div>
            <?php endif; ?>
          <!--BREADCRUMB - END-->
  <!--FOOTER-->
  <?php echo $__env->make('frontend.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <div class="overlay" id="overLay"></div>

  <!-- Share Modal -->
 <div class="modal" id="shareModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        <h3>Share this experiences</h3>
		    <div class="tr-share-infos">
          <div class="tr-share-details">
            <span class="tr-hotel-name"> <?php if(!empty($searchresults)): ?> <h2 class="tr-title"><?php if($top_attractions == 1): ?>Top Attractions in <?php echo e($lname); ?> 
              <?php else: ?> Things to do in <?php echo e($lname); ?> <?php endif; ?></h2><?php endif; ?></span>
          </div>
        </div>
        <div class="tr-share-options">
          <div class="tr-share-option">
            <a href="javascript:void(0);" class="tr-copy">Copy link</a>
          </div>
          <div class="tr-share-option">
          <a href="#" id="emailShare" target="_blank" class="tr-email">Email</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="smsShare" target="_blank" class="tr-messages">Messages</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="whatsappShare" target="_blank" class="tr-whatsapp">WhatsApp</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="facebookShare" target="_blank" class="tr-facebook">Facebook</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="twitterShare" target="_blank" class="tr-twitter">Twitter</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="messengerShare" target="_blank" class="tr-messenger">Messenger</a>
        </div>
        <div class="tr-share-option">
          <a href="javascript:void(0);" onclick="copyEmbedCode()" class="tr-embed">Embed</a>
        </div>
      </div>

      <!-- Feedback Alerts -->
      <div class="tr-alert tr-copy-alert" id="copyAlert">Link copied</div>
      <div class="tr-alert tr-copy-alert" id="embedAlert">Embed code copied</div>
    </div>
  </div>
</div>
</body>
</html>
	
<script src="<?php echo e(asset('/js/header.js')); ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const firstCard = document.querySelector('.card__Container');
    if (firstCard) {
      firstCard.style.paddingTop = '16px';
    }
  });

</script>
<script>
    // Function to filter attractions by redirecting to URL with xpat parameter
    function filterAttractions() {
        var currentUrl = window.location.href;
        var baseUrl = currentUrl.split('?')[0]; // Remove any existing query parameters
        
        // Check if URL already contains xpat parameter
        if (currentUrl.includes('xpat')) {
            return; // Already filtering attractions
        }
        
        // Add xpat parameter to URL
        if (baseUrl.includes('-')) {
            window.location.href = baseUrl + '-xpat';
        } else {
            window.location.href = baseUrl + '-xpat';
        }
    }

    // Function to handle tab switching and content filtering
    function openTab(tabId, event = null) {
        // Remove active class from all tab buttons
        var tabButtons = document.getElementsByClassName('tab-btn');
        for (var i = 0; i < tabButtons.length; i++) {
            tabButtons[i].classList.remove('active');
        }

        // Add active class to the clicked button or set by tabId
        if (event) {
            event.currentTarget.classList.add('active');
        } else {
            // Find the button with matching tabId and make it active
            for (var i = 0; i < tabButtons.length; i++) {
                if (tabButtons[i].getAttribute('onclick').includes(tabId)) {
                    tabButtons[i].classList.add('active');
                    break;
                }
            }
        }

        // Filter items based on the selected tab
        var allItems = document.querySelectorAll('.card__Container');

        allItems.forEach(function(item) {
            var itemType = item.getAttribute('data-type');

            // Show/hide items based on tab
            if (tabId === 'tab1' && itemType === 'attraction') {
                item.style.display = '';
            } else if (tabId === 'tab2' && itemType === 'experience') {
                item.style.display = '';
            } else if (tabId === 'tab3' && itemType === 'restaurant') {
                item.style.display = '';
            } else if (tabId === 'tab4') {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Initialize filtering when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        var activeTabBtn = document.querySelector('.tab-btn.active');
        if (activeTabBtn) {
            // Extract tab ID from onclick attribute
            var onclick = activeTabBtn.getAttribute('onclick');
            var match = onclick.match(/openTab\('([^']+)'\)/);
            if (match && match[1]) {
                openTab(match[1]);
            }
        } else {
            // No active tab found, activate default (e.g., tab4)
            openTab('tab4');
        }
    });
</script>


<script type="text/javascript" src="<?php echo e(asset('/frontend/hotel-detail/js/common.js')); ?> "></script>
<script src="<?php echo e(asset('/js/restaurant.js')); ?>"></script>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>


<script>
    // Initialize map variables
    var mapInitialized = false;
    var map, defaultIcon, highlightedIcon, defaultIconRes, highlightedIconRes, experienceIcon, experienceHighlightedIcon;
    var markers = {}, restaurantMarkers = {}, experienceMarkers = {};
    var locations = [], restaurantLocations = [], experienceLocations = [];
    
    // Collect location data
    <?php if(!empty($structuredFeed) && count($structuredFeed) > 0): ?>
        <?php $__currentLoopData = $structuredFeed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($feedItem['type']) && $feedItem['type'] !== 'transit' && isset($feedItem['data'])): ?>
                <?php
                    $lat = $feedItem['data']['latitude'] ?? null;
                    $lon = $feedItem['data']['longitude'] ?? null;
                ?>
                <?php if(!empty($lat) && !empty($lon)): ?>
                    locations.push([<?php echo e($lat); ?>, <?php echo e($lon); ?>]);
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php elseif(!empty($searchresults)): ?>
        <?php foreach ($searchresults as $result): ?>
        <?php if (!empty($result->Latitude) && !empty($result->Longitude)): ?>
        locations.push([<?php echo $result->Latitude; ?>, <?php echo $result->Longitude; ?>]);
        <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php $__currentLoopData = $restaurantdata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        restaurantLocations.push({
            lat: <?php echo e($resta['Latitude']); ?>,
            long: <?php echo e($resta['Longitude']); ?>,
            name: '<?php echo e($resta['Title']); ?>',
            city: '<?php echo e($resta['locname']); ?>',
            rating: '<?php echo e($resta['Averagerating']); ?>',
            id: '<?php echo e($resta['RestaurantId']); ?>',
            PriceRange: '<?php echo e($resta['PriceRange']); ?>',
            image: '<?php echo e(asset("/images/Hotel lobby-image.png")); ?>'
        });
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	
   <?php $__currentLoopData = $getexp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $experience): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(!empty($experience['Latitude']) && !empty($experience['Longitude'])): ?>
        experienceLocations.push({
            lat: <?php echo e($experience['Latitude']); ?>,
            long: <?php echo e($experience['Longitude']); ?>,
            name: '<?php echo e($experience['Name']); ?>',
            city: '<?php echo e($lname); ?>',  // Use $lname here
            id: '<?php echo e($experience['ExperienceId']); ?>',
            rating: '<?php echo e($resta['Averagerating'] ?? "No Rating Available"); ?>',
            image: '<?php echo e($experience['Img1'] ?? asset("/images/Hotel lobby.svg")); ?>'
        });
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    // Initialize map when document is ready
    $(document).ready(function() {
        // Lazy load map initialization
        const mapObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !mapInitialized) {
                    initMap();
                    setupEventListeners();
                    mapInitialized = true;
                    mapObserver.disconnect();
                }
            });
        }, { threshold: 0.1 });
        
        // Observe the map container
        const mapContainer = document.getElementById('map1');
        if (mapContainer) {
            mapObserver.observe(mapContainer);
        }
        
        // Initialize immediately if map is already visible
        if (isElementInViewport(document.getElementById('map1'))) {
            initMap();
            setupEventListeners();
            mapInitialized = true;
        }
    });
    
    // Helper function to check if element is in viewport
    function isElementInViewport(el) {
        if (!el) return false;
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Initialize the map
    function initMap() {
        // Set default center and determine actual center
        var defaultCenter = [48.8566, 2.3522]; // Default location (Paris)
        var center = locations.length > 0 ? locations[0] : defaultCenter;
        var isMobile = window.innerWidth <= 768;

        // Create map with appropriate options
        var mapOptions = {
            center: center,
            zoom: isMobile ? 18 : 15 // Adjust zoom level for mobile and non-mobile devices
        };

        map = new L.map('map1', mapOptions);
        var layer = new L.TileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        });

        map.addLayer(layer);

        // Disable scroll zoom and dragging on the map to prevent map movement
        map.scrollWheelZoom.disable();
        map.dragging.disable();

        // Initialize marker icons
        initIcons();
        
        // Initialize all markers
        initMarkers();
        initRestaurantMarkers();
        initExperienceMarkers();
        
        // Update marker icons initially
        updateMarkerIcons();
    }
    
    // Initialize custom marker icons
    function initIcons() {
        // Cache icon URLs for better performance
        const iconUrls = {
            attraction: '<?php echo e(asset('/js/images/3.svg')); ?>',
            attractionHighlighted: '<?php echo e(asset('/js/images/4.svg')); ?>',
            restaurant: '<?php echo e(asset('/js/images/1.svg')); ?>',
            restaurantHighlighted: '<?php echo e(asset('/js/images/1h.svg')); ?>',
            experience: '<?php echo e(asset('/js/images/2.svg')); ?>',
            experienceHighlighted: '<?php echo e(asset('/js/images/2h.svg')); ?>'
        };
        
        defaultIcon = L.icon({
            iconUrl: iconUrls.attraction,
            iconSize: [32, 40]
        });

        highlightedIcon = L.icon({
            iconUrl: iconUrls.attractionHighlighted,
            iconSize: [34, 42]
        });

        defaultIconRes = L.icon({
            iconUrl: iconUrls.restaurant,
            iconSize: [32, 40]
        });

        highlightedIconRes = L.icon({
            iconUrl: iconUrls.restaurantHighlighted,
            iconSize: [34, 42]
        });
        
        experienceIcon = L.icon({
            iconUrl: iconUrls.experience,
            iconSize: [32, 40]
        });
        
        experienceHighlightedIcon = L.icon({
            iconUrl: iconUrls.experienceHighlighted,
            iconSize: [34, 42]
        });
    }
    
    // Set up global event listeners
    function setupEventListeners() {
        // Redirect scroll over the map to the hotel listing
        const mapContainer = document.querySelector('#map1');
        const hotelListingContainer = document.querySelector('.tr-explore-left-section');

        if (mapContainer && hotelListingContainer) {
            mapContainer.addEventListener('wheel', function(event) {
                event.preventDefault();
                hotelListingContainer.scrollBy({
                    top: event.deltaY,
                    behavior: 'auto'
                });
            });
        }
        
        // Map event handlers - use throttled versions for better performance
        map.on('zoomend', throttle(updateMarkerIcons, 100));
        map.on('moveend', throttle(updateMarkerIcons, 100));
        
        // Map button click handler
        $('.tr-explore-map-btn').click(function() {
            $(".tr-explore-listing .tr-map-section").css({
                "display": "block"
            });
            $("body").addClass('modal-open');

            setTimeout(function() {
                map.invalidateSize();
                adjustMapZoom();
            }, 100); // Adding a slight delay to ensure the map is fully visible before recalculating its size
        });
        
        // Window resize handler with throttling for better performance
        window.addEventListener('resize', throttle(adjustMapZoom, 250));
        
        // Initial update on window load
        window.addEventListener('load', updateMarkerIcons);
    }
    
    // Throttle function to limit the rate at which a function can fire
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Initialize attraction markers
    function initMarkers() {
        <?php if(!empty($structuredFeed) && count($structuredFeed) > 0): ?>
            <?php $__currentLoopData = $structuredFeed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $feedItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(isset($feedItem['type']) && $feedItem['type'] !== 'transit' && isset($feedItem['data'])): ?>
                    <?php
                        $itemData = $feedItem['data'];
                        $lat = $itemData['latitude'] ?? null;
                        $lon = $itemData['longitude'] ?? null;
                        $itemId = $itemData['id'] ?? null;
                        $itemTitle = $itemData['title'] ?? '';
                        
                        if (!empty($lat) && !empty($lon)) {
                            $imagePath = asset('/images/Hotellobby-nmustsee-compressed.svg');
                            if (isset($sightImages) && !$sightImages->isEmpty()) {
                                foreach ($sightImages as $sightImage) {
                                    if ($sightImage->Sightid == $itemId) {
                                        $imagePath = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sightImage->Image}&width=280&height=109";
                                        break;
                                    }
                                }
                            }
                        }
                    ?>
                    <?php if(!empty($lat) && !empty($lon)): ?>
                        var name<?php echo e($index); ?> = '<?php echo e(addslashes($itemTitle)); ?>';
                        var cityName<?php echo e($index); ?> = '<?php echo e($lname ?? 'N/A'); ?>';
                        var category<?php echo e($index); ?> = '<?php echo e(ucfirst($feedItem['type'] ?? 'attraction')); ?>';
                        var imagePath<?php echo e($index); ?> = '<?php echo e($imagePath); ?>';
                        
                        // Create marker
                        var marker<?php echo e($index); ?> = new L.Marker([<?php echo e($lat); ?>, <?php echo e($lon); ?>], { icon: defaultIcon });
                        marker<?php echo e($index); ?>.addTo(map);
                        
                        // Set up marker data for later use
                        marker<?php echo e($index); ?>.markerData = {
                            name: name<?php echo e($index); ?>,
                            isRecommend: 'N/A',
                            cityName: cityName<?php echo e($index); ?>,
                            timing: 'N/A',
                            imagePath: imagePath<?php echo e($index); ?>,
                            category: category<?php echo e($index); ?>

                        };

                        // Add event listeners
                        marker<?php echo e($index); ?>.on('click', function(e) {
                            showPopup(e.target);
                        });
                        
                        // Store marker reference
                        markers[<?php echo e($itemId); ?>] = marker<?php echo e($index); ?>;
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php elseif(!empty($searchresults)): ?>
            <?php for ($i = 0; $i < count($searchresults); $i++): ?>
            <?php if (!empty($searchresults[$i]->Latitude) && !empty($searchresults[$i]->Longitude)): ?>
            var name<?php echo $i; ?> = '<?php echo addslashes($searchresults[$i]->Title); ?>';
            var isRecommend<?php echo $i; ?> = document.querySelector('.isrecomd_<?php echo $i; ?>') ? document.querySelector('.isrecomd_<?php echo $i; ?>').textContent : 'N/A';
            var cityName<?php echo $i; ?> = document.querySelector('.cityname_<?php echo $i; ?>') ? document.querySelector('.cityname_<?php echo $i; ?>').textContent.trim() : 'N/A';
            var timing<?php echo $i;?> = document.querySelector('.timing_<?php echo $i;?>') ? document.querySelector('.timing_<?php echo $i;?>').textContent : 'N/A';
            var category<?php echo $i; ?> = document.querySelector('.catname_<?php echo $i; ?>') ? document.querySelector('.catname_<?php echo $i; ?>').textContent.trim() : 'N/A';
            <?php
                $imagePath = asset('/images/Hotellobby-nmustsee-compressed.svg');
                foreach ($sightImages as $sightImage) {
                    if ($sightImage->Sightid == $searchresults[$i]->SightId) {
                        $imagePath = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sightImage->Image}&width=280&height=109";
                        break;
                    }
                }
            ?>
            var imagePath<?php echo $i; ?> = '<?php echo $imagePath; ?>';
            
            // Create marker
            var marker<?php echo $i; ?> = new L.Marker([<?php echo $searchresults[$i]->Latitude; ?>, <?php echo $searchresults[$i]->Longitude; ?>], { icon: defaultIcon });
            marker<?php echo $i; ?>.addTo(map);
            
            // Set up marker data for later use
            marker<?php echo $i; ?>.markerData = {
                name: name<?php echo $i; ?>,
                isRecommend: isRecommend<?php echo $i; ?>,
                cityName: cityName<?php echo $i; ?>,
                timing: timing<?php echo $i; ?>,
                imagePath: imagePath<?php echo $i; ?>,
                category: category<?php echo $i; ?>
            };

            // Add event listeners
            marker<?php echo $i; ?>.on('click', function(e) {
                showPopup(e.target);
            });
            
            // Store marker reference
            markers[<?php echo $searchresults[$i]->SightId; ?>] = marker<?php echo $i; ?>;
            <?php endif; ?>
            <?php endfor; ?>
        <?php endif; ?>
        
        // Add global map click handler to close popups
        map.on('click', function(event) {
            if (!event.originalEvent.target.closest('.leaflet-popup-content')) {
                Object.values(markers).forEach(marker => marker.closePopup());
            }
        });
    }
    
    // Initialize restaurant markers
    function initRestaurantMarkers() {
        restaurantLocations.forEach(function(location) {
            // Create marker
            var marker = L.marker([location.lat, location.long], { icon: defaultIconRes }).addTo(map);
            
            // Store data with marker
            marker.markerData = location;
            
            // Create popup content
            var popupContent = createRestaurantPopup(location);
            
            // Bind popup
            marker.bindPopup(popupContent, {
                offset: L.point(0, -20),
                autoPan: true
            });
            
            // Add event listeners
            marker.on('click', function(e) {
                marker.openPopup();
            });
            
            // Store marker reference
            restaurantMarkers[location.id] = marker;
        });
    }
    
    // Initialize experience markers
    function initExperienceMarkers() {
        experienceLocations.forEach(function(location) {
            // Create marker
            var marker = L.marker([location.lat, location.long], { icon: experienceIcon }).addTo(map);
            
            // Store data with marker
            marker.markerData = location;
            
            // Create popup content
            var popupContent = createExperiencePopup(location);
            
            // Bind popup
            marker.bindPopup(popupContent, {
                offset: L.point(0, -20),
                autoPan: true
            });
            
            // Add event listeners
            marker.on('click', function(e) {
                marker.openPopup();
            });
            
            // Store marker reference
            experienceMarkers[location.id] = marker;
        });
    }

    // Show popup for attraction marker
    function showPopup(marker) {
        var data = marker.markerData;
        showTestName({target: marker}, data.name, data.isRecommend, data.cityName, data.timing, data.imagePath, data.category);
    }

    // Function to show popup content
    function showTestName(e, name, isRecommend, cityName, timing, imagePath, category) {
        var marker = e.target;

        // Use default placeholders for missing data
        var popupContent = `
        <div class="tr-map-tooltip tr-explore-listing" style="top: -214px !important; right: 0; left: 0; margin: auto; font-size: 14px;">
            <div class="tr-historical-monument">
                <div class="tr-heading-with-distance">
                    <div class="tr-category" style="font-size: 12px;">${category || 'Attraction'}</div>
                </div>
                <div class="tr-image">
    <a href="javascript:void(0);">
        ${
            imagePath && !imagePath.includes('Hotellobby-nmustsee-compressed.svg') ?
            `<img src="${imagePath}" 
                  alt="${name || 'Unnamed Attraction'}" 
                  height="109" 
                  width="280" loading="lazy">` :
            `<img src="<?php echo e(asset('/images/Hotel lobby.svg')); ?>" 
                  alt="${name || 'Unnamed Attraction'}"
                  height="109" 
                  width="280" loading="lazy">`
        }
    </a>
</div>
                <div class="tr-details" style="font-size: 14px;">
                    <h3 style="font-size: 16px;">${name || 'Unnamed Attraction'}</h3>
                    <div class="tr-location" style="font-size: 12px;">${cityName || 'Unknown City'}</div>
                    <div class="tr-like-review">
                        <div class="tr-heart">
                            <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99604 2.28959C5.36052 1.20745 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99604 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99604 2.28959Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="tr-ranting-percent" style="font-size: 12px;">${isRecommend || ''}</div>
                    </div>
                </div>
                 <div class="tr-more-inform" style="font-size: 12px;">
                <ul>
                    <li><span>Open:</span> ${timing || ''}</li>
                </ul>
            </div>    
        </div>
        </div>`;

        marker.unbindPopup(); // Unbind existing popups to ensure no conflicts
        marker.bindPopup(popupContent, {
            offset: L.point(0, -20), // Adjust the popup offset for better positioning
            autoPan: true // Ensure the popup stays within the map bounds
        }).openPopup();
    }
    
    // Create restaurant popup content
    function createRestaurantPopup(location) {
        return `
            <div class="tr-map-tooltip tr-explore-listing" style="top: -214px !important; right: 0; left: 0; margin: auto; font-size: 14px;">
                <div class="tr-historical-monument">
                    <div class="tr-heading-with-distance">
                        <div class="tr-category" style="font-size: 12px;">Restaurant</div>
                    </div>
                    <div class="tr-image">
                        <a href="javascript:void(0);">
                            <img loading="lazy" src="${location.image || 'default-image.png'}" alt="${location.name || 'Image'}" height="109" width="280">
                        </a>
                    </div>
                    <div class="tr-details" style="font-size: 14px;">
                        <h3 style="font-size: 16px;">${location.name || 'Unnamed Location'}</h3>
                        <div class="tr-location" style="font-size: 12px;">${location.city || 'Unknown City'}</div>
                        <div class="tr-like-review">
                            <div class="tr-heart">
                                <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99604 2.28959C5.36052 1.20745 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99604 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99604 2.28959Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <div class="tr-ranting-percent" style="font-size: 12px;">
    ${location.rating ? location.rating + '%' : 'No Rating Available'}
</div>
                        </div>
                    </div>
                    <div class="tr-more-inform" style="font-size: 12px;">
                        <ul>
                            <li><span>Open:</span> ${location.PriceRange || 'No Information Available'}</li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Create experience popup content
    function createExperiencePopup(location) {
        return `
    <div class="tr-map-tooltip tr-explore-listing" style="top: -214px !important; right: 0; left: 0; margin: auto; font-size: 14px;">
        <div class="tr-historical-monument">
            <div class="tr-heading-with-distance">
                <div class="tr-category" style="font-size: 12px;">Experience</div>
            </div>
            <div class="tr-image">
                <a href="javascript:void(0);">
                    <img loading="lazy" src="${location.image}" alt="${location.name}" height="109" width="280">
                </a>
            </div>
            <div class="tr-details" style="font-size: 14px;">
                <h3 style="font-size: 16px;">${location.name}</h3>
                <div class="tr-location" style="font-size: 12px;">
                    ${location.city || 'Unknown City'}
                </div>
                <div class="tr-like-review">
                    <div class="tr-heart">
                        <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99604 2.28959C5.36052 1.20745 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99604 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99604 2.28959Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div class="tr-ranting-percent" style="font-size: 12px;">
    ${location.rating ? location.rating + '%' : 'No Rating Available'}
</div>
                </div>
            </div>
        </div>
    </div>
`;
    }

    // Marker highlight functions
    function highlightMarker(element) {
        var sid = element.getAttribute('data-sid');
        var marker = markers[sid];
        if (marker) {
            marker.setIcon(highlightedIcon);

            // Find the marker data using a more efficient approach
            var markerData = marker.markerData;
            if (markerData) {
                // Show the popup with the correct data
                showTestName({target: marker}, markerData.name, markerData.isRecommend, markerData.cityName, markerData.timing, markerData.imagePath, markerData.category);
            }

            marker.openPopup();
            map.panTo(marker.getLatLng());
        }
    }

    function unhighlightMarker(element) {
        var sid = element.getAttribute('data-sid');
        var marker = markers[sid];
        if (marker) {
            marker.setIcon(defaultIcon);
            marker.closePopup();
        }
        updateMarkerIcons();
    }

    function highlightRestaurantMarker(element) {
        var restaurantId = element.getAttribute('data-restaurant-id');
        var marker = restaurantMarkers[restaurantId];
        if (marker) {
            marker.setIcon(highlightedIconRes);
            marker.openPopup();
            map.panTo(marker.getLatLng());
        }
    }

    function unhighlightRestaurantMarker(element) {
        var restaurantId = element.getAttribute('data-restaurant-id');
        var marker = restaurantMarkers[restaurantId];
        if (marker) {
            marker.setIcon(defaultIconRes);
            marker.closePopup();
        }
    }

    function highlightExperienceMarker(element) {
        var experienceId = element.getAttribute('data-experience-id');
        var marker = experienceMarkers[experienceId];
        if (marker) {
            marker.setIcon(experienceHighlightedIcon);
            marker.openPopup();
            map.panTo(marker.getLatLng());
        }
    }

    function unhighlightExperienceMarker(element) {
        var experienceId = element.getAttribute('data-experience-id');
        var marker = experienceMarkers[experienceId];
        if (marker) {
            marker.setIcon(experienceIcon);
            marker.closePopup();
        }
    }

    function updateMarkerIcons() {
        var attractionElements = document.querySelectorAll('.attraction');
        attractionElements.forEach(function(element) {
            var sid = element.getAttribute('data-sid');
            var isMustSee = element.getAttribute('data-ismustsee');

            if (isMustSee === "1") {
                var marker = markers[sid];
                if (marker) {
                    marker.setIcon(defaultIcon);
                }
            }
        });
    }
</script>

<script>
// Handle the results loader
document.addEventListener('DOMContentLoaded', function() {
  // Get the results loader element
  const resultsLoader = document.getElementById('results-loader');
  const resultsContainer = document.getElementById('getcatfilterdata');
  
  // Check if we have search results
  const searchResults = document.querySelectorAll('.tr-museum, .tr-list');
  
  // If we have results, hide the loader after a delay
  if (searchResults.length > 0) {
    // Show the loader for a minimum time to ensure a smooth experience
    setTimeout(function() {
      if (resultsContainer) {
        resultsContainer.classList.add('results-loaded');
      }
    }, 1500); // Show loader for 1.5 seconds minimum
  } else {
    // If no results, still hide the loader
    if (resultsContainer) {
      resultsContainer.classList.add('results-loaded');
    }
  }
  
  // Load more button functionality
  const loadMoreBtn = document.querySelector('.tr-load-more');
  
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
      // Show loading indicator
      if (resultsContainer) {
        resultsContainer.classList.remove('results-loaded');
      }
      
      // Your AJAX call to load more content would go here
      // After content is loaded, the button should be moved to the bottom
      
      // For demonstration, let's simulate loading more content
      setTimeout(function() {
        // Hide loading indicator
        if (resultsContainer) {
          resultsContainer.classList.add('results-loaded');
        }
        
        // Move the load more button container to the end of the content
        const loadingContainer = document.querySelector('.tr-loading-container');
        if (loadingContainer) {
          const parentElement = loadingContainer.parentElement;
          parentElement.appendChild(loadingContainer);
        }
      }, 1000);
    });
  }
});
</script>

<script>
    function hideCarouselControls(id) {
        document.getElementById('carousel-controls-' + id).style.display = 'none';
    }

    function showCarouselControls(id) {
        document.getElementById('carousel-controls-' + id).style.display = 'block';
    }
    
    // Add event listeners for mobile touch events
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.carousel');
        sliders.forEach(slider => {
            const sliderId = slider.id;
            if (sliderId.startsWith('Slider')) {
                const id = sliderId.replace('Slider', '');
                const controls = document.getElementById('carousel-controls-' + id);
                
                if (controls) {
                    // For mobile: show controls when touching the slider
                    slider.addEventListener('touchstart', function() {
                        showCarouselControls(id);
                    });
                }
            }
        });
    });
function toggleMute(button, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const video = button.parentElement.querySelector('video');
    if(video.muted) {
        video.muted = false;
        button.innerHTML = '<i class="fa fa-volume-up"></i>';
    } else {
        video.muted = true;
        button.innerHTML = '<i class="fa fa-volume-off"></i>';
    }
}
</script>

<style>

/* weather */
.weather__section h3 {font-size: 18px; font-weight: 600; color: #000; margin-bottom: 10px;}
 table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      border: 1px solid #E1E3E8;
    }
.weather__section th{
 padding: 11px 22px;
 line-height: 20px;
}
.weather__section td{
 padding: 20px 22px;
}


.temp-divider {
  width: 20px;
  margin: 3px auto;
  border: 1px solid #E1E3E8;
  opacity: 1;
}
.weather__section table th,
.weather__section table td {
  width: 25%;
  box-sizing: border-box;
  text-align: center;
}
    th, td {
      text-align: left;
      border-bottom: 1px solid #E1E3E8;
      vertical-align: middle;
    }

    .weather__section th {
      background-color: #F3F4F6;
      font-weight: 600;
      color: #333;
      font-size: 16px;
      vertical-align: middle;
    }

    td .dot {
      height: 6px;
      width: 6px;
      border-radius: 50%;
      display: inline-block;
      margin-left: 3px;
    }

    .dot.green { background-color: green; }
    .dot.yellow { background-color: gold; }
    .dot.red { background-color: red; }

    /* .temp {
      text-align: center;
    } */

    .temp span {
      display: block;
    }

    .clothing {
      white-space: pre-line;
      font-size: 12px;
    }
        .weather__section{
        margin-top: 60px;
        width: 600px;
      }

    @media (max-width: 600px) {

.weather__section td{padding: 16px 15px;}
.weather__section th{padding: 10px 14px;}
td {position: relative;padding-left: 50%; }
.weather__section table th,
.weather__section table td {width: auto;}
.weather__section{width: 100%;}
}

 </style>     <?php /**PATH C:\wamp64\www\tavelll\resources\views/listing.blade.php ENDPATH**/ ?>