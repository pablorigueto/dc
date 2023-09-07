(function ($, Drupal, once) {

  Drupal.behaviors.toggleMenu = {
    attach(context) {
      if (!drupalSettings.toggleMenu) {
        $(document).ready(function () {
          // Hide all language options initially
          $(".menu.menu--level-1").hide();

          // Add a click event handler to the Menu heading
          $("h2.block__title").click(function () {
            // Find the related menu element within the same parent
            var menu = $(this).siblings(".menu.menu--level-1");

            // Toggle the visibility of the menu associated with the clicked heading
            menu.slideToggle();
          });
        });
        drupalSettings.toggleMenu = true;
      }
    }
  }



})(jQuery, Drupal, once);

