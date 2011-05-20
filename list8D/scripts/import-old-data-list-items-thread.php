<?php
	
  require_once("import-old-data-setup.php");
  
  
	$args = getopt('hdl:ct:');
	// Debug -d
	if(isset($args['d'])) {
		$save = false;
		$d=true;
	} else {
		$save = true;
		$d=false;
	}
  
	$lists = unserialize(fgets(STDIN));
	
	//echo "\n\n\n\n\n";
  $i=1;
  
	//	$progress = new List8D_CliProgress(count($imported_lists));
	$imported_items = 0;
	$legacy_items = 0;
  // Foreach item 
  foreach($lists as $listId) {
  	//echo "\033[4F";
		//echo "\033[0J";
  	
		//echo "Total progress:\n";	
		//$progress->step = $i;
		//$progress->draw();
		//echo "\n";
		
		// Fetch the list items
		$items = $db->fetchAll("SELECT `material`.*, `sequence`.*, `annotation`.* FROM `material` INNER JOIN `sequence` ON sequence.material_id = material.material_id INNER JOIN `annotation` ON sequence.note_id = annotation.note_id WHERE list_id = '$listId'");
		// Fetch the list notes and merge them with items
		$items = array_merge($items,$db->fetchAll("SELECT `sequence`.*, `annotation`.* FROM `sequence` LEFT JOIN `annotation` ON sequence.note_id = annotation.note_id WHERE material_id IS NULL AND list_id='$listId'"));
	
	  $list = new List8D_Model_List();
	  
	  $list = $list->getById($listId);

	  $j=1;
	  
	  if (count($items)) {
		  $listProgress = new List8D_CliProgress(count($items)); 
		}
	  //echo "\n";
	  //echo "\n";
	  

		
	  foreach ($items as $old) {
	  	
	  	//echo "\033[2F";
			//echo "\033[0J";
				  	//echo "List progress (".count($items)." items):\n";	
  		//$listProgress->step = $j;
  		//$listProgress->draw();	
			//echo "\nUsing ".round((memory_get_usage()/1024)/1024,3)." MB";
			
	  	$imported_items++;
	  	
	  	$item = new List8D_Model_Item();
    	
    	// If there is no material_id ie its a note create sub list
    	if (empty($old['material_id']) && !empty($old['content'])) {
    	
    		$item = new List8D_Model_list();
    		$item->setData('title',strip_tags($old['content']));
    		$item->setPosition($old['rank']);

    		$item->setListId($listId);
	
    	} else {
    	  //! TODO needs to be at bottom of else incase book have url
    		// If the material has url create a url resource
    		
				if (!empty($old['url'])) {
				
    		    $uri = new List8D_Model_Resource_Uri();
    		    
    		    if($uri = $uri->findByData('url',$old['url'])) {
    		    	
				
    		    } else {
							$uri = new List8D_Model_Resource_Uri();
	    		    $uri->setData('url',$old['url']);
							$uri->setData('title',$old['title']);
							if($save) {
								$uri->save();      	    	
							}
							
    		    }   	    
    		    
    		    $position = $old['rank'];
    		    
    		    if ($save) {
  	  		    $item = $list->addItem($uri,$position);
    		    }
    		    
    		}

    		// if the material has an isbn or rcn create a book resource 
    		
				else if (!empty($old['control_no']) || !empty($old['isbn'])) {
    			
    			$book = new List8D_Model_Resource_Book();
    			
					// determine isbn type in old data
    			if (!empty($old['control_no'])) {
    				$key = 'Voyager_rcn';
    				$value = $old['control_no'];
    			}
    			
    			if (!empty($old['isbn'])) {
    				if (count($old['isbn'])==10) {
    					$key = 'isbn10';
    				} else if (count($old['isbn'])==13) {
    					$key = 'isbn13';
    				} else if (isset($old['isbn'])) {
    					$key = 'isbn';
    				}
    				$value = $old['isbn'];
    			} 
    			    		
    			if ($book = $book->findByData($key,$value)) {
    			
    			} else {
    				$book = new List8D_Model_Resource_Book();
    				$book->setData('Voyager_rcn',$old['control_no']);
    				try {
    					$book->loadMetadata(true);	
    				} catch (Exception $e) {
    					$failedItemImports[] = $old['material_id'];
    					continue;
    				}
    				if ($save) {
    					$book->save();
    				}
    			}
    			
    		  $position = $old['rank'];
    		  
    		  if ($save) {
  	  		  $item = $list->addItem($book,$position);
    		  }
    		  
    		}

    		// otherwise create a legacy item
    		else {
    			$legacy_items++;
    			$legacy = new List8D_Model_Resource_Legacy();
    			$legacy->setData('title',$old['title']);
    			$legacy->setData('authors',$old['authors']);
    			$legacy->setData('edition',$old['edition']);
    			$legacy->setData('publisher',$old['publisher']);
    			$legacy->setData('published',$old['pubdate']);
    			
    		  $position = $old['rank'];
    		  
    			if ($save) {
    				$legacy->save();
  	  		  $item = $list->addItem($legacy,$position);
    		  }
    			
    		}
    		
    		// set other item data values
    		if (!empty($old['pages']))
	    		$item->setData('part',"pages ".$old['pages']);
    		if (!empty($old['key_text']))
	    		$item->setData('core_text',$old['key_text']);
    		$item->setData('recommended_for_purchase',$old['purchase']);
    		if (!empty($old['private_note']))
	    		$item->setData('private_note',$old['private_note']);
	    	if (!empty($old['content']))	
	    		$item->setData('public_note',$old['content']);
	    		
    	}
    	
    	if ($save) {
    		$item->save();
    	}
    	
    	echo "^$j/".count($items)." items on $i/".count($lists)." lists";
    	
    	$j++;
    	
	  }     
    
    $i++;
    
  }
  
  //echo "\033[4F";
	//echo "\033[0J";  
	
	//$progress->draw();
