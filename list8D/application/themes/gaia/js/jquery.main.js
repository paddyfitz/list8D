$(document).ready(function(){
	$('a.opener').fancybox({
		'hideOnContentClick': false,
		'titleShow'     : false,
		'showCloseButton':false
	});
	var _closer = $('div.lightbox').find('a.close');
		_closer.click($.fancybox.close);
		_closer.click(function(){
			return false;
		});
});