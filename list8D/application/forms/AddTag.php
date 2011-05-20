<?php

class List8D_Form_AddTag extends List8D_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

		// Add namespace field
        $this->addElement('text', 'namespace', array(
            'label'      => 'Tag type:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 24)),
            ),
						'description' => 'Commonly used tags are: module, department, faculty',
        ));
        
        // Add tag field
        $this->addElement('text', 'tag', array(
            'label'      => 'Tag:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 128)),
            ),
        ));

	    // Add parent_id field
        $this->addElement('hidden', 'parent_id', array(
            //'label'      => 'Parent ID:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 4)),
            ),
        ));
        
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Save',
        ));

        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }
}