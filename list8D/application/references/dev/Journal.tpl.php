<?php 
	$outputed = false;
	$published = false;
	$output = "";
	
	// part_author date part_title
	if ($this->item->getDataValue("part_author")) {
		$output .= $this->item->getDataValue("part_author");
		$outputed = true;
	} else if ($this->resource->getDataValue("authors")) {
		$output .= $this->resource->getDataValue("authors");
		$outputed = true;
	}
	
	
	if ($this->item->getDataValue("publication_date")) {
		$output .= " (".$this->item->getDataValue("publication_date").") ";
		$published = true;
		$outputed = true;
	} else if ($this->resource->getDataValue("publication_date")) {
		$output .= " (".$this->resource->getDataValue("publication_date").") ";
		$published = true;
		$outputed = true;
	}
	
	if (!$published && $outputed) {
		$output .= ", ";
	}
	
	
	if ($this->item->getDataValue("part_title")) {
		$output .= "'".$this->item->getDataValue("part_title")."' ";
	} elseif ($this->item->getDataValue("journal")) {
		$output .= "'".$this->item->getDataValue("title")."' ";
	} else {
		$output .= $this->resource->getDataValue("title")." ";
	}
	
	if (($this->resource->getDataValue("title") && $this->item->getDataValue("part_title")) || $this->resource->getDataValue("journal")) {
		$output .= " in ";
		$outputed = true;
	}
	
	$outputed = false;
	$published = false;
	
	// authors [date] title
	/*
if ($this->resource->getDataValue("authors") && $this->item->getDataValue("part_author")) {
		$output .= $this->resource->getDataValue("authors");
		$outputed = true;
	} 
*/
	if (($this->resource->getDataValue("title") && $this->item->getDataValue("part_title")) || $this->resource->getDataValue("journal")) {
		if ($this->item->getDataValue("part_title")) $output .= "<em>";
		if ($this->resource->getDataValue("journal"))
			$output .= $this->resource->getDataValue("journal")." ";
		elseif ($this->resource->getDataValue("title") && $this->item->getDataValue("part_title"))
			$output .= $this->resource->getDataValue("title")." ";
		
		if ($this->item->getDataValue("part_title")) $output .= "</em>";
		$outputed = true;
	}
								  					
	
	if ($this->item->getDataValue('volume')) {
		$output .= $this->item->getDataValue('volume');	
		$outputed = true;					  						
	} else if ($this->resource->getDataValue('volume')) {
		$output .= $this->resource->getDataValue('volume');	
		$outputed = true;					  						
	} 

	if ($this->item->getDataValue('issue')) {
		if($outputed) $output .= ", ";
		$output .= $this->item->getDataValue('issue');		
	} else if ($this->resource->getDataValue('issue')) {
		if($outputed) $output .= ", ";
		$output .= $this->resource->getDataValue('issue');	
	} 
	
	if ($this->item->getDataValue('part')) {
		if($outputed) $output .= ", ";
		$output .= $this->item->getDataValue('part');
	}
	
	$output = trim($output," ,");
	echo $output;
	