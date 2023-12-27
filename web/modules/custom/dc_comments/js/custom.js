(function ($, Drupal, once) {

  Drupal.behaviors.commentsMore = {
    attach(context) {
      if (!drupalSettings.commentsMore) {
        $(document).ready(function () {
          // Add a click event handler to the Menu heading.
          $(".comments-more", context).click(function () {
            // Find the related menu element within the same parent.
            const MORE = $(this).siblings('.comments-manage-links');
  
            // Check if the more element exists.
            if (MORE.length) {
              // Check if the elements are visible.
              if (MORE.is(":visible")) {
                // Use fadeOut to hide both elements.
                MORE.fadeOut();
              } else {
                // Use fadeIn to show both elements.
                MORE.fadeIn();
              }
            }
          });
        });
        drupalSettings.commentsMore = true;
      }
    }
  };

  Drupal.behaviors.preventClickOnImage = {
    attach(context) {
      if (!drupalSettings.preventClickOnImage) {
        $(document).ready(function () {
          // Add a click event handler to avoid click on user profile img.
          $(".comment__picture a", context).click(function (event) {
            event.preventDefault();
          });
        });
        drupalSettings.preventClickOnImage = true;
      }
    }
  };

})(jQuery, Drupal, once);
