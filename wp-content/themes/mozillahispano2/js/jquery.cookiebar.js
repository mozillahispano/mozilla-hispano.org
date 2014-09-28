/*
 * Copyright (C) 2012 PrimeBox (info@primebox.co.uk)
 *
 * This work is licensed under the Creative Commons
 * Attribution 3.0 Unported License. To view a copy
 * of this license, visit
 * http://creativecommons.org/licenses/by/3.0/.
 *
 * Documentation available at:
 * http://www.primebox.co.uk/projects/cookie-bar/
 *
 * When using this software you use it at your own risk. We hold
 * no responsibility for any damage caused by using this plugin
 * or the documentation provided.
 */
(function(jQuery) {
  jQuery.cookieBar = function(options, val) {
    if (options === 'cookies') {
      var doReturn = 'cookies';
    } else if (options === 'set') {
      var doReturn = 'set';
    } else {
      var doReturn = false;
    }
    var defaults = {
      message: 'Usamos cookies propias y de terceros para mejorar tu experiencia y realizar tareas de analítica. Al continuar navegando entendemos que aceptas nuestra política de cookies.', //Message displayed on bar
      autoEnable: true, //Set to true for cookies to be accepted automatically. Banner still shows
      expireDays: 365, //Number of days for cookieBar cookie to be stored for
      forceShow: false, //Force cookieBar to show regardless of user cookie preference
      effect: 'slide', //Options: slide, fade, hide
      element: 'body', //Element to append/prepend cookieBar to. Remember "." for class or "#" for id.
      append: false, //Set to true for cookieBar HTML to be placed at base of website. Actual position may change according to CSS
      fixed: true, //Set to true to add the class "fixed" to the cookie bar. Default CSS should fix the position
      bottom: true, //Force CSS when fixed, so bar appears at bottom of website
      zindex: '99', //Can be set in CSS, although some may prefer to set here
      redirect: String(window.location.href), //Current location
      domain: String(window.location.hostname), //Location of privacy policy
      referrer: String(document.referrer) //Where visitor has come from
    };
    var options = jQuery.extend(defaults, options);

    //Sets expiration date for cookie
    var expireDate = new Date();
    expireDate.setTime(expireDate.getTime() + (options.expireDays * 24 * 60 * 60 * 1000));
    expireDate = expireDate.toGMTString();

    var cookieEntry = 'cb-enabled={value}; expires=' + expireDate + '; path=/';

    //Retrieves current cookie preference
    var i, cookieValue = '',
      aCookie, aCookies = document.cookie.split('; ');
    for (i = 0; i < aCookies.length; i++) {
      aCookie = aCookies[i].split('=');
      if (aCookie[0] === 'cb-enabled') {
        cookieValue = aCookie[1];
      }
    }
    //Sets up default cookie preference if not already set
    if (cookieValue === '' && options.autoEnable) {
      cookieValue = 'enabled';
      document.cookie = cookieEntry.replace('{value}', 'enabled');
    }

    if (doReturn === 'cookies') {
      //Returns true if cookies are enabled, false otherwise
      if (cookieValue === 'enabled' || cookieValue === 'accepted') {
        return true;
      } else {
        return false;
      }
    } else if (doReturn === 'set' && (val === 'accepted' || val === 'declined')) {
      //Sets value of cookie to 'accepted' or 'declined'
      document.cookie = cookieEntry.replace('{value}', val);
      if (val === 'accepted') {
        return true;
      } else {
        return false;
      }
    } else {
      //Sets up enable/accept button if required
      var message = options.message.replace('{policy_url}', options.policyURL);

      //Sets up privacy policy button if required
      if (options.policyButton) {
        var policyButton = '<a href="' + options.policyURL + '" class="cb-policy">' + options.policyText + '</a>';
      } else {
        var policyButton = '';
      }
      //Whether to add "fixed" class to cookie bar
      if (options.fixed) {
        var fixed = ' class="fixed"';
      }

      if (options.zindex !== '') {
        var zindex = ' style="z-index:' + options.zindex + ';"';
      } else {
        var zindex = '';
      }
      //Displays the cookie bar if arguments met
      if (options.forceShow || cookieValue === 'enabled' || cookieValue === '') {
        if (options.append) {
          jQuery(options.element).append('<div id="cookie-bar"' + fixed + zindex + '><p>' + message + '</p></div>');
        } else {
          jQuery(options.element).prepend('<div id="cookie-bar"' + fixed + zindex + '><p>' + message + '</p></div>');
        }
      }

      //Sets the cookie preference to accepted if enable/accept button pressed
      if (cookieValue !== 'accepted') {
        jQuery(window).scroll(function() {
          document.cookie = cookieEntry.replace('{value}', 'accepted');
          if (cookieValue !== 'enabled' && cookieValue !== 'accepted') {
            window.location = options.currentLocation;
          } else {
            jQuery('#cookie-bar').delay(2000).slideUp(300, function() {
              jQuery('#cookie-bar').remove();
            });
            return false;
          }
        });
      }
    }
  };
})(jQuery);
