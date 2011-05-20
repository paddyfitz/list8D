(function($) {
	
	$('.view-tools li a').click(function() {
		$('#box-holder').removeClass('small-list').removeClass('medium-list').removeClass('medium-list').addClass($(this).parent().attr('class')+"-list");
		$(this).parent().siblings().find("a").removeClass('active');
		$(this).addClass("active");
		return false;
	});
	
})(jQuery);