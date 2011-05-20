<?php

class list8D {

	protected $_host;
	protected $_method="GET";
	protected $_port="80";
	protected $_basePath=false;
	protected $_offset=0;
	protected $_limit=0;
	protected $_unique = array(
		'code'=>null,
	);
	protected $_template;
	protected $_list=null;
	
	function __construct($host) {
		$this->_host = $host;
		
	}
	
	public function setHost($host) {
		$this->_host = $host;
	}
	
	public function getHost() {
		return $this->_host;
	}
	
	public function setLimit($limit) {
		$this->_limit = $limit;
	}
	
	public function getLimit() {
		return $this->_limit;
	}
	
	public function setOffset($offset) {
		$this->_offset = $offset;
	}
	
	public function getOffset() {
		return $this->_offset;
	}
	
	public function setPage($page) {
		if ($this->_limit)
			$this->_offset = ($page-1)*$this->_limit;
	}
	
	public function getPage() {
		if ($this->_limit)
			return ceil($this->_offset/$this->_limit)+1;
		else 
			return false;
	}
	
	public function setPort($port) {
		$this->_port = $port;
	}
	
	public function getPort() {
		return $this->_port;
	}
	
	public function setBasePath($path) {
		$this->_basePath = $path;
	}
	
	public function getBasePath() {
		return $this->_basePath;
	}
	
	public function setMethod($method) {
		$this->_method = $method;
	}
	
	public function getMethod() {
		return $this->_method;
	}
		
	public function getUrl() {
		$return = "http://".$this->getHost().":".$this->getPort();
		if($this->getBasePath()) 
			$return .= "/".$this->getBasePath();
		$return .= "/api";
		return $return;
	}
	
	public function setCode($code) {
		$this->_unqiue['code'] = $code;
	}
	
	public function call($_data) {
	
		// convert variables array to string:
    $data = array();    
    foreach($_data as $key => $value) {
    	if (is_array($value)) {
    		foreach($value as $v) {
    			$data[] = $key.'[]='.$v;
    		}
    	} else {
        $data[] = "$key=$value";
      }
    }
    $data = implode("&",$data);    


    $url = parse_url($this->getUrl());
    
		// open a socket connection on port 80
    $fp = fsockopen($url['host'], $url['port']);

    // send the request headers:
    if ($this->getMethod() == "GET") {
    	$data_length = 0;
    	$url['path'] .= "?".$data;  
    } else {
    	$data_length = count($data);
    }

		fputs($fp, "{$this->getMethod()} {$url['path']} HTTP/1.0\r\n");
    fputs($fp, "Host: {$url['host']}\r\n");
    //fputs($fp, "Referer: $referer\r\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
    fputs($fp, "Content-length: ". $data_length ."\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    if ($this->getMethod() == "POST") 
		  fputs($fp, $data);
    
    $result ='';
    while(!feof($fp)) {
    	$result .= fgets($fp,128);
    }
    //echo "<pre>".$result."</pre>";
    fclose($fp);
    
    $result = explode("\r\n\r\n",$result,2);

    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';
		
		$header = explode("\r\n",$header);
		$return=array();
		foreach($header as $head) {
			if(preg_match("/^((HTTP\S*) ((\S*) (.*)))/",$head,$matches)) {
				$return['response'] = $matches[0];
				$return['status_code'] = $matches[3];
				$return['status_code_number'] = $matches[4];
				$return['status_message'] = $matches[5];
			} else if (preg_match("/^([^:]+):(.*)$/",$head,$matches)) {
				$return[$matches[1]] = $matches[2];
			} else {
				$return[] = $head;
			}
		}
		$return['content'] = json_decode($content);
		$return['list8D_status'] = $return['content']->status;
		$return['content'] = $return['content']->response; 
		//var_dump($return['content']);
		if ($return['status_code_number']>299 || $return['list8D_status'] == 'failed') {
			$this->handleError($return);
		} 
    return $return;
 
    
	}
	
	public function handleError($return) {

		if (empty($return['list8D_status'])) {
			throw new Exception("list8D server responded with error: ".$return['status_code']);
		} else {
			throw new Exception("list8D responded with error: ".$return['content']->message);
		}
		
	}
	
	public function sayHello($who,$when) {
		$params = array(
			"method"=>"sayHello",
			"who"=>$who,
			"when"=>$when,
		);
		
		$response = $this->call($params);
		return $response['content']->response;
	}
	
	public function getListByCode($code) {
		$params = array(
			"method"=>"getListByCode",
			"code"=>$code,
		);
		$response = $this->call($params);
		return $response['content']->response;
	}
	public function getListById($id,$select=false,$limit=0,$offset=0) {
		$params = array(
			"method"=>"getListById",
			"listId"=>$id,
			'select'=>$select,
			'offset'=>$offset,
			'limit'=>$limit,
		);
		$response = $this->call($params);
		return $response['content'];
	}
	public function getListByData($key,$value,$select=false) {
		$params = array(
			"method"=>"getListByData",
			"key"=>$key,
			'value'=>$value,
			'select'=>$select,
			'offset'=>$this->getOffset(),
			'limit'=>$this->getLimit(),
		);
		$response = $this->call($params);
		return $response['content'];
	}
	
	public function setModuleCode($code) {
		$this->_unique['code'] = $code;
	}
	
	public function getModuleCode() {
		return $this->_unique['code'];
	}
		
	public function getList($select) {
		if ($this->_list == null) {
			foreach($this->_unique as $unique => $value) {
				if (!empty($value)) 
					$this->_list = $this->getListByData($unique,$value,$select);
			}
		}
		return $this->_list;
	}
	
	public function setTemplate($template) {
		if ($template == 'compressed' || $template=='full')
			$this->_template = $template;	
	}	
	
	public function getTemplate() {
		if (empty($this->_templateObject)) {
			$this->_templateObject = new list8D_template();
		}
		return $this->_templateObject;
	}
	public function getItemsTemplate() {
		if (empty($this->_itemsTemplate)) {
			require_once("templates/reading-list-".strtolower($this->_template).".class.php");
			$templateClass = "readingList".ucwords($this->_template);
			$this->_itemsTemplate = new $templateClass();
		}
		return $this->_itemsTemplate;
	}
	
	public function getTemplateVariables() {
		return $this->getItemsTemplate()->getVariables();
	}
	public function render() {
		$select = $this->getTemplateVariables();

		$this->getTemplate()->list = $this->getList($select);	
		$this->getItemsTemplate()->list = $this->getList($select);	
		if ($this->_limit)
			$this->getTemplate()->pages = ceil(count($this->getTemplate()->list->children)/$this->_limit);
		else
			$this->getTemplate()->pages = 1;
		$this->getTemplate()->currentPage = $this->getPage();
		$this->getTemplate()->listId = $this->getModuleCode();
		$this->getTemplate()->filename = 'reading-list-list.tpl.php';
		$this->getItemsTemplate()->filename = 'reading-list-'.strtolower($this->_template).'.tpl.php';
		$this->getTemplate()->items = $this->getItemsTemplate()->render();
		return $this->getTemplate()->render();
	}	
}