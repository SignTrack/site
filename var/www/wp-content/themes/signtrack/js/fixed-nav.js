function attach_fixed_nav() {

	if (jQuery('.widely-header-sidebar').length === 0 && jQuery('body').hasClass('fixed-nav')) {
		jQuery(window).bind('scroll', function() {
			var navTop = jQuery('#nav').position().top;
			if (window.outerWidth < 768) {
				return;
			}
			var headerBottom = jQuery('#header').position().top + jQuery('#header').height();
			if (jQuery(window).scrollTop() > navTop) {

				if ( jQuery('#nav.fixed').length === 0) {
					jQuery('#nav').clone().appendTo('#wrap').addClass('fixed').css('top', jQuery('#header').position().top);
					attachSmoothScroll();
				}

				if (jQuery('.header-right-nav').length != 0) {
					var opac = Math.floor((jQuery(window).scrollTop() - navTop)/5);
					if (opac > 100) {
						opac = 100;
					}
					jQuery('#nav.fixed').css('opacity', opac/50);
				}
			}

			else if (jQuery(window).scrollTop() < (navTop) ) {
				jQuery('#nav.fixed').remove();
			}

			else {
				jQuery('#nav').removeClass('fixed');
			}
		});
	}
}
