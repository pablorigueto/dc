(function ($, Drupal, once) {

  Drupal.behaviors.payments = {
    attach: function (context, settings) {
			if (drupalSettings.payments) {
				return;
			}

			drupalSettings.payments = TRUE;

			// if () {

			// }



		}
	}

})(jQuery, Drupal, once);
