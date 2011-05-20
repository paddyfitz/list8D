<?php
	class List8D_Form_GoogleBooks extends List8D_Form {

		public function init()
		{
			// Set the method for the display form to POST
			$this->setMethod('post');
			
			// Add an email element
			$this->addElement('text', 'q', array(
													 'label'      => 'RCN:',
													 'required'   => true,
													 'filters'    => array('StringTrim'),
													 ));
			// Add the submit button
			$this->addElement('submit', 'submit', array(
														'ignore'   => true,
														'label'    => 'Query',
														));
			// And finally add some CSRF protection
			// $this->addElement('hash', 'csrf', array(
			//										'ignore' => true,
			//										));
		}
		
	}
