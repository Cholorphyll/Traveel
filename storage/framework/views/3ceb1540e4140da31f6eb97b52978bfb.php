<style>
html, body {
    min-height: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    position: relative;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.container {
    flex: 1 0 auto;
}

footer {
    flex-shrink: 0;
    width: 100%;
    position: relative;
    bottom: 0;
}

@media screen and (max-width: 768px) {
    body {
        min-height: 100vh;
    }
    
    footer {
        margin-top: auto;
    }
    
    .tr-footer-top {
        padding-bottom: 20px;
    }
}
	@media screen and (max-width: 768px) {
    footer {
        position: static;
        width: 100%;
        margin-top: 20px;
    }
    
    .tr-footer-top {
        padding-bottom: 20px;
    }
}
</style>

<div id="trGeoPrompt" style="display:none; position:fixed; inset:0; z-index:999999;">
    <div id="trGeoPromptBackdrop" style="position:absolute; inset:0; background:rgba(0,0,0,0.55);"></div>
    <div style="position:relative; margin: 0 auto; max-width: 520px; padding: 16px; height: 100%; display:flex; align-items:flex-end;">
        <div style="width:100%; background:#fff; border-radius:16px; padding:16px; box-shadow:0 10px 30px rgba(0,0,0,0.2);">
            <div style="font-size:16px; font-weight:600; color:#111; margin-bottom:6px;">Use your location?</div>
            <div style="font-size:13px; color:#555; line-height:1.4; margin-bottom:12px;">We use it to show weather and personalized recommendations near you.</div>
            <label style="display:flex; gap:10px; align-items:flex-start; font-size:13px; color:#444; margin-bottom:12px;">
                <input id="trGeoDontAsk" type="checkbox" style="margin-top:2px;" />
                <span>Don’t ask again</span>
            </label>
            <div style="display:flex; gap:10px;">
                <button id="trGeoAllowBtn" type="button" style="flex:1; border:0; padding:12px 14px; border-radius:12px; background:#111; color:#fff; font-weight:600;">Allow location</button>
                <button id="trGeoLaterBtn" type="button" style="flex:1; border:1px solid #ddd; padding:12px 14px; border-radius:12px; background:#fff; color:#111; font-weight:600;">Not now</button>
            </div>
        </div>
    </div>
</div>

<script>
  (function(){
    function isMobile(){
      try{ return window.matchMedia && window.matchMedia('(max-width: 768px)').matches; }catch(e){ return false; }
    }
    function getCookie(name){
      try{
        const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
      }catch(e){ return null; }
    }
    function setCookie(name, value, maxAgeSeconds){
      try{
        let cookie = name + '=' + encodeURIComponent(value) + '; Path=/; SameSite=Lax';
        if (typeof maxAgeSeconds === 'number') cookie += '; Max-Age=' + maxAgeSeconds;
        if (location && location.protocol === 'https:') cookie += '; Secure';
        document.cookie = cookie;
      }catch(e){}
    }
    function hasStoredGeo(){
      const lat = getCookie('tr_user_lat');
      const lng = getCookie('tr_user_lng');
      if (lat && lng) return true;
      try{
        const ll = localStorage.getItem('tr_user_geo');
        if(!ll) return false;
        const parsed = JSON.parse(ll);
        return parsed && typeof parsed.lat === 'number' && typeof parsed.lng === 'number';
      }catch(e){ return false; }
    }
    function storeGeo(lat, lng){
      const latStr = String(lat);
      const lngStr = String(lng);
      setCookie('tr_user_lat', latStr, 60 * 60 * 24 * 30);
      setCookie('tr_user_lng', lngStr, 60 * 60 * 24 * 30);
      setCookie('tr_user_geo_ts', String(Date.now()), 60 * 60 * 24 * 30);
      try{
        localStorage.setItem('tr_user_geo', JSON.stringify({lat:Number(lat), lng:Number(lng), ts:Date.now()}));
      }catch(e){}
    }
    function hidePrompt(){
      const el = document.getElementById('trGeoPrompt');
      if(el) el.style.display = 'none';
    }
    function showPrompt(){
      const el = document.getElementById('trGeoPrompt');
      if(el) el.style.display = 'block';
    }

    document.addEventListener('DOMContentLoaded', function(){
      if (!isMobile()) return;
      if (!('geolocation' in navigator)) return;
      if (hasStoredGeo()) return;
      if (getCookie('tr_geo_dismissed') === '1') return;

      const allowBtn = document.getElementById('trGeoAllowBtn');
      const laterBtn = document.getElementById('trGeoLaterBtn');
      const dontAsk = document.getElementById('trGeoDontAsk');
      const backdrop = document.getElementById('trGeoPromptBackdrop');
      if(!allowBtn || !laterBtn || !dontAsk) return;

      function dismiss(){
        const ttl = (dontAsk && dontAsk.checked) ? (60 * 60 * 24 * 180) : (60 * 60 * 24 * 7);
        setCookie('tr_geo_dismissed', '1', ttl);
        hidePrompt();
      }

      allowBtn.addEventListener('click', function(){
        allowBtn.disabled = true;
        allowBtn.textContent = 'Getting location...';
        navigator.geolocation.getCurrentPosition(
          function(pos){
            const lat = pos && pos.coords ? pos.coords.latitude : null;
            const lng = pos && pos.coords ? pos.coords.longitude : null;
            if (typeof lat === 'number' && typeof lng === 'number') {
              storeGeo(lat, lng);
            }
            hidePrompt();
            try{ location.reload(); }catch(e){}
          },
          function(){
            allowBtn.disabled = false;
            allowBtn.textContent = 'Allow location';
            dismiss();
          },
          { enableHighAccuracy: false, timeout: 10000, maximumAge: 60 * 60 * 1000 }
        );
      });
      laterBtn.addEventListener('click', dismiss);
      if(backdrop){ backdrop.addEventListener('click', dismiss); }
      showPrompt();
    });
  })();
</script>

<footer>
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <div class="tr-footer-top">
            <div class="tr-footer-logo">
              <a href="https://www.travell.co/">
				<picture>
                <source srcset="<?php echo e(asset('/frontend/hotel-detail/images/travell-white-logo.webp')); ?>" type="image/webp">
                <img src="<?php echo e(asset('/frontend/hotel-detail/images/travell-white-logo.webp')); ?>" alt="travell-white-logo" loading="lazy" class="lcp-image">
               </picture>
			 </a>
              <div class="tr-copy-right tr-mobile">&copy; 2025 Travell.co, Inc.</div>
            </div>
            <div class="tr-footer-links-section">
              <div class="tr-footer-links-left-col">
                <div class="tr-footer-links">
                  <h5>About</h5>
                  <ul>
                    <li><a href="https://www.travell.co/about-us">About Us</a></li>
                    
                    <li><a href="https://www.travell.co/career">Careers</a></li>
					  <li><a href="<?php echo e(route('contact_us')); ?>">Contact Us</a></li>
                  </ul>
                </div>
              </div>
              <div class="tr-footer-links-right-col">
                <div class="tr-footer-links">
                  <h5>Rooms</h5>
                  <ul>
                    <li><a href="https://www.travell.co/stays">Hotels</a></li>
                    <li><a href="https://www.travell.co/stays">Motels</a></li>
                    <li><a href="https://www.travell.co/stays">Apartments</a></li>
                  </ul>
                </div>
                <div class="tr-footer-links">
                  <h5>Explore</h5>
                  <ul>
                    <li><a href="https://www.travell.co/lo-129700020031-london-england">London</a></li>
                    <li><a href="https://www.travell.co/lo-113600100005-paris-ile-de-france">Paris</a></li>
                    <li><a href="https://www.travell.co/lo-131300370558-new-york-city-new-york-ny">New York</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="tr-footer-bottom">
            <div class="tr-another-links">
              <ul>
                <li>&copy; 2025 Travell.co</li>
                <li><a href="<?php echo e(route('privacy_policy')); ?>">Privacy</a></li>
                <li><a href="<?php echo e(route('term_condition')); ?>">Terms</a></li>
              </ul>
            </div>
            <div class="tr-social-links">
              <ul>
                <li>English (US)</li>
                <li>(&#36) USD</li>
                <li><a href="https://www.facebook.com/mytravellco/" class="tr-facebook" title="Facebook" target="_blank"></a></li>
                <li><a href="https://x.com/wwwTravellco" class="tr-twitter" title="Twitter" target="_blank"></a></li>
                <li><a href="https://www.instagram.com/wwwtravellco" class="tr-instagram" title="Instagram" target="_blank"></a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer><?php /**PATH C:\wamp64\www\tavelll\resources\views/frontend/footer.blade.php ENDPATH**/ ?>