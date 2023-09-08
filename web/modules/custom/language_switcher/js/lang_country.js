(function ($, Drupal, once) {

  Drupal.behaviors.langMenuCustomBehavior = {
    attach: function (context, settings) {
      let currentUrl = window.location.href;
      // Create a URL object
      let url = new URL(currentUrl);
      let parts = url.pathname.split('/');
      let urlLangCode = parts[1];
      
      // Check if localStorage supports the selectedLanguage key.
      if (typeof localStorage !== 'undefined' && localStorage.getItem('selectedLanguage')) {
        // Retrieve the selectedLanguage value from localStorage.
        let language = localStorage.getItem('selectedLanguage');

        if (urlLangCode !== language) {
          setLangIcon(urlLangCode, context);
        }
        else {
          setLangIcon(language, context);
        }
      }
      else {
        $('#block-dc-theme-language-menu').each(function() {
          setLangIcon(urlLangCode, context);
        });
      }

      $(document).ready(function() {
        // Loop through each anchor tag and change its href based on the title attribute.
        $('#block-dc-theme-language ul.menu li a').each(function() {
          let title = $(this).attr('title');
          // Assuming you have some logic to determine the new href based on the title.
          const NEWHREF = '/' + title;
          $(this).attr('href', NEWHREF);
        });
      });

    }
  };

  function setLangIcon(langCode, context) {
    $('#block-dc-theme-language-menu', context).addClass(langCode + '__ls');
    $("h2#block-dc-theme-language-menu").text(langCode.toUpperCase());
  }

  Drupal.behaviors.storageLangCodeOnBrowser = {
    attach: function (context, settings) {
      once('storageLangCodeOnBrowser', '#block-dc-theme-language .menu__item--link', context).forEach(element => {
        element.addEventListener('click', function (e) {
          const LANGUAGE = $(this).find(".menu__link--link").prop('title');
          let selectedLanguageCookie = getCookie("selectedLanguage");

          // Remove the existing selectedLanguage cookie (if it exists)
          // and if they are the same, we don't need to change it.
          if (selectedLanguageCookie === '/' + LANGUAGE) {
            return;
          }

          deleteCookie("selectedLanguage");
  
          // Set the new value in both localStorage and as a new cookie
          localStorage.setItem('selectedLanguage', LANGUAGE);
          setCookie("selectedLanguage", '/' + LANGUAGE, 365);

        });
      });
    }
  };

  function setCookie(name, value, daysToExpire) {
    const expirationDate = new Date();
    expirationDate.setTime(expirationDate.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
    const expires = "expires=" + expirationDate.toUTCString();
    document.cookie = name + "=" + value + "; " + expires + "; path=/";
  }

  // Function to delete a cookie
  function deleteCookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
  }

  // Function to get a cookie by name
  function getCookie(name) {
    const cookies = document.cookie.split('; ');
    for (const cookie of cookies) {
      const [cookieName, cookieValue] = cookie.split('=');
      if (cookieName === name) {
        return cookieValue;
      }
    }
    return null;
  }

})(jQuery, Drupal, once);
