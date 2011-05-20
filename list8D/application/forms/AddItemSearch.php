<?php
// application/forms/Guestbook.php

class List8D_Form_AddItemSearch extends List8D_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        
        
        // Add a list id element
        $this->addElement('hidden', 'id', array('value' => 1));
		
        $this->addElement('select', 'type', array(
			'label' => 'Select Resource Type:',
			'required' => true,
			
			));
			
			$this->addElement('text', 'keyword', array(
			'label' => 'Search Keywords:',
			'required' => true,
			'filters' => array('StringTrim'),
			));
        
/*
        // Add a resource id element
        $this->addElement('text', 'resource_id', array(
            'label'      => 'Resource id:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
        
        // Add a list id element
        $this->addElement('text', 'list_id', array(
            'label'      => 'List id:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
        
        // Add an order element
        $this->addElement('text', 'order', array(
            'label'      => 'Order:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));

        // Add the start date element
        $this->addElement('text', 'start', array(
            'label'      => 'Start date:',
            'required'   => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
        
        // Add the end date element
        $this->addElement('text', 'end', array(
            'label'      => 'End date:',
            'required'   => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
        
        // Add a created element
        $this->addElement('text', 'created', array(
            'label'      => 'Created:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
        
        // Add a updated element
        $this->addElement('text', 'updated', array(
            'label'      => 'Updated:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
        
        // Add a autor element
        $this->addElement('text', 'author', array(
            'label'      => 'Author:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
*/
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Find',
        ));

        // And finally add some CSRF protection
        /*
$this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
*/
        
    }
}