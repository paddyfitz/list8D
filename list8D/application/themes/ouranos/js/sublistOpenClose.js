jQuery(function($){

	button = $("<div class='open-close'>close sublist</div>");

	
	$('.list-items-title').prepend(button).addClass('open');

	$('.open-close').click(function() {	
		var button = $(this);
		var parent = button.parent();
		if (parent.hasClass('open')) {
			parent.removeClass('open');
			parent.addClass('closed');
			parent.siblings('.list-items-content').hide();
			button.html("open sublist");
		} else {
			parent.removeClass('closed');
			parent.addClass('open');
			parent.siblings('.list-items-content').show();
			button.html("close sublist");
		}
	});
	
});
