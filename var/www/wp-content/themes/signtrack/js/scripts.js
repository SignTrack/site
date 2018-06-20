// @codekit-prepend "jquery.min.js"
// @codekit-prepend "smooth-scroll.js"
// @codekit-prepend "fixed-nav.js"
// @codekit-prepend "mobile-header.js"
// @codekit-prepend "widebox.js"
// @codekit-prepend "fade-in.js"


/**
 * Check to see if elem is above the bottom
 * of the view port
 */
function widely_in_view(elem) {

	elem = jQuery(elem);

	var win = $(window);

	var viewport = {
		top : win.scrollTop(),
		left : win.scrollLeft()
	};
	viewport.right = viewport.left + win.width();
	viewport.bottom = viewport.top + window.innerHeight;
	var bounds = elem.offset();
	bounds.right = bounds.left + elem.outerWidth();
	bounds.bottom = bounds.top + elem.outerHeight();

	return (bounds.bottom < viewport.bottom);
}



function widelyAddHero(url) {
	if (!(jQuery('body').hasClass('single') || jQuery('body').hasClass('page') ) ) {
		return;
	}
	jQuery('body').addClass('body-hero-image').css({
		'background-image': "url(" + url + ")",
		'background-position-x' : '100%',
		'background-position-y' : '25%',
	});
}

function widelyAddHomepageHero(url) {
	if (!jQuery('body').hasClass('home')) {
		return;
	}

	jQuery('body').addClass('body-hero-image').css({
		'background-image': "url(" + url + ")",
		'background-position-x' : 'center',
		'background-position-y' : 'top',
		'background-size' : '100%',
	});
}


function widelyAddHomepageHeroParallax(url) {

	if ((jQuery('body').hasClass('home'))) {
		jQuery('.homepage-parallax-header').css('display', 'block');
		jQuery('body').addClass('body-hero-image').addClass('body-hero-parallax');
		$(window).load(function() {

			var image = jQuery('.homepage-parallax-header').find('img');
			var desHeight = image.height() - jQuery('#header').height() - 100;

			if (jQuery('body').hasClass('iphone') ) {

				image.parent().css({
					'bottom': '-30px !important'
				});
				console.log('ihpone kill parallax');
				return;
			}
			image.css({
				'top': '-' + desHeight + 'px',
				'left':'center'
			});
			jQuery(document).bind('scroll', function() {
				var yPos = (window.scrollY)/jQuery(document).height();
				//var yShift = 100 - 5 * (widelyMap(parseFloat(yPos), 0, 1, 0, 100));
				var yShift = (-1 * desHeight) + 5 * (widelyMap(parseFloat(yPos), 0, 1, 0, desHeight));
				if (yShift <= 0) {
					image.animate({
						'top' : yShift + 'px',
					}, 25);
				}
				else {
					image.css({
						'top' : '0px',
					});
				}

			});
		});
	}
	else {
		jQuery('.homepage-parallax-header').remove();
		return;
	}


	// if (!jQuery('body').hasClass('home')) {
	// 	jQuery('.homepage-parallax-header').empty();
	// 	return;
	// }
	// jQuery('body').addClass('body-hero-image').addClass('body-hero-parallax');
	// var image = jQuery('.homepage-parallax-header').find('img');
	// var desHeight = image.height() - jQuery('#header').height() - 100;
	// image.css({
	// 	'top': '-' + desHeight + 'px',
	// 	'left':'center'
	// });
	// jQuery(document).bind('scroll', function() {
	// 	var yPos = (window.scrollY)/jQuery(document).height();
	// 	//var yShift = 100 - 5 * (widelyMap(parseFloat(yPos), 0, 1, 0, 100));
	// 	var yShift = (-1 * desHeight) + 5 * (widelyMap(parseFloat(yPos), 0, 1, 0, desHeight));
	// 	if (yShift <= 0) {
	// 		image.animate({
	// 			'top' : yShift + 'px',
	// 		}, 25);
	// 	}
	// 	else {
	// 		image.css({
	// 			'top' : '0px',
	// 		});
	// 	}
	//
	// });

	// jQuery('body').addClass('body-hero-image').addClass('body-hero-parallax').css({
	// 	'background-image': "url(" + url + ")",
	// 	'background-position-x' : 'center',
	// 	'background-position-y' : '0%',
	// 	'background-size' : 'inherit',
	// });
	//
	// if (window.outerWidth < 768) {
	// 	jQuery('body').animate({
	// 		'background-position-y' : '0%',
	// 	}, 10);
	// 	return;
	// }
	//
	// jQuery(document).bind('scroll', function() {
	//
	// 	var yPos = (window.scrollY)/jQuery(document).height();
	// 	//var yShift = 100 - 5 * (widelyMap(parseFloat(yPos), 0, 1, 0, 100));
	// 	var yShift = -50 * (widelyMap(parseFloat(yPos), 0, 1, 0, 100));
	//
	// 	jQuery('body').animate({
	// 		'background-position-y' : yShift + '%',
	// 	}, 10);
	// });
}


function widelyAddHeroParallax(url) {
	if (!jQuery('body').hasClass('single')) {
		return;
	}
	jQuery('body').addClass('body-hero-parallax');
	widelyAddHero(url);

	if (jQuery('body').hasClass('iphone') ) {
		console.log('iphone kill parallax');
		return;
	}

	jQuery(document).bind('scroll', function() {

		var yPos = (window.scrollY)/jQuery(document).height();
		var yShift = 100 - 5 * (widelyMap(parseFloat(yPos), 0, 1, 0, 100));

		jQuery('body').animate({
			'background-position-y' : yShift + '%',
		}, 10);
	});
}

function widelyAddHeroParallaxMouseTrack(url) {
	if (!jQuery('body').hasClass('single')) {
		return;
	}
	jQuery('body').addClass('body-hero-parallax').css('background-size', '120%');
	widelyAddHero(url);

	jQuery(document).bind('mousemove', function(e) {

		var xPos = (e.pageX)/jQuery(window).width();
		var xShift = widelyMap(parseFloat(xPos), 0, 1, -10, 10) + 50;
		var yPos = (e.pageY)/jQuery(window).height();
		var yShift = widelyMap(parseFloat(yPos), 0, 1, -10, 10) + 50;

		jQuery('body').animate({
			'background-position-x' : xShift + "%",
			'background-position-y' : yShift + '%',
		}, 10);
	});
}

function widelyMap(val, set1Min, set1Max, set2Min, set2Max) {
	return ( ( ((set2Max - set2Min) * (val - set1Min))/(set1Max - set1Min) ) + set2Min );
}






function signtrackMobileNav() {
	console.log(jQuery(window).outerWidth());

	if (jQuery(window).outerWidth() < 754) {
		console.log("Activate Mobile");
		var nav = jQuery('#menu-primary-1');
		var navContainer = nav.parent();
		navContainer.prepend('<div class="mobile-nav-button"><i class="fa fa-bars"></i></div>');
		nav.hide().addClass('hidden-nav');

		jQuery('.mobile-nav-button').on('click', function() {

			if (nav.hasClass('hidden-nav')) {
				nav.show(800).removeClass('hidden-nav');
			}
			else {
				nav.hide(800).addClass('hidden-nav');
			}


		})

	}
}






jQuery(document).ready(function(){
	attach_widebox_functionality();
	widely_fade_in();
	attach_fixed_nav();
	reveal_mobile_nav();
	reveal_mobile_contact();
	clear_mobiles();
	attachSmoothScroll();
	signtrackMobileNav();

	var qtys = jQuery('.quantity');
	qtys.each(function() {
		// jQuery(this).html(jQuery(this).html() + '<p>1</p>');
		// jQuery(this).find('input').css('display', 'none').attr('value', 1);
	});
});



function setupCampaign(email, fname, lname, phone, packageName, signLimit) {
    // jQuery.ajax(
    // {
    //     url: "http://signtrackapp.com/manage/campaign/handler/format/html",
    //     data: {
    //         email: email,
    //         fname: fname,
    //         lname: lname,
    //         phone: phone,
	// 		package_name: packageName,
	// 		sign_limit: signLimit,
    //         type:'setup'
    //     },
    //     type: "POST",
    //     success: function(response){
    //         if(response.length==4){
    //             jQuery('.signtrack-password').html('<pre>'+response+'</pre>');
    //         }else{
    //             //error
    //             alert("Sorry, there was an error setting up your account. Please call (xxx)xxx-xxxx");
	// 			jQuery('.signtrack-password').html('<pre>Please call (xxx)xxx-xxxx</pre>');
    //         }
    //     }
    // });
}
