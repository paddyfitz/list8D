<?php


class AmazonController extends List8D_Controller
{

    public function init()
    {
			parent::init();
    }

    public function indexAction()
    {
        // action body
		$request = $this->getRequest();
        $form    = new List8D_Form_Amazon();
		
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $conf = $this->getInvokeArg('bootstrap')->getApplication()->getOptions();
                $ns = List8D_Model_Metatron_Amazon::getNamespace();
                $model = new List8D_Model_Metatron_Amazon($conf['list8d'][$ns]);
				// pre_dump($model->getMetadata($form->getValue("isbn")));
				$this->view->amazon = $model->findResources($form->getValue("keywords"));

			}
        }
        
        $this->view->form = $form;
    }


}

