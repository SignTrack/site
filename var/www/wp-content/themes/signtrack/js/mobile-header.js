function clear_mobiles() {
	jQuery('#inner').on('click', function() {
		var elem = jQuery('#wrap');

		if (elem.hasClass('inner-shifted-left')) {
			elem.animate({ 'left' : '+=50%'}, 300, function() {
				elem.removeClass('inner-shifted-left');
			});
			jQuery('.mobile-contact-container').animate({ 'left' : '+=50%'});
		}

		if (elem.hasClass('inner-shifted-right')) {
			elem.animate({ 'left' : '-=50%'}, 300, function() {
				elem.removeClass('inner-shifted-right');
			});
			jQuery('.mobile-nav-container').animate({ 'right' : '+=50%'});
		}
	});
}






function reveal_mobile_nav() {
	jQuery('.mobile-header-nav-button').on('click', function(e) {
		e.preventDefault();

		var elem = jQuery('#wrap');

		/**
		* Check to see if the contact button was pushed already
		*/
		if (elem.hasClass('inner-shifted-left')) {
			jQuery('.mobile-contact-container').animate({ 'left' : '+=50%'});
			elem.animate({ 'left' : '+=50%'}, 300, function() {
				elem.removeClass('inner-shifted-left');
				elem.addClass('inner-shifted-right').animate({ 'left' : '+=50%'});
				jQuery('.mobile-nav-container').animate({ 'right' : '-=50%'});
			});

		}

		else {

			/**
			* Shift right to reveal nav
			*/
			if (elem.hasClass('inner-shifted-right')) {
				elem.animate({ 'left' : '-=50%'}, 300, function() {
					elem.removeClass('inner-shifted-right');
				});
				jQuery('.mobile-nav-container').animate({ 'right' : '+=50%'});
			}
			/**
			* Shift to original position
			*/
			else {
				elem.addClass('inner-shifted-right').animate({ 'left' : '+=50%'}, 300);
				jQuery('.mobile-nav-container').animate({ 'right' : '-=50%'});
			}


		}


	});
}


function reveal_mobile_contact() {
	jQuery('.mobile-header-contact-button').on('click', function(e) {
		e.preventDefault();

		var elem = jQuery('#wrap');

		/**
		* Check to see if currently displaying nav
		*/
		if (elem.hasClass('inner-shifted-right')) {

			jQuery('.mobile-nav-container').animate({ 'right' : '+=50%'});
			elem.animate({ 'left' : '-=50%'}, 300, function() {
				elem.removeClass('inner-shifted-right');
				elem.animate({ 'left' : '-=50%'}).addClass('inner-shifted-left');
				jQuery('.mobile-contact-container').animate({ 'left' : '-=50%'});

			} );
		}

		else {

			/**
			* Shift left to reveal the contact menu
			*/
			if (elem.hasClass('inner-shifted-left')) {
				elem.animate({ 'left' : '+=50%'}, 300, function() {
					elem.removeClass('inner-shifted-left');
				});
				jQuery('.mobile-contact-container').animate({ 'left' : '+=50%'});
			}
			/**
			* Shift back to original position
			*/
			else {
				elem.animate({ 'left' : '-=50%'}).addClass('inner-shifted-left');
				jQuery('.mobile-contact-container').animate({ 'left' : '-=50%'}, 300);
			}
		}



	});
}
