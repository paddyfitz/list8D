<?php
// application/forms/Guestbook.php

class List8D_Form_FindUser extends List8D_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('get');
        
        // Add a title search field
        $this->addElement('text', 'search_user_by_username_and_display_name', array(
            'label'      => 'Find user by login or display name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));
        
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Search',
        ));
        
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl()."/user");
        
    }
}