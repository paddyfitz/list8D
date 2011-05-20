jumpToTop = $("<li><span class='link icon icon-arrow-stop-090'>jump to top</span></li>");
jumpToBottom = $("<li><span class='link icon icon-arrow-stop-270'>jump to bottom</span></li>");

$('#actions')
  .clone()
  .attr('id','actions-fixed')
  .css({
  	'position':'fixed',
  	'top':'20px'})
  .hide()
  .appendTo('#sidebar');
  
$('.link',jumpToBottom).click(function() { 
	$(window).scrollTop($('#footer').position().top);
});
$('.link',jumpToTop).click(function() { 
	$(window).scrollTop(0);
	$('#actions-fixed').hide();
});
$('#sidebar').bind('inview', function (event, visible) {
  if (visible == true) {
	  // element is now visible in the viewport
  	if ($('#actions-fixed').is(':visible')) {
  	  $('#actions-fixed').fadeOut();
  	}
  } else {
    // element has gone out of viewport
    $('#actions-fixed').fadeIn();
  }
});
$('#actions .links')
  .prepend(jumpToBottom);
$('#actions-fixed .links')
  .prepend(jumpToBottom.clone(true));
$('#actions-fixed .links')
  .prepend(jumpToTop);
$(window).scroll();