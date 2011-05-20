<?php

class SupportController extends List8D_Controller {

	public function init() {
		parent::init();
	}
    
  public function errorreportAction() {
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		$this->viewRenderer->setViewScriptPathSpec('submit-error-report.:suffix');
    $this->layoutView->title = "Submit error report";
    
    $form = new List8D_Form;
    $form->setMethod('post');
    
    if (isset($this->params['username']))
    	$value = $this->params['username'];
    else
    	$value = $currentUser->getLogin(); 	
    $username = $form->addElement('text','username',array('label' => 'Login','value'=>$value,'description'=>'The login of the user this error relates to, in most cases this will be your username.'));
    
    if (isset($this->params['message']))
    	$value = $this->params['description'];
    else 
    	$value = "";
    $username = $form->addElement('textarea','description',array('label' => 'Description','value'=>$value,'description'=>'Please provide as much information about the problem as possible, please remember that we do not have the context in which the error occurred which is often key. The more information about exactly what you were doing and what browser or platform you were using the easier it will be to track down the problem and the quicker we can fix it. Useful information includes: step by step guide to what you did, browser vendor and version, urls you were at, error code and stack trace information (found in the \'more technical information\'). '));
    
    $this->view->form = $form;
  }
    
}