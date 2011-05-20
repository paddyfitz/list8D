<?php 

class List8D_Form_Element_Array extends Zend_Form_Element {

	public function init() {
		$this->addPrefixPath('List8D_Form_Decorator', APPLICATION_PATH . "/../library/List8D/Form/Decorator", 'decorator')->addDecorator("Array");
	}
	
    /**
     * Render form element
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }

        $content = '';
		$content = '<dt id="convener-label"><label for="convener" class="optional">Conveners:</label></dt>';
		
		$values = (array)$this->getValue();
		
		//pre_dump($this->getFilters());
		//exit;
		
		//pre_dump($values);
	/*	foreach ($values as $key => $value){
			$wibble = (array)$value;
			pre_dump($wibble);
			//echo "Key: ".$key." Value: ".$value;
		}
	*/	
		//exit;
		$last_key = 0;
		foreach($values as $key => $value){
			$content .= '<dd id="convener-element['.$key.']"><input type="text" name="convener['.$key.']" id="convener['.$key.']" value="'.$value.'" /></dd>';
			$last_key = $key;
		}
		$last_key++;
		$content .= '<dd id="convener-element['.$last_key.']"><input type="text" name="convener['.$last_key.']" id="convener['.$last_key.']" value="" /></dd>';
		
        /*
		foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }
		*/
        return $content;
    }

}