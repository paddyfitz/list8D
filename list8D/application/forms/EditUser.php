<?php

class List8D_Form_EditUser extends List8D_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

		        $this->addElement('select', 'role', array(
			'label' => 'Select Role:',
			'required' => true,
			));
        
		$this->addElement('text', 'login', array(
			'label' => 'Login:',
			'required' => true,
			'filters' => array('StringTrim'),
			'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 128))
                )
			));
		$this->addElement('text', 'displayname', array(
			'label' => 'Display Name',
			'required' => true,
			'filters' => array('StringTrim'),
			'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 255))
                )
			));
		$this->addElement('text', 'email', array(
			'label' => 'Email Address:',
			'required' => true,
			'filters' => array('StringTrim', "StringToLower"),
			'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 255)),
				array('validator' => 'EmailAddress')
                )
			));
		$this->addElement('text', 'instid', array(
			'label' => 'Institution ID:',
			'required' => false,
			'filters' => array('StringTrim'),
			'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 255))
                )
			));


        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Save User',
        ));

        // And finally add some CSRF protection
		$this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
 
    }
}