(function ($, Drupal, once) {

  // Update the langcode on Cookie.
  Drupal.behaviors.updateLangOnCookie = {
    attach: function (context) {
      // Get the div element
      const LANGUAGE_SWITCHER = document.getElementById('block-dc-theme-languageswitcher', context);

      // Check if the element exists.
      if (!LANGUAGE_SWITCHER) {
        return;
      }

      // Add click event listener to the div.
      LANGUAGE_SWITCHER.addEventListener('click', function(event) {
        // Check if the clicked element is a link.
        if (event.target.tagName === 'A') {

          // Get the lang attr.
          const LINK_DESTINATION = event.target.getAttribute('hreflang');

          const TRIMMED_SEGMENTS = getPathSegmentsDestination();

          // If the link destination and the current link are the same, just avpid the redirect.
          if (TRIMMED_SEGMENTS[1] === LINK_DESTINATION) {
            //Prevent the default link behavior.
            event.preventDefault();
          }

          // Delete the Cookie before to update him.
          deleteCookie("selectedLanguage");
          // Set the new value to cookie.
          setCookie("selectedLanguage", '/' + LINK_DESTINATION, 365);

        }
      });
    }
  };

  // Set langCode base on GeoIP or Default lang site.
  Drupal.behaviors.setDefaultLangOnCookie = {
    attach: function () {
      $(document).ready(function() {

        manageCookie(getLangCodeFromUrl());

      });
    }
  };

  // Hidden the language en, using js, to avoid issues on user data.
  Drupal.behaviors.languageEnHidden = {
    attach(context) {
      if (!drupalSettings.languageEnHidden) {
        $(document).ready(function () {
          // Find the list item with hreflang="en"
          let listItem = document.querySelector('ul.links li[hreflang="en"]');
          
          // Check if the list item is found
          if (listItem) {
            // Set display to none
            listItem.style.display = 'none';
          }
        });
        drupalSettings.languageEnHidden = true;
      }
    }
  };

  // Default title of language switcher.
  Drupal.behaviors.defaultLanguageTitleSwitcher = {
    attach: function (context) {
      let selectedLanguageCookie = getCookie("selectedLanguage");

      if (selectedLanguageCookie) {
        const TITLE = selectedLanguageCookie.split('/');
        updateLanguageTitleSwitcher(TITLE[1].trim(), context);
        return;
      }

      const DESTINATION = getPathSegmentsDestination();
      if (DESTINATION) {
        updateLanguageTitleSwitcher(DESTINATION[1].trim(), context);
        return;
      }

    }
  };

  function updateLanguageTitleSwitcher(selectedLanguageCookie, context) {
    // Update the title with the value of selectedLanguageCookie.
    $(".language-switcher-language-url h2", context).text(selectedLanguageCookie);
  }

  function getPathSegmentsDestination() {

    // Get the current URL.
    const CURRENT_URL = window.location.href;

    // Get the base URL.
    const BASE_URL = window.location.origin;

    // Get the relative path (current URL without the base URL).
    const RELATIVE_PATH = CURRENT_URL.replace(BASE_URL, '');

    // Split the relative path into segments.
    const PATH_SEGMENTS = RELATIVE_PATH.split('/');

    // Trim each segment.
    return PATH_SEGMENTS.map(function(segment) {
      return segment.trim();
    });

  }

  function getLangCodeFromUrl() {
    let currentUrl = window.location.href;
    // Create a URL object
    let url = new URL(currentUrl);
    let parts = url.pathname.split('/');
    return parts[1];
  }

  function manageCookie(LANGUAGE) {
    let selectedLanguageCookie = getCookie("selectedLanguage");
    // Remove the existing selectedLanguage cookie (if it exists)
    // and if they are the same, we don't need to change it.
    if (selectedLanguageCookie === '/' + LANGUAGE) {
      return;
    }

    deleteCookie("selectedLanguage");

    // Set the new value to cookie.
    //localStorage.setItem('selectedLanguage', LANGUAGE);
    setCookie("selectedLanguage", '/' + LANGUAGE, 365);
  }

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
