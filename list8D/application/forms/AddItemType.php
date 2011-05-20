<?php
// application/forms/Guestbook.php

class List8D_Form_AddItemType extends List8D_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        
        
        
        // Add a list id element
        //$this->addElement('hidden', 'id', array('value' => 1));
		/*
		$this->addElement('select', 'type', array(
			'label' => 'Select Resource Type:',
			'required' => true,
			'attribs' => $resourceTypes,
			));
        */
        
        // Add the submit button
        /*$this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Add Item',
        ));*/

        // And finally add some CSRF protection
        /*
$this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
*/
        
    }
}