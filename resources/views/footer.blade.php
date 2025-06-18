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
<footer>
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <div class="tr-footer-top">
            <div class="tr-footer-logo">
              <a href="https://www.travell.co/"><img 
                  src="{{asset('/frontend/hotel-detail/images/travell-white-logo.png')}}"
                  alt="travell-white-logo"></a>
              <div class="tr-copy-right tr-mobile">&copy; 2025 Travell.co, Inc.</div>
            </div>
            <div class="tr-footer-links-section">
              <div class="tr-footer-links-left-col">
                <div class="tr-footer-links">
                  <h5>About</h5>
                  <ul>
                    <li><a href="https://www.travell.co/about-us">About Us</a></li>
                    <li><a href="https://www.travell.co/blog">Blog</a></li>
                    <li><a href="https://www.travell.co/career">Careers</a></li>
					  <li><a href="{{route('contact_us')}}">Contact Us</a></li>
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
                <li><a href="{{route('privacy_policy')}}">Privacy</a></li>
                <li><a href="{{route('term_condition')}}">Terms</a></li>
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
  </footer>