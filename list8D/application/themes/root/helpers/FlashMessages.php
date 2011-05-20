<?php

class List8D_Theme_Root_Helper_FlashMessages extends List8D_ViewHelper {
	
	public function flashMessages($clear = true) {
		$output = "";
		$messages = array_merge($this->view->flashMessenger->getMessages(),$this->view->flashMessenger->getCurrentMessages());
		foreach($messages as $message) {
			$output .= $this->view->flashMessage($message);
		}
		if ($clear) {
			$this->view->flashMessenger->clearMessages();
			$this->view->flashMessenger->clearCurrentMessages();
		}
		$output .= "<script type='text/javascript'>
			var close = $('<div class=\"close\"><span class=\"text\">close</span></div>');
			$('.flashMessage.closable').append(close);
			$('.flashMessage .close').click(function() {
				//$(this).parent().slideUp('slow');
				$(this).parent().animate({ 
    			paddingBottom: '0',
    			paddingTop: '0',
    			marginBottom: '0',
    			marginTop: '0',
			    opacity: '0',
	  	  	height: '0px'
			  }, 'fast',function() { $(this).remove(); }); 
			});</script>";
		return $output;
	}
	
}
