<?php

class List8D_Form_List extends List8D_Form
{
    public function init()
    {

				//calculate the default date values.
				$year = date("Y");
				if(date("m") > 8){
					$year++;
				}

				$start_value = "{$year}-09-01 00:00:00";
				$year++;
				$end_value = "{$year}-07-01 00:00:00";
				
        // Set the method for the display form to POST
        $this->setMethod('post');
        
        // Add title field
        $this->addElement('text', 'title', array(
            'label'      => 'Title:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));
        
        // Add code field
        $this->addElement('text', 'code', array(
            'label'      => 'Module code:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));

        // Add year field
        $this->addElement('text', 'year', array(
            'label'      => 'Year:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));

        // Add start field
        $this->addElement('text', 'start', array(
            'label'      => 'Start date:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
						'value'      => $start_value,
						'description' => 'Please use the format YYYY-MM-DD HH:MM:SS',
        ));

        // Add end field
        $this->addElement('text', 'end', array(
            'label'      => 'End date:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
						'value'      => $end_value,
						'description' => 'Please use the format YYYY-MM-DD HH:MM:SS',
        ));
        
        // Add is_published field
        $this->addElement('checkbox', 'is_published', array(
            'label'      => 'Published:',
            'required'   => false,
        ));

        // Add private notes field
        $this->addElement('textarea', 'private_notes', array(
            'label'      => 'Instructions for librarians:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));

        // Add public notes field
        $this->addElement('textarea', 'public_notes', array(
            'label'      => 'Notes for students:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
            ),
        ));

        // Add department field
					$this->addElement('text', 'department', array(
            'label'      => 'Department:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	array('validator' => 'StringLength', 'options' => array(0, 200)),
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