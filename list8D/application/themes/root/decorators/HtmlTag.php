<?php 

class List8D_Form_Decorator_HtmlTag extends Zend_Form_Decorator_HtmlTag
{
	
	public function render($content) {

		$content = $this->getOption("prepend") . $content .$this->getOption("append");
		$this->removeOption("prepend");
		$this->removeOption("append");
		
		return parent::render($content);

  }

}