

document.addEventListener('DOMContentLoaded', function() {
    const banner = document.getElementById('cookie-consent-banner');
    
    
    const consent = getCookie('cookie_consent');

    if (consent === null) {
        
        if (banner) {
            banner.style.display = 'flex';
        }
    } else {
        
        if (banner) {
            banner.style.display = 'none';
        }
        
    }

    
    const acceptBtn = document.getElementById('cookie-accept');
    if (acceptBtn) {
        acceptBtn.addEventListener('click', function() {
            setCookie('cookie_consent', 'accepted', 365); 
            banner.style.display = 'none';
            
            console.log("Cookie consent: Accepted. Starting tracking/analytics.");
        });
    }

   
    const rejectBtn = document.getElementById('cookie-reject');
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            setCookie('cookie_consent', 'rejected', 365); 
            banner.style.display = 'none';
            
            console.log("Cookie consent: Rejected. Blocking tracking/analytics.");
        });
    }

    

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
});