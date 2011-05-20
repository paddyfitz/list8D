<?php

class List8D_Form_AddTagUser extends List8D_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

		$this->addElement('hidden', 'tagid', array(
			'required' => true,
			'validators' => array(
                array('validator' => 'digits')
               )
		));

        $this->addElement('select', 'userid', array(
			'label' => 'User:',
			'required' => true,
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