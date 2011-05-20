<?php 

class List8D_Theme_Root_Helper_RenderTagChildren extends List8D_ViewHelper
{
	public function renderTagChildren(List8D_Model_Tag $ctag, $o, $me=null) {
		$content = "<li>";
		if ($me == $ctag->getId())
			$content .= "<strong>";
		$content .= $o->a($ctag->getNamespace() . ':' . $ctag->getTagName(),
			array("controller"=>"tag","action"=>"view", 'id'=>$ctag->getId()));
		if ($me == $ctag->getId())
			$content .= "</strong>";
		$content .= "</li>";

		$check = 0;
		$ret = "";
		foreach((array) $ctag->getChildren() as $child) {
			$ret .= $this->renderTagChildren($child, $o, $me);
			$check = 1;
		}

		if ($check == 1) {
			$content .= "<ul>";
			$content .= $ret;
			$content .= "</ul>";
		}

		return $content;

	}
  
}