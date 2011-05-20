<?php 

class List8D_Form_Decorator_Array extends Zend_Form_Decorator_Abstract {

	function render ($content) {
		
		$element = $this->getElement();
		
		/*
		if(!$element instanceof Zend_Form_Element_Multi){
			$content = "Not instance of";
			return $content;
		}
		
		if(null === ($view = $element->getView())){
			$content = "null";
			return $content;
		}
		*/
		
		$translator = $element->getTranslator();
		
		$html = '';
		$html = '<dt id="convenor-label"><label for="convenors" class="optional">Convenors:</label></dt>';
		$values = (array)$element->getValue();
		//pre_dump($element->getName());
		//pre_dump($values);
		//exit;
		

		$baseName = $element->getName();
		foreach($values as $key => $value){
			$html .= "Key: ".$key." Value: ".$value;
		}
		
		return $html.$content;
		
		//return "helloa".$content;
	}

}