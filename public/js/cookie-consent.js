// Cookie Consent Banner
const cookieConsent = {
  init: function() {
    if (!this.getCookie('cookie_consent')) {
      this.showBanner();
    }
  },

  showBanner: function() {
    const banner = document.createElement('div');
    banner.id = 'cookie-consent-banner';
    banner.innerHTML = `
      <div class="cookie-content">
        <p>This website uses cookies and other tracking technologies to improve your browsing experience for the following purposes: 
        to enable basic functionality of the website, to provide a better experience on the website, to measure your interest in our 
        products and services and to personalize marketing interactions, to deliver ads that are more relevant to you.</p>
        <div class="cookie-buttons">
          <button id="accept-cookies" class="cookie-btn accept">Accept</button>
          <button id="decline-cookies" class="cookie-btn decline">Decline</button>
        </div>
      </div>
    `;
    document.body.appendChild(banner);

    document.getElementById('accept-cookies').addEventListener('click', () => this.setConsent(true));
    document.getElementById('decline-cookies').addEventListener('click', () => this.setConsent(false));
  },

  setConsent: function(accepted) {
    document.getElementById('cookie-consent-banner').style.display = 'none';
    this.setCookie('cookie_consent', accepted ? 'accepted' : 'declined', 365);
  },

  setCookie: function(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
  },

  getCookie: function(name) {
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
      const [cookieName, cookieValue] = cookie.trim().split('=');
      if (cookieName === name) return cookieValue;
    }
    return null;
  }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  cookieConsent.init();
});
