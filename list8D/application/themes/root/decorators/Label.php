<?php

class List8D_Form_Decorator_Label extends Zend_Form_Decorator_Label {
	
	/**
     * Render a label
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $label     = $this->getLabel();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $id        = $this->getId();
        $class     = $this->getClass();
        $options   = $this->getOptions();


        if (empty($label) && empty($tag)) {
            return $content;
        }


				if ($element->hasErrors()) {
					$class .= ' error';
				}

        if (!empty($label)) {
            $options['class'] = $class;
            $label = $view->formLabel($element->getFullyQualifiedName(), trim($label), $options);
        } else {
            $label = '&nbsp;';
        }

        if (null !== $tag) {
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $decorator = new Zend_Form_Decorator_HtmlTag();
            if (is_array($tag)) {
            	if (!isset($tag['id'])) {
            		$tag['id'] = $this->getElement()->getName() . '-label';
            	}
 	            $decorator->setOptions($tag);
						} else {
						  $decorator->setOptions(array('tag' => $tag,
  	                                       'id'  => $this->getElement()->getName() . '-label'));
						}

            $label = $decorator->render($label);
        }

        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $label;
            case self::PREPEND:
                return $label . $separator . $content;
        }
    }
	
}