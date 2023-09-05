(function ($, Drupal) {

  // Settings to carousel owl.
  Drupal.behaviors.carousels = {
    attach() {
      if (!drupalSettings.carousels) {
        $(document).ready(function () {
          // alert('teste');
        });
        drupalSettings.carousels = true;
      }
    }
  }


})(jQuery, Drupal);
