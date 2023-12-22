(function ($, Drupal, once) {

  Drupal.behaviors.toggleMenu = {
    attach(context) {
      if (!drupalSettings.toggleMenu) {
        $(document).ready(function () {
          // Add a click event handler to the Menu heading.
          $(".primary_menu h2", context).click(function () {
            console.log('teste');
            // Find all open menus and close them.
            $(".primary_menu ul:visible", context).not($(this).siblings("ul")).slideUp();

            // Find the related menu element within the same parent.
            const MENU = $(this).siblings("ul", context);
  
            // Check if this menu is already open.
            const isMenuOpen = MENU.is(":visible");
    
            // If the menu was not open, then open it.
            if (!isMenuOpen) {
              MENU.slideDown();
            }
            else {
              MENU.slideUp();
            }
          });
          // /* To mobile Accordion. */
          // $(".primary_menu h2#block-dc-theme-planos-menu").click(function () {
          //   // Toggle the 'rotate' class on click
          //   $(this).addClass('rotate'); 
          // });
        });
        drupalSettings.toggleMenu = true;
      }
    }
  }

  // Drupal.behaviors.toggleMenu = {
  //   attach(context) {
  //     if (!drupalSettings.toggleMenu) {
  //       $(document).ready(function () {
  //         // Add a click event handler to the Menu headings.
  //         $(".primary_menu h2", context).click(function () {
  
  //           // Find all open menus and close them.
  //           $(".primary_menu ul:visible", context).not($(this).siblings("ul")).slideUp();
  
  //           // Find the related menu element within the same parent.
  //           const MENU = $(this).siblings("ul", context);
  
  //           // Toggle the visibility of the menu.
  //           MENU.slideToggle();
  //         });
  //       });
  //       drupalSettings.toggleMenu = true;
  //     }
  //   }
  // }; 

  Drupal.behaviors.addDefaultLike = {
    attach(context) {
      if (!drupalSettings.addDefaultLike) {
        $(document).ready(function () {
          // Check if the span with id 'like-1' is empty
          // let like1Span = $('.path-frontpage .like_dislike span[id^="like-"], ' +
          //  '.path-search .like_dislike span[id^="like-"]', context);
          let like1Span = $('.like_dislike span[id^="like-"]', context);

          if (like1Span.text().trim() === '') {
            // If it's empty, add the value 0
            like1Span.text('0');
          }
        });
        drupalSettings.addDefaultLike = true;
      }
    }
  }

  Drupal.behaviors.openSearch = {
    attach(context) {
      if (!drupalSettings.openSearch) {
        $(document).ready(function () {
          let BANNER = $('.basicpage_header_container');
          // Add a click event handler to the Menu heading.
          // $("#block-dc-theme-searchbtn-2", context).click(function () {
          $("#block-dc-theme-searchbtn-2, #search_mobile_icon", context).click(function () {


            // Search to open.
            const MENU = $('.search-block-form', context);
            
            // Check if this menu is already open.
            const isMenuOpen = MENU.is(":visible");
    
            // If the menu was not open, then open it.
            if (!isMenuOpen) {
              MENU.fadeIn();
              BANNER.addClass('hidden');
            }
            else {
              MENU.fadeOut();
              BANNER.removeClass('hidden');
            }
          });
        });
        drupalSettings.openSearch = true;
      }
    }
  }

  Drupal.behaviors.contentRedirect = {
    attach(context) {
      if (!drupalSettings.contentRedirect) {
        $(document).ready(function () {
          // Add a click event handler to the Menu heading.
          $(".path-frontpage .text_and_image", context).on('click', function () {
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
 
          // Add a click event handler to the Menu heading.
          $(".menu--search-btn", context).click(function () {

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
  
  Drupal.behaviors.pressEscToCloseIt = {
    attach: function attach(context) {
      let BANNER = $('.path-node .basicpage_header_container');
      once('pressEscToCloseIt', '.search-block-form', context).forEach(element => {
        let searchForm = $("body:not(:has(.path-frontpage)) .search-block-form");
        // Add a keydown event handler to close the menu when pressing ESC.
        $(document).on('keydown', function (event) {
          if (event.key === 'Escape' || event.keyCode === 27) {
            BANNER.removeClass('hidden');
            searchForm.slideUp();
          }
        });
      });
    }
  };

  Drupal.behaviors.mobileMenu = {
    attach(context) {
      if (!drupalSettings.mobileMenu) {
        $(document).ready(function () {
  
          // Add a click event handler to the Menu heading.
          $(".mobile", context).click(function () {
  
            // Find the related menu element within the same parent.
            const MENU = $(this).siblings(".primary_menu");

            // Check if the menu element exists.
            if (MENU.length) {
  
              // Toggle the menu visibility.
              MENU.slideToggle();
            }
          });
        });
        drupalSettings.mobileMenu = true;
      }
    }
  };
  
  
  // Drupal.behaviors.smoothPosition = {
  //   attach: function attach(context) {

  //     // Check if the behavior has already been applied
  //     if (context !== document) {
  //       return;
  //     }
    
  //     const SEARCH_BLOCK = document.querySelector('.search-block-form');
    
  //     if (SEARCH_BLOCK && !SEARCH_BLOCK.classList.contains('show')) {
  //       SEARCH_BLOCK.classList.add('show');
  //     }
  
  //     const HIGHTLIGHT_CONTAINER = document.querySelector('.homepage-highlight-container');
      
  //     if (HIGHTLIGHT_CONTAINER && !HIGHTLIGHT_CONTAINER.classList.contains('show')) {
  //       HIGHTLIGHT_CONTAINER.classList.add('show');
  //     }

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
