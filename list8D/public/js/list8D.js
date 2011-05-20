jQuery(function($){
	
	list8D.orderSaved = true;
	list8D.orderSaving = false;
	list8D.orderToSave = false;
	list8D.lastOrderSent = false;
	list8D.saveOrder = function (id,order) {
	
		// The list has changed
		list8D.orderSaved = false;

		if (!list8D.orderSaving) {
		
		  list8D.orderSaving = true;
			list8D.lastOrderSent = order;
		  $.ajax({
		  	cache: false,
		  	url: "/admin/api/",
		  	type: "POST",
		  	data: {
		  		method: "reorderList",
		  		json: true,
		  		cache: false,
		  		listStructure: order,
		  		listId: id
		  	},
		  	dataType: 'json',
		  	success: function (data,textStatus,XMLHttpRequest) {

		  		list8D.orderSaving = false;
		  		if(data.status!='success') {
		  			return this.error(XMLHttpRequest,data.List8D_API.reorderList.status,data.List8D_API.reorderList.response.message);
		  		} else {

		  			if (list8D.orderToSave) {
		  				list8D.saveOrder(list8D.params.id,list8D.orderToSave);
			  			list8D.orderToSave = false;
		  			} 
		  		}
		  	},
		  	error: function (XMLHttpRequest, textStatus, errorThrown) {
		  		list8D.orderSaving=false;
		  	}
		  });
		  
		} else {
			
			list8D.orderSaved = false;
			list8D.orderToSave = order;
			
		}
	}
	
	$(window).unload(function() {

		if(list8D.orderToSave) {
			list8D.saveOrder(list8D.params.id,list8D.orderToSave);
		}
	});
	
	$().ready(function() {
		$('body').addClass('js');
	});
	
});

