<?php
// Generic confirm delete form.
//
// Usage:
//			$form = new List8D_Form_ConfirmDelete();
//			$form->setAction($this->view->url());
//			$hidden = $form->getElement("id");
//			$hidden->setValue($id);

class List8D_Form_ConfirmDelete extends List8D_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        
		$this->addElement('hidden', 'id', array(
			'required' => true,
			'validators' => array(
                array('validator' => 'digits')
               )
		));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Confirm',
        ));

        // And finally add some CSRF protection
		$this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
 
    }
}