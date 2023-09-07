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
  
  Drupal.behaviors.MyModule = {
    attach: function attach(context) {
      once('myModuleClick', '.search-block-form', context).forEach(element => {
      	element.addEventListener('click', e => {
          const INPUT = $('input.form-search', context);
          const SEARCH = $('.search-block-form');
          if (!INPUT.is(e.target)) {
            // If clicked outside, hide the search block form.
            SEARCH.hide();
          }
        });
      });
    }
  };




})(jQuery, Drupal, once);

// Drupal.behaviors.toggleMenu = {
//   attach(context) {
//     if (!drupalSettings.toggleMenu) {
//       $(document).ready(function () {
//         // Hide all language options initially.
//         //Commented because set the default state hide on theme of css.
//         //$(".menu.menu--level-1").hide();

//         // Add a click event handler to the Menu heading.
//         $("h2.block__title").click(function () {
//           // Find the related menu element within the same parent.
//           const MENU = $(this).siblings(".menu.menu--level-1");

//           // Toggle the visibility of the menu associated with the clicked heading.
//           MENU.slideToggle();
//         });
//       });
//       drupalSettings.toggleMenu = true;
//     }
//   }
// }
