<?php

class List8D_Form_AddTagList extends List8D_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

		$this->addElement('hidden', 'listid', array(
			'required' => true,
			'validators' => array(
                array('validator' => 'digits')
               )
		));

        $this->addElement('text', 'tag', array(
			'label' => 'tag:',
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