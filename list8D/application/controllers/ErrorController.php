<?php

class ErrorController extends List8D_Controller
{
		public function init() {
			parent::init();
		}
		
    public function errorAction()
    {

        $errors = $this->_getParam('error_handler');

	      $this->outputStackTrace($errors->exception);
	      
				$this->view->supportEmailAddress = false;
				$this->view->supportEmailMessage = "";
        global $application;
    		$settings = $application->getOptions();		


    		if (isset($settings['list8d']['supportEmailSubject']))
    			$this->view->supportEmailSubject = $settings['list8d']['supportEmailSubject'];
    		if (isset($settings['list8d']['supportEmailAddress']))
    			$this->view->supportEmailAddress = $settings['list8d']['supportEmailAddress'];
				
				
				
    		$this->view->url = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    		$this->view->url .= $_SERVER['HTTP_HOST'];
    		$this->view->url .= $_SERVER['REQUEST_URI'];
    		
    		if (isset($_SERVER['HTTP_REFERER']))
	    		$this->view->referringUrl = $_SERVER['HTTP_REFERER'];
    		
    		$this->view->userAgent = $_SERVER['HTTP_USER_AGENT'];
    		
    		if (isset($settings['list8d']['supportEmailBody'])) {
    			$this->view->supportEmailBody = $settings['list8d']['supportEmailBody'];
    			$this->view->supportEmailBody = str_replace('{$code}',$this->view->code,$this->view->supportEmailBody);
    			$this->view->supportEmailBody = str_replace('{$message}',$this->view->message,$this->view->supportEmailBody);
    			$this->view->supportEmailBody = str_replace('{$url}',$this->view->url,$this->view->supportEmailBody);
    			$this->view->supportEmailBody = str_replace('{$referringUrl}',$this->view->referringUrl,$this->view->supportEmailBody);
    			$this->view->supportEmailBody = str_replace('{$userAgent}',$this->view->userAgent,$this->view->supportEmailBody);
    			if ($this->view->currentUser->isAllowed('stack-trace','view'))
	    			$this->view->supportEmailBody = str_replace('{$stackTrace}',join("\n\n",self::getTraces($errors->exception, 'text')),$this->view->supportEmailBody);
	    		else
	    			$this->view->supportEmailBody = str_replace('{$stackTrace}',"",$this->view->supportEmailBody);
    		}
    		
        if (($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION || $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER)) {
        
        	// 404 error -- controller or action not found
        	$this->getResponse()->setHttpResponseCode(404);
        	
				} 
								
				
        if (APPLICATION_ENV=='production') {
				  $this->viewRenderer->setViewScriptPathSpec('error.:suffix');

        } else {
        	$this->layoutView->headLink()->setContainer(new Zend_View_Helper_Placeholder_Container());
        	$this->layoutView->headLink()->appendStylesheet('error.css');
       		$this->_helper->layout->setLayout('blank');
				  $this->viewRenderer->setViewScriptPathSpec('error-debug.:suffix');
        }
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
        $this->layoutView->title = "Error: ".$this->view->friendlyText;
        $this->layoutView->displayTitle = false;
  }

	
  
  /**
   * Gets the stack trace for this exception.
   */
  protected function outputStackTrace(Exception $exception)
  {
    $this->view->format = 'html';
   $errors = $this->_getParam('error_handler');
    

    //$response->setStatusCode(500);
    //$response->setContentType('text/html');
    if ($exception->getCode())
			$this->view->code = $exception->getCode();
		//if ($this->view->code>300) {
  	  $this->view->text   = 'Internal server error';
  	  $this->view->class = '500';
  	  $this->view->friendlyText   = 'A system error occurred';
  	  $this->view->description = "Something went wrong when we were trying to build the page. It's more than likely a problem with the code, please feel free to contact support.";
  	//}
		if ($this->view->code==403) {
  	  $this->view->text   = 'Permission denied';
   	  $this->view->class = 'access-denied';
  	  $this->view->friendlyText   = 'Permission denied';
  	  $this->view->description = "You do not have permission to access this URL. If you followed what appeared to be a legitimate link, or think that you should have access to this page please contact support.";
  	}
    //$text = $exception->getMessage();

      
		if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION || $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER) {
      $this->view->code   = '404';
  	  $this->view->class = 'missing-file';
  	  $this->view->text   = 'Page not found';
  	  $this->view->description = "The url your browser is requesting is not valid. If you entered the URL yourself please check it and try again. If you followed a link from outside the reading list system it is most likely the link was wrong or out of date. If you clicked a link from within the reading list system, it means there was a small system error and we're sorry for the inconvenience.";
  	  $this->view->friendlyText   = 'Page not found';
    } 
    
    if ($exception instanceof Zend_Db_Exception) {
  	  $this->view->text   = 'Database error';
    }
    
    if ($exception instanceof Zend_Db_Statement_Exception) {
  	  $this->view->text   = 'Database statement error';
    }
      
    

    /*
// send an error 500 if not in debug mode
    if (!sfConfig::get('sf_debug'))
    {
      if ($template = self::getTemplatePathForError($format, false))
      {
        include $template;
        return;
      }
    }
*/

    $this->view->message = null === $exception->getMessage() ? 'n/a' : $exception->getMessage();
    $this->view->name    = get_class($exception);
    $this->view->traces  = self::getTraces($exception, 'html');

    // dump main objects values
    //$sf_settings = '';
    //$settingsTable = $requestTable = $responseTable = $globalsTable = $userTable = '';
    /*
if (class_exists('sfContext', false) && sfContext::hasInstance())
    {
      $context = sfContext::getInstance();
      $settingsTable = self::formatArrayAsHtml(sfDebug::settingsAsArray());
      $requestTable  = self::formatArrayAsHtml(sfDebug::requestAsArray($context->getRequest()));
      $responseTable = self::formatArrayAsHtml(sfDebug::responseAsArray($context->getResponse()));
      $userTable     = self::formatArrayAsHtml(sfDebug::userAsArray($context->getUser()));
      $globalsTable  = self::formatArrayAsHtml(sfDebug::globalsAsArray());
    }
*/

  }
  
  /**
   * Prints the stack trace for this exception.
   */
  public function printStackTrace()
  {
    if (null === $this->wrappedException)
    {
      $this->setWrappedException($this);
    }

    $exception = $this->wrappedException;

    if (!sfConfig::get('sf_test'))
    {
      // log all exceptions in php log
      error_log($exception->getMessage());

      // clean current output buffer
      while (ob_get_level())
      {
        if (!ob_end_clean())
        {
          break;
        }
      }

      ob_start(sfConfig::get('sf_compressed') ? 'ob_gzhandler' : '');

      header('HTTP/1.0 500 Internal Server Error');
    }

    try
    {
      $this->outputStackTrace($exception);
    }
    catch (Exception $e)
    {
    }

    if (!sfConfig::get('sf_test'))
    {
      exit(1);
    }
  }
  
  static protected function getTraces($exception, $format = 'html')
  {
    $traceData = $exception->getTrace();
    array_unshift($traceData, array(
      'function' => '',
      'file'     => $exception->getFile() != null ? $exception->getFile() : null,
      'line'     => $exception->getLine() != null ? $exception->getLine() : null,
      'args'     => array(),
    ));

    $traces = array();
    if ($format == 'html')
    {
      $lineFormat = 'at <strong>%s%s%s</strong>(%s)<br />in <a href=\'coda://%s#%s\'><em>%s</em> line %s</a> <a href="#" onclick="$(this).siblings(\'ul\').toggle(); return false;">...</a><br /><ul class="code" id="%s" style="display: %s">%s</ul>';
    }
    else
    {
      $lineFormat = 'at %s%s%s(%s) in %s line %s';
    }

    for ($i = 0, $count = count($traceData); $i < $count; $i++)
    {
      $line = isset($traceData[$i]['line']) ? $traceData[$i]['line'] : null;
      $file = isset($traceData[$i]['file']) ? $traceData[$i]['file'] : null;
      $args = isset($traceData[$i]['args']) ? $traceData[$i]['args'] : array();
      $traces[] = sprintf($lineFormat,
      	
        (isset($traceData[$i]['class']) ? $traceData[$i]['class'] : ''),
        (isset($traceData[$i]['type']) ? $traceData[$i]['type'] : ''),
        $traceData[$i]['function'],
        self::formatArgs($args, false, $format),
        self::formatFile($file, $line, $format, null === $file ? '' : $file),
        null === $line ? '' : $line,
        self::formatFile($file, $line, $format, null === $file ? 'n/a' : $file),
        null === $line ? 'n/a' : $line,
        'trace_'.$i,
        $i == 0 ? 'block' : 'none',
        self::fileExcerpt($file, $line)
      );
    }

    return $traces;
  }
  
  /**
   * Formats an array as a string.
   *
   * @param array   $args     The argument array
   * @param boolean $single
   * @param string  $format   The format string (html or txt)
   *
   * @return string
   */
  static protected function formatArgs($args, $single = false, $format = 'html')
  {
    $result = array();

    $single and $args = array($args);

    foreach ($args as $key => $value)
    {
      if (is_object($value))
      {
        $formattedValue = ($format == 'html' ? '<em>object</em>' : 'object').sprintf("('%s')", get_class($value));
      }
      else if (is_array($value))
      {
        $formattedValue = ($format == 'html' ? '<em>array</em>' : 'array').sprintf("(%s)", self::formatArgs($value));
      }
      else if (is_string($value))
      {
        $formattedValue = ($format == 'html' ? sprintf("'%s'", self::escape($value)) : "'$value'");
      }
      else if (null === $value)
      {
        $formattedValue = ($format == 'html' ? '<em>null</em>' : 'null');
      }
      else
      {
        $formattedValue = $value;
      }
      
      $result[] = is_int($key) ? $formattedValue : sprintf("'%s' => %s", self::escape($key), $formattedValue);
    }

    return implode(', ', $result);
  }

  /**
   * Formats a file path.
   * 
   * @param  string  $file   An absolute file path
   * @param  integer $line   The line number
   * @param  string  $format The output format (txt or html)
   * @param  string  $text   Use this text for the link rather than the file path
   * 
   * @return string
   */
  static protected function formatFile($file, $line, $format = 'html', $text = null)
  {
    if (null === $text)
    {
      $text = $file;
    }

    if ('html' == $format && $file && $line && false /*needs file link formater*/)
    {
      $link = strtr($linkFormat, array('%f' => $file, '%l' => $line));
      $text = sprintf('<a href="%s" title="Click to open this file" class="file_link">%s</a>', $link, $text);
    }

    return $text;
  }
  
  /**
   * Returns an excerpt of a code file around the given line number.
   *
   * @param string $file  A file path
   * @param int    $line  The selected line number
   *
   * @return string An HTML string
   */
  static protected function fileExcerpt($file, $line)
  {
    if (is_readable($file))
    {
      $content = preg_split('#<br />#', highlight_file($file, true));

      $lines = array();
      for ($i = max($line - 14, 1), $max = min($line + 5, count($content)); $i <= $max; $i++)
      {
        $lines[] = '<li'.($i == $line ? ' class="selected"' : '').'>'.$content[$i - 1].'</li>';
      }

      return '<ol start="'.max($line - 14, 1).'">'.implode("\n", $lines).'</ol>';
    }
  }
  
  /**
   * Escapes a string value with html entities
   *
   * @param  string  $value
   *
   * @return string
   */
  static protected function escape($value)
  {
    if (!is_string($value))
    {
      return $value;
    }
    
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }
  
}

