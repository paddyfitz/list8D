<?php 

class List8D_Reference_Dev extends List8D_Reference {
	
	public function renderArticle($item,$resource) {
		$outputed = false;
		$published = false;
		$output = "";
		
		if ($resource->getDataValue("authors")) {
			$output .= $resource->getDataValue("authors");
			$outputed = true;
		}
		if ($resource->getDataValue("publication_date")) {
			$output .= " (".$resource->getDataValue("publication_date").") ";
			$published = true;
		}
		if (!$published && $outputed) {
			$output .= ", ";
		}
		$output .= "'".$resource->getDataValue("title")."'";
		$output .= " in ";
		$outputed = true;
		
		$outputed = false;
		$published = false;
		
		
		if ($resource->getDataValue("journal")) {
			$output .= "<em>";
			$output .= $resource->getDataValue("journal");
			$outputed = true;
			$output .= "</em>";
		}
		
		if ($resource->getDataValue('volume')) {
			$output .= " ".$resource->getDataValue('volume');
			$outputed = true;					  						
		}
		
		if($outputed) $output .= ", ";
		
		if ($resource->getDataValue('issue')) {
			$output .= $resource->getDataValue('issue').":";
		}
		
		if ($item->getDataValue('part')) 
			$output .= $item->getDataValue('part');
		
		$output = trim($output," ,:");
		return $output;
	
	}

	public function renderDefault($item,$resource) {
	
		$outputed = false;
		$published = false;
		$output = "";
		// part_author date part_title!
		if ($item->getDataValue("part_title")) {
			if ($item->getDataValue("part_author")) {
				$output .= $item->getDataValue("part_author");
				$outputed = true;
			}
			if ($resource->getDataValue("publication_date")) {
				$output .= " (".$resource->getDataValue("publication_date").") ";
				$published = true;
			}
			if (!$published && $outputed) {
				$output .= ", ";
			}
			$output .= "'".$item->getDataValue("part_title")."'";
			$output .= " in ";
			$outputed = true;
		}
		$outputed = false;
		$published = false;
		
		
		// authors [date] title
		if ($resource->getDataValue("authors")) {
			$output .= $resource->getDataValue("authors");
			$outputed = true;
		} 
		if (!$item->getDataValue("part_title")) {
			if ($outputed) $output .= ", ";
			if ($resource->getDataValue("publication_date")) {
				$output .= " (".$resource->getDataValue("publication_date").") ";
				$published = true;
			}
		}
		if (!$published && $outputed) {
			$output .= ", ";
		}
		if ($resource->getDataValue("title")) {
			if ($item->getDataValue("part_title")) $output .= "<em>";
			$output .= $resource->getDataValue("title");
			$outputed = true;
			if ($resource->getDataValue("edition")) {	
				if ($outputed) $output .= ", ";
				$output .= " ".$resource->getDataValue("edition");
			}
			if ($item->getDataValue("part_title")) $output .= "</em>";
			$outputed = true;
		}
		if ($outputed) $output .= ", ";
		
		// publisher
		if ($resource->getDataValue("publisher")) {
			//$output .= $resource->getDataValue("publisher", false);
                        $publisher =  $resource->getDataValue("publisher", false);
                        if(is_array($publisher)) {
                            $output .= implode(", ", $publisher);
                        } else {
                            $output .= $publisher;
                        }
                        //var_dump($resource->getData());
		}
								  					
		if ($item->getDataValue('part')) {
			$part = $item->getDataValue('part');
                        if(preg_match("/^[0-9-\s]+$/", $part)) { //it is allowed whitespace digits and numbers
                            $output .= " pp ".$part;
                        } else {
                            $output .= " ".$part;
                        }
                       
                }


		$output = trim($output," ,");
		return $output;
	
	}
	
	public function renderJournal ($item,$resource) {
		
		$outputed = false;
		$published = false;
		$output = "";
		
		// part_author date part_title
		if ($item->getDataValue("part_author")) {
			$output .= $item->getDataValue("part_author");
			$outputed = true;
		} else if ($resource->getDataValue("authors")) {
			$output .= $resource->getDataValue("authors");
			$outputed = true;
		}
		
		
		if ($item->getDataValue("publication_date")) {
			$output .= " (".$item->getDataValue("publication_date").") ";
			$published = true;
			$outputed = true;
		} else if ($resource->getDataValue("publication_date")) {
			$output .= " (".$resource->getDataValue("publication_date").") ";
			$published = true;
			$outputed = true;
		}
		
		if (!$published && $outputed) {
			$output .= ", ";
		}
		
		
		if ($item->getDataValue("part_title")) {
			$output .= "'".$item->getDataValue("part_title")."' ";
		} elseif ($item->getDataValue("journal")) {
			$output .= "'".$item->getDataValue("title")."' ";
		} else {
			$output .= $resource->getDataValue("title")." ";
		}
		
		if (($resource->getDataValue("title") && $item->getDataValue("part_title")) || $resource->getDataValue("journal")) {
			$output .= " in ";
			$outputed = true;
		}
		
		$outputed = false;
		$published = false;
		
		// authors [date] title
		/*
if	 ($resource->getDataValue("authors") && $item->getDataValue("part_author")) {
			$output .= $resource->getDataValue("authors");
			$outputed = true;
		} 
*/	
		if (($resource->getDataValue("title") && $item->getDataValue("part_title")) || $resource->getDataValue("journal")) {
			if ($item->getDataValue("part_title")) $output .= "<em>";
			if ($resource->getDataValue("journal"))
				$output .= $resource->getDataValue("journal")." ";
			elseif ($resource->getDataValue("title") && $item->getDataValue("part_title"))
				$output .= $resource->getDataValue("title")." ";
			
			if ($item->getDataValue("part_title")) $output .= "</em>";
			$outputed = true;
		}
									  					
		
		if ($item->getDataValue('volume')) {
			$output .= $item->getDataValue('volume');	
			$outputed = true;					  						
		} else if ($resource->getDataValue('volume')) {
			$output .= $resource->getDataValue('volume');	
			$outputed = true;					  						
		} 
		
		if ($item->getDataValue('issue')) {
			if($outputed) $output .= ", ";
			$output .= $item->getDataValue('issue');		
		} else if ($resource->getDataValue('issue')) {
			if($outputed) $output .= ", ";
			$output .= $resource->getDataValue('issue');	
		} 
		
		if ($item->getDataValue('part')) {
			if($outputed) $output .= ", ";
			$output .= $item->getDataValue('part');
		}
		
		$output = trim($output," ,");
		return $output;
	}
	
	public function renderUri() {
		$outputed = false;
		$output = "";
		
		// part_author date part_title
		if ($this->item->getDataValue("part_title")) {
			if ($this->item->getDataValue("part_author")) {
				$output .= $this->item->getDataValue("part_author");
				$outputed = true;
			}
			
			if ($outputed) {
				$output .= ", ";
			}
			$output .= "'".$this->item->getDataValue("part_title")."'";
			$output .= " in ";
			$outputed = true;
		}
		$outputed = false;
		$published = false;
		
		// title
		if ($this->resource->getDataValue('title')) {
			$output .= $this->resource->getDataValue('title');
			$outputed = true;
		}
		// url
		if ($outputed) $output .= ", ";
		$output .= $this->resource->getDataValue('url');
		
		$output = trim($output," ,");
		return $output;
	}
}