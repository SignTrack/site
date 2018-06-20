function attach_widebox_close() {

	jQuery('.widebox-action').on('click', function(click) {
		click.stopPropagation();
	});

	jQuery('.widebox-display').on('click', function(e) {
		e.stopPropagation();
		jQuery(this).remove();
	});
}

function attach_widebox_functionality() {
	jQuery('.widebox-trigger').on('click', function() {

		var elem = jQuery(this);
		var widebox = elem.parent();

		var action = widebox.find('.widebox-action');

		var display = action.clone();

		display.addClass('widebox-action-active');

		var displayContainer = jQuery('<div/>', {class: 'widebox-display'}).html(display);
		widebox.append(displayContainer);

		attach_widebox_close();

	});
}
