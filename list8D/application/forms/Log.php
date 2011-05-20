<?php

class List8D_Form_Log extends List8D_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        
        // Add process field
        $this->addElement('text', 'process', array(
            'label'      => 'Action:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));
        
        // Add table field
        $this->addElement('text', 'table', array(
            'label'      => 'Table:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));
        
        // Add row_id field
        $this->addElement('text', 'row_id', array(
            'label'      => 'Row id:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));
        
        // Add column field
        $this->addElement('text', 'column', array(
            'label'      => 'Column:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));
        
        // Add user field
        $this->addElement('text', 'user', array(
            'label'      => 'User id:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));
        
        // Add start field
        $this->addElement('text', 'start', array(
            'label'      => 'Start timestamp (will change later):',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));
        
        // Add end field
        $this->addElement('text', 'end', array(
            'label'      => 'End timestamp (will change later):',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Filter',
        ));

        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }
}