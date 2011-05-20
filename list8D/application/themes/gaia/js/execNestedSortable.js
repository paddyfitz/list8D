jQuery(function($){
	
	// remove existing move links
	//$('.sortable-item').prepend('<img src="/themes/root/images/fugue/arrow-move.png" class="move" />');

	$('#list-items').NestedSortable({
		accept: 'sortable-item',
		handle: '.move',
		noNestingClass: "not-list",
		nestingPxSpace: 20,
		applyNesting: false,
		onChange : function(s) {
			list8D.saveOrder(list8D.params.id,s[0].o);
			list_items = $(".list-items");
			list_items.each(function() {
				if ($(this).children('*:not(p.no-items)').length) {
					$(this).children('p.no-items').remove();		
				} else if (!$(this).children('p.no-items').length) {
					$(this).append('<p class="no-items">There are no items on this list yet.</p>');		
				}
			});
    }
	});

	$('#dragHelper').addClass('list-items').wrap('<div class="box-holder small-list" />');
	$('a.move').click(function(){return false;});

	$('img.notes-icon').hover(function(){
		$(this).parent().siblings('.notes').css({
			position: 'absolute',
			top: ($(this).position().top+20)+"px",
			left: $(this).position().left+"px"
		}).show()
	},function(){
		$(this).parent().siblings('.notes').hide();		
	}).removeAttr("title").removeAttr('alt');

});

/*
jQuery(function($){
	
	$('.list-items').NestedSortable({
		accept: 'sortable-item',
		handle: '.move',
		noNestingClass: "not-list",
		nestingPxSpace: 16,
		applyPadding: true,
		onChange : function(s) {
			//list8D.saveOrder(list8D.params.id,s[0].o);
    },
	});

});
*/
