<?php

	class List8D_Form_Decorator_ViewHelper extends Zend_Form_Decorator_ViewHelper {
    
    /**
     * Retrieve element attributes
     *
     * Set id to element name and/or array item.
     * 
     * @return array
     */
    public function getElementAttribs()
    {
        if (null === ($element = $this->getElement())) {
            return null;
        }
				
				$attribs = $element->getAttribs();
        if (isset($attribs['helper'])) {
            unset($attribs['helper']);
        }

        if (method_exists($element, 'getSeparator')) {
            if (null !== ($listsep = $element->getSeparator())) {
                $attribs['listsep'] = $listsep;
            }
        }

        if (isset($attribs['id'])) {
            return $attribs;
        }

        $id = $element->getName();

        if ($element instanceof Zend_Form_Element) {
            if (null !== ($belongsTo = $element->getBelongsTo())) {
                $belongsTo = preg_replace('/\[([^\]]+)\]/', '-$1', $belongsTo);
                $id = $belongsTo . '-' . $id;
            }
        }

        $element->setAttrib('id', $id);
        $attribs['id'] = $id;

				
				if ($element->hasErrors()) {
					if (isset($attribs['class']))
						$attribs['class'] .= "error";
					else
						$attribs['class'] = 'error';
				}

        return $attribs;
        
    }
    
     
    
  }