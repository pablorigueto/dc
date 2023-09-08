(function ($, Drupal, once) {

  Drupal.behaviors.toggleMenu = {
    attach(context) {
      if (!drupalSettings.toggleMenu) {
        $(document).ready(function () {
          // Hide all language options initially.
          // Commented because set the default state hide on the theme of CSS.
          //$(".menu.menu--level-1").hide();
  
          // Add a click event handler to the Menu heading.
          $("h2.block__title").click(function () {
            // Find the related menu element within the same parent.
            const MENU = $(this).siblings(".menu.menu--level-1");
  
            // Check if this menu is already open.
            const isMenuOpen = MENU.is(":visible");
  
            // Hide all menus.
            $(".menu.menu--level-1").slideUp();
  
            // If the menu was not open, then open it.
            if (!isMenuOpen) {
              MENU.slideDown();
            }
          });
        });
        drupalSettings.toggleMenu = true;
      }
    }
  }
  
  Drupal.behaviors.toggleSearch = {
    attach(context) {
      if (!drupalSettings.toggleSearch) {
        $(document).ready(function () {
          // Hide all language options initially.
          // Commented because set the default state hide on the theme of CSS.
          //$(".search-block-form").hide();
  
          // Add a click event handler to the Menu heading.
          $(".menu--search-btn").click(function () {
            // Find the related menu element within the same parent.
            const MENU = $(this).siblings(".search-block-form");
  
            // Check if this menu is already open.
            const isMenuOpen = MENU.is(":visible");
  
            // Hide all menus.
            $(".search-block-form").slideToggle();
  
            // If the menu was not open, then open it.
            if (!isMenuOpen) {
              MENU.slideDown();
            }
          });
        });
        drupalSettings.toggleSearch = true;
      }
    }
  }
  
  Drupal.behaviors.closeSearchClickingOutsideOfit = {
    attach: function attach(context) {
      once('closeSearchClickingOutsideOfit', '.search-block-form', context).forEach(element => {
      	element.addEventListener('click', e => {
          const INPUT = $('input.form-search', context);
          const SEARCH = $('.search-block-form');
          if (!INPUT.is(e.target)) {
            // If clicked outside, hide the search block form.
            SEARCH.slideUp();
          }
        });
      });
    }
  };

  // Drupal.behaviors.storageLangCodeOnBrowser = {
  //   attach: function (context, settings) {
  //     once('storageLangCodeOnBrowser', '#block-dc-theme-language .menu__item--link', context).forEach(element => {
  //       element.addEventListener('click', function (e) {
  //         const LANGUAGE = $(this).find(".menu__link--link").prop('title');
  //         let selectedLanguageCookie = getCookie("selectedLanguage");

  //         // Remove the existing selectedLanguage cookie (if it exists)
  //         if (selectedLanguageCookie) {
  //           deleteCookie("selectedLanguage");
  //         }
  
  //         // Set the new value in both localStorage and as a new cookie
  //         localStorage.setItem('selectedLanguage', LANGUAGE);
  //         setCookie("selectedLanguage", '/' + LANGUAGE, 365);

  //       });
  //     });
  //   }
  // };

  // function setCookie(name, value, daysToExpire) {
  //   const expirationDate = new Date();
  //   expirationDate.setTime(expirationDate.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
  //   const expires = "expires=" + expirationDate.toUTCString();
  //   document.cookie = name + "=" + value + "; " + expires + "; path=/";
  // }

  // // Function to delete a cookie
  // function deleteCookie(name) {
  //   document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
  // }

  // // Function to get a cookie by name
  // function getCookie(name) {
  //   const cookies = document.cookie.split('; ');
  //   for (const cookie of cookies) {
  //     const [cookieName, cookieValue] = cookie.split('=');
  //     if (cookieName === name) {
  //       return cookieValue;
  //     }
  //   }
  //   return null;
  // }




  // Drupal.behaviors.langMenuCustomBehavior = {
  //   attach: function (context, settings) {
  //     // Check if localStorage supports the selectedLanguage key.
  //     if (typeof localStorage !== 'undefined' && localStorage.getItem('selectedLanguage')) {
  //       // Retrieve the selectedLanguage value from localStorage.
  //       const LANGUAGE = localStorage.getItem('selectedLanguage');

  //       // Set the value as a class for the nav#block-dc-theme-language-menu element.
  //       $('#block-dc-theme-language-menu', context).addClass(LANGUAGE + '__ls');

  //       if (LANGUAGE == 'en-us') {
  //         $("h2#block-dc-theme-language-menu").text("EN-US");
  //       }
  //     }

  //     $(document).ready(function() {
  //       // Loop through each anchor tag and change its href based on the title attribute.
  //       $('#block-dc-theme-language ul.menu li a').each(function() {
  //         let title = $(this).attr('title');
  //         // Assuming you have some logic to determine the new href based on the title.
  //         const NEWHREF = '/' + title;
  //         $(this).attr('href', NEWHREF);
  //       });
  //     });

  //   }
  // };



})(jQuery, Drupal, once);
