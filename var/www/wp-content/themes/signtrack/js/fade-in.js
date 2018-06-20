

function widely_fade_in() {


	jQuery(window).bind('scroll', function() {

		var elems = jQuery('.widely-fade');

		elems.each(function( i,n ) {

			if (widely_in_view(n) && !jQuery(n).hasClass("fade-active")) {

				var target = jQuery(n);


				if (target.hasClass('fade-in-up')) {
					target.addClass('fadeInUp animated fade-active').removeClass('fade-in-up');
					target.animate({'opacity': '+=1'}, 300);
				}
				else if (target.hasClass('fade-in-down')) {
					target.addClass('fadeInDown animated fade-active').removeClass('fade-in-Down');
					target.animate({'opacity': '+=1'}, 300);
				}
				else if (target.hasClass('fade-in-left')) {
					target.addClass('fadeInLeft animated fade-active fade-active').removeClass('fade-in-Down');
					target.animate({'opacity': '+=1'}, 300);

				}
				else if (target.hasClass('fade-in-right')) {
					target.addClass('fadeInRight animated fade-active').removeClass('fade-in-Down');
					target.animate({'opacity': '+=1'}, 300);
				}
				else {
					target.addClass('fade-active');
					target.animate({'opacity': '+=1'}, 300);
				}
			}

		});

	});
}
