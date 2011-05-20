<?php

class List8D_Theme_Root_Helper_Pager extends List8D_ViewHelper  {
	
	public function pager($page=null,$pages=null,$pageParam="page",$limit=10) {

		if ($page === null && isset($this->view->page)) {
			$page = $this->view->page;
		} else if ($page === null) {
			$page = 1;
		}
		if ($pages === null && isset($this->view->pages)) {
			$pages = $this->view->pages;
		} else if ($pages === null) {
			$pages = 1;
		}

		$output = "";
		if ($pages > 1) {
			$output .= "<ul class='pager'>";
			
			if ($page!=1) {
				$output .= "<li class='first-page'><a href='{$this->view->url(array('controller'=>$this->view->params['controller'],'action'=>$this->view->params['action'],$pageParam=>1))}' class='icon icon-first'>first</a></li>";
				$output .= "<li class='previous-page'><a href='{$this->view->url(array('controller'=>$this->view->params['controller'],'action'=>$this->view->params['action'],$pageParam=>$page-1))}' class='icon icon-prev'>previous</a></li>";
			}
			
			$upperJump = $page+$limit;
			if ($upperJump>$pages) 
			  $upperJump = $pages;
			
			$lowerJump = $page-$limit;
			if ($lowerJump<1) 
			  $lowerJump = 1;
			
			$unusedBefore = $limit/2+1-$page;
			if ($unusedBefore<0)
			  $unusedBefore = 0;
			
			$unusedAfter = $limit/2-($pages-$page);
			if ($unusedAfter<0)
				$unusedAfter = 0;			
				
			for($i=0;$i<$pages;$i++) {
				
				if ($i+1 == $page) 
					$selected = ' class="selected"';
				else 
					$selected = "";
								
				if (!$limit || ($i<$page+1+(ceil($limit/2)+$unusedBefore) && $i>$page-3-(ceil($limit/2)+$unusedAfter)) || $i+1==$pages) {
					
					if ($limit && $i==$page+(ceil($limit/2)+$unusedBefore) && $i+1!=$pages)
						$output .= "<li><a href='{$this->view->url(array('controller'=>$this->view->params['controller'],'action'=>$this->view->params['action'],$pageParam=>$upperJump))}'>...</a></li>";
					else if ($limit && $i==$page-2-(ceil($limit/2)+$unusedAfter) && $i+1!=$pages)
						$output .= "<li><a href='{$this->view->url(array('controller'=>$this->view->params['controller'],'action'=>$this->view->params['action'],$pageParam=>$lowerJump))}'>...</a></li>";
					else
						$output .= "<li$selected><a href='{$this->view->url(array('controller'=>$this->view->params['controller'],'action'=>$this->view->params['action'],$pageParam=>$i+1))}'$selected>".($i+1)."</a></li>";
				}
				
			}
			if ($page!=$pages) {
				$output .= "<li class='next-page'><a href='{$this->view->url(array('controller'=>$this->view->params['controller'],'action'=>$this->view->params['action'],$pageParam=>$page+1))}' class='icon icon-next'>next</a></li>";
				$output .= "<li class='last-page'><a href='{$this->view->url(array('controller'=>$this->view->params['controller'],'action'=>$this->view->params['action'],$pageParam=>$pages))}' class='icon icon-last'>last</a></li>";
			}
			$output .= "</ul'>";
		}
		return $output;	
	}
}