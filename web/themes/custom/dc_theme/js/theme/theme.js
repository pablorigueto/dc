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
            console.log('teste');
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

  Drupal.behaviors.contentRedirect = {
    attach(context) {
      if (!drupalSettings.contentRedirect) {
        $(document).ready(function () {
          // Add a click event handler to the Menu heading.
          $(".path-frontpage .text_and_image").on('click', function () {
          // Get the href attribute from the anchor within the clicked element.
          var link = $(this).find('a').attr('href');
            // Redirect to the specified URL
            window.location.href = link;
          });
        });
        drupalSettings.contentRedirect = true;
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
              console.log('clicado');
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
          //const NODE_SEARCH = $('.path-node .search-block-form');
          const SEARCH = $('body:not(:has(.path-frontpage)) .search-block-form');
          if (!INPUT.is(e.target)) {
            // If clicked outside, hide the search block form.
            SEARCH.slideUp();
            //NODE_SEARCH.slideUp();
          }
        });

        // Add a keydown event handler to close the menu when pressing ESC.
        $(document).on('keydown', function (event) {
          if (event.key === 'Escape' || event.keyCode === 27) {
            //$(".path-node .search-block-form").slideUp();
            $("body:not(:has(.path-frontpage)) .search-block-form").slideUp();
          }
        });

      });
    }
  };

  /* Modal to copy text and show modal quickly */
  // Drupal.behaviors.copyCode = {
  //   attach: function attach(context) {
  //     once('copyCode', '.page-node-type-page pre', context).forEach(element => {
  //       element.addEventListener('click', async e => {
  //         // Assuming you want to copy the content of the <pre> element
  //         let copyText = element.textContent;
  //         try {
  //           // Use the Clipboard API to copy the text
  //           await navigator.clipboard.writeText(copyText);

  //           $("#block-dc-theme-copycode").fadeIn();
 
  //           // Re-add the 'none' display style after a 1-second delay
  //           setTimeout(function() {
  //             // $("#block-dc-theme-copycode").slideDown();
  //             $("#block-dc-theme-copycode").fadeOut();
  //           }, 1000); // 1000 milliseconds = 1 second
            
  //         }
  //         catch (err) {
  //           console.error('Failed to copy text: ', err);
  //         }
  //       });
  //     });
  //   }
  // };

  // Drupal.behaviors.slickCarousel = {
  //   attach: function attach(context) {
  //     $(".homepage-highlight-container").slick({
  //       dots: false,
  //       infinite: true,
  //       speed: 600,
  //       slidesToShow: 1,
  //       autoplay: true,
  //       autoplaySpeed: 4000,
  //       fade: true,
  //       cssEase: 'linear'
  //     });
  //   }
  // };

  Drupal.behaviors.smoothPosition = {
    attach: function attach(context) {

      // Check if the behavior has already been applied
      if (context !== document) {
        return;
      }
    
      const SEARCH_BLOCK = document.querySelector('.search-block-form');
    
      if (SEARCH_BLOCK && !SEARCH_BLOCK.classList.contains('show')) {
        SEARCH_BLOCK.classList.add('show');
      }
  
      const HIGHTLIGHT_CONTAINER = document.querySelector('.homepage-highlight-container');
      
      if (HIGHTLIGHT_CONTAINER && !HIGHTLIGHT_CONTAINER.classList.contains('show')) {
        HIGHTLIGHT_CONTAINER.classList.add('show');
      }

    }
  };

  Drupal.behaviors.addCopyCodeBtn = {
    attach: function attach(context) {
      once('addCopyCodeBtn', '.page-node-type-page pre', context).forEach(element => {
        $(element).append("<div class='btnFromJs'><span>copy</span></div>");
      });
    }
  };

  Drupal.behaviors.copyCode = {
    attach: function attach(context) {
      once('copyCode', '.page-node-type-page pre .btnFromJs', context).forEach(element => {
        element.addEventListener('click', async e => {
  
          // Append the 'popFromJs' div to the parent <pre> element
          let preElement = element.closest('pre');

          if ($(preElement).find('.popFromJs').length === 0) {
            $(preElement).append("<div class='popFromJs'><span>copied!</span></div>");
          }
  
          // Get the newly appended 'popFromJs' div.
          let copyCodeDiv = $(preElement).find('.popFromJs');
  
          // Exclude both '.btnFromJs' and '.popFromJs' elements when copying text.
          let copyText = $(preElement)
            .clone()
            .find('.btnFromJs, .popFromJs')
            .remove()
            .end()
            .text();
  
          try {
            // Use the Clipboard API to copy the text
            await navigator.clipboard.writeText(copyText);
  
            // Fade in the cloned copyCodeDiv
            copyCodeDiv.fadeIn();
  
            // Re-add the 'none' display style after a 1-second delay
            setTimeout(function() {
              copyCodeDiv.fadeOut();

            }, 800); // 800 milliseconds = 0.8 seconds
  
          }
          catch (err) {
            console.error('Failed to copy text: ', err);
          }
        });
      });
    }
  };




})(jQuery, Drupal, once);


// Drupal.behaviors.copyCode = {
//   attach: function attach(context) {
//     once('copyCode', '.page-node-type-page pre', context).forEach(element => {
//       element.addEventListener('click', async e => {
//         // Assuming you want to copy the content of the <pre> element
//         let copyText = element.textContent;
//         try {
//           // Use the Clipboard API to copy the text
//           await navigator.clipboard.writeText(copyText);

//           $("#block-dc-theme-copycode").fadeIn();

//           // Re-add the 'none' display style after a 1-second delay
//           setTimeout(function() {
//             // $("#block-dc-theme-copycode").slideDown();
//             $("#block-dc-theme-copycode").fadeOut();
//           }, 1000); // 1000 milliseconds = 1 second
          
//         }
//         catch (err) {
//           console.error('Failed to copy text: ', err);
//         }
//       });
//     });
//   }
// };