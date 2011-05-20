jumpToTop = $("<li><span class='link icon icon-arrow-stop-090'>jump to top</span></li>");
jumpToBottom = $("<li><span class='link icon icon-arrow-stop-270'>jump to bottom</span></li>");

$('#advanced-actions')
  .clone()
  .attr('id','advanced-actions-fixed')
  .css({
  	'position':'fixed',
  	'top':'90px'})
  .hide()
  .appendTo('#sidebar');
  
$('#create-item')
  .clone()
  .attr('id','create-item-fixed')
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
	$('#advanced-actions-fixed').hide();
});
$('#sidebar').bind('inview', function (event, visible) {
  if (visible == true) {
	  // element is now visible in the viewport
  	if ($('#create-item-fixed').is(':visible')) {
  	  $('#create-item-fixed').fadeOut();
  	}
  	if ($('#advanced-actions-fixed').is(':visible')) {
  	  $('#advanced-actions-fixed').fadeOut();
  	}
  } else {
    // element has gone out of viewport
    $('#advanced-actions-fixed').fadeIn();
    $('#create-item-fixed').fadeIn();
  }
});
$('#advanced-actions .info-box .links')
  .prepend(jumpToBottom);
$('#advanced-actions-fixed .info-box .links')
  .prepend(jumpToBottom.clone(true));
$('#advanced-actions-fixed .info-box .links')
  .prepend(jumpToTop);
$(window).scroll();