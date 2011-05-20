<?php

class List8D_Theme_Root_Helper_RenderData extends List8D_ViewHelper {
	
    
    public function renderData($list,$options=array()) {
    
			$output = array();

			foreach($list->getData() as $key => $value) {

				if ($list->isList()) {
					$type="list";
				} else {
					$type="item";
				}
				
				if (!empty($value['type'])) {
					switch ($value['type']) {
						case "boolean":
							if (isset($value['value']) && $value['value']) {
								$output[$value['title']] = "Yes"; 
							} else {
								$output[$value['title']] = "No"; 
							}
							$output[$value['title']] .= " ".$this->view->a("toggle ".$value['title'],array('controller'=>'data','action'=>'toggle','id'=>null,$type.'id'=>$list->getId(),'data'=>$key,'destination'=>$this->view->getDestination()));
							$output[$value['title']] .= " ".$this->view->a("edit ".$value['title'],array('controller'=>'data','action'=>'edit','id'=>null,$type.'id'=>$list->getId(),'data'=>$key,'destination'=>$this->view->getDestination()));
							break;
							
					
							
						case "date":
						
							if (isset($options['date format'])) {
	  						$format = $options['date format'];
	  					} else {
	  						$themeSettings = new Zend_Config_Ini(APPLICATION_PATH."/configs/theme.ini");
	  						if ($themeSettings->defaultDateFormat) {
	  							$format = $themeSettings->defaultDateFormat;
	  						} else {
	  							$format = 'l jS \of F Y';
	  						}
	  					}
							
							if (isset($value['value'])) {
								$output[$value['title']] = date($format,strtotime($value['value']));
							} else {
								$output[$value['title']] = "";
							}
							
							$output[$value['title']] .= " ".$this->view->a("change ".$value['title'],array('controller'=>'data','action'=>'edit','id'=>null,$type.'id'=>$list->getId(),'data'=>$key,'destination'=>$this->view->getDestination()));
							
							break;
							
						case "text":
						case "textarea":
						default:
				            if(!empty($value['value']))
    							$output[$value['title']] = $value['value'];
    				        else
    				        	$output[$value['title']] = "";
							$output[$value['title']] .= " ".$this->view->a("edit ".$value['title'],array('controller'=>'data','action'=>'edit','id'=>null,$type.'id'=>$list->getId(),'data'=>$key,'destination'=>$this->view->getDestination()));
							break;
					}
				}
			}
			
			return $this->view->dl($output);
    
    }
    
}