<?php

class KentvoyagerController extends List8D_Controller
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        // action body
				$request = $this->getRequest();
        $form    = new List8D_Form_KentVoyager();
		
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $model = new List8D_Model_Metatron_KentVoyager();
				$this->view->kv = $model->findResources($form->getValue("rcn"));
			}

        }
        $this->view->form = $form;
    }


}

