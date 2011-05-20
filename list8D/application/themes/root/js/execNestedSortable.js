jQuery(function($){
	
	// remove existing move links
	$('.sortable-item').prepend('<img src="/themes/root/images/fugue/arrow-move.png" class="move" />');
	
	
$('#list-items').NestedSortable({
		accept: 'sortable-item',
		handle: '.move',
		noNestingClass: "not-list",
		nestingPxSpace: 20,
		onChange : function(s) {
			
			list8D.saveOrder(list8D.params.id,s[0].o);
			
    },
	});



});
