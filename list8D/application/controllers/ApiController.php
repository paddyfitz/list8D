<?php
	class ApiController extends List8D_Controller
	{
		
		
		
		public function init() {
			// $this->_helper->viewRenderer->setNoRender();
		}
		
		
		// comes from a comment in the php manuel for the json_decode function
		public static function json_indent($json) {
			
			$indentedJson = '';
			$identPos = 0;
			$jsonLength = strlen($json);
			
			for($i = 0; $i <= $jsonLength; $i++) {
				
				$_char = substr($json, $i, 1);
				
				if($_char == '}' || $_char == ']') {
					$indentedJson .= chr(13);
					$identPos --;
					for($ident = 0;$ident < $identPos;$ident++) {
						$indentedJson .= chr(9);
					}
				}
				
				$indentedJson .= $_char;
				
				if($_char == ',' || $_char == '{' || $_char == '[') {
					
					$indentedJson .= chr(13);
					if($_char == '{' || $_char == '[') {
						$identPos ++;
					}
					for($ident = 0;$ident < $identPos;$ident++) {
						$indentedJson .= chr(9);
					}
				}
			}
			
			return $indentedJson;
			
		}
		
		public function sayhelloAction() {
			echo 'Hello world!';
		}
		
		public function jsonAction() {
			$server = new Zend_Json_Server();
			$server->setClass('List8D_API');
			if ('GET' == $_SERVER['REQUEST_METHOD']) {
				// Indicate the URL endpoint, and the JSON-RPC version used:
				$server->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
				
				// Grab the SMD
				$smd = $server->getServiceMap();
				
				// Return the SMD to the client
				header('Content-Type: application/json');
				echo $smd;
				return;
			}
			$server->handle();
		}
		
		public function indexAction() {
			/*
$server = new Zend_Rest_Server();
			$server->setClass('List8D_API');
			$server->returnResponse(true);
			$resp = $server->handle();
			
			// For AJAX requests ??
            if($this->getRequest()->isXmlHttpRequest()) {
                $contentType = 'application/json';
            } else {
                $contentType = 'text/plain';
            }
			$params = $this->getRequest()->getParams();
			if (isset($params['json'])) {
				$this->getResponse()
			     ->setHeader('Content-type', $contentType)
		  		 ->setBody(Zend_Json::fromXml($resp));
			}
			else {
				$this->getResponse()
			     ->setHeader('Content-type', $contentType)
		  		 ->setBody($resp);
			}
*/
			ini_set('display_errors', 0);
			//! TODO Catch errors add them as json to the response
			$server = new List8D_Json_Rest_Server();
			$server->setClass('List8D_API');
			$server->handle();
			
		}
		
		
		public function preDispatch()
		{
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
		}
		
	}