<?php

class GoogleBooksController extends List8D_Controller
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        // action body
	$request = $this->getRequest();
        $form    = new List8D_Form_GoogleBooks();
		
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $model = new List8D_Model_Metatron_GoogleBooks();
				$this->view->itemdata = $model->findResources($form->getValue("q"));
			}

        }
        $this->view->form = $form;
    }


}

