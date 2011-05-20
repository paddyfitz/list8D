<?php

class List8D_Theme_Root_Helper_FlashMessage extends List8D_ViewHelper {
	
	public function flashMessage($message) {
		if (!empty($message['type']))
			$type = $message['type'];
		else 
			$type = "";
		if (isset($message['closable']) && $message['closable']===false) {
			$closable = "";
		} else {
			$closable = "closable";
		}
		$output = "<div class='flashMessage clearfix $type $closable'>";
		switch($message['type']) {
			case "ok":
				$output .= "<img src='{$this->view->baseUrl}/themes/gaia/images/ok-icon-small.png' />";
				break;
			case "warning":
				$output .= "<img src='{$this->view->baseUrl}/themes/gaia/images/warning-icon-small.png' />";
				break;
			case "error":
				$output .= "<img src='{$this->view->baseUrl}/themes/gaia/images/error-icon-small.png' />";
				break;
			case "info":
			default: 
				$output .= "<img src='{$this->view->baseUrl}/themes/gaia/images/info-icon-small.png' />";
				break;
		}
		
		if (!empty($message['title']))
			$output .= "<h3>{$message['title']}</h3>";
		if (!empty($message['description']))
			$output .= $message['description'];
		if (!empty($message['actions'])) {
			$output .= "<ul class='actions'>";
			foreach($message['actions'] as $action) {
				$output .= "<li class='btn with-icon'><a href='{$this->view->url($action['url'])}' class='button {$action['class']}'>{$action['text']}</a></li>";
			}
			$output .= "</ul>";
		}
		$output .= "</div>";
		
		if (!empty($message['autoclose']))
			$output .= "<script type='text/javascript'>
					$(function() {
						setTimeout(function () {
							\$('.flashMessage').eq(0).animate({ 
    						paddingBottom: '0',
    						paddingTop: '0',
    						marginBottom: '0',
    						marginTop: '0',
			  			  opacity: 0,
	  	  				height: '0px',
			  			}, 'normal',function() { $(this).remove(); });
						},{$message['autoclose']});
					});
			</script>";

		return $output;
	}
	
}