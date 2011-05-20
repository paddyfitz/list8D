<?php

class URLController extends List8D_Controller
{

    public function init()
    {
			parent::init();
    }

    public function indexAction()
    {
        // action body
		$request = $this->getRequest();
        $form    = new List8D_Form_URL();
		
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $model = new List8D_Model_Metatron_URL();
				$this->view->url = $model->getMetadata($form->getValue("url"));
			}
        }
        
        $this->view->form = $form;
    }


}

