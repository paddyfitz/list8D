<?php 
	$outputed = false;
	$published = false;
	$output = "";
	// part_author date part_title!
	if ($this->item->getDataValue("part_title")) {
		if ($this->item->getDataValue("part_author")) {
			$output .= $this->item->getDataValue("part_author");
			$outputed = true;
		}
		if ($this->resource->getDataValue("publication_date")) {
			$output .= " (".$this->resource->getDataValue("publication_date").") ";
			$published = true;
		}
		if (!$published && $outputed) {
			$output .= ", ";
		}
		$output .= "'".$this->item->getDataValue("part_title")."'";
		$output .= " in ";
		$outputed = true;
	}
	$outputed = false;
	$published = false;
	
	
	// authors [date] title
	if ($this->resource->getDataValue("authors")) {
		$output .= $this->resource->getDataValue("authors");
		$outputed = true;
	} 
	if (!$this->item->getDataValue("part_title")) {
		if ($outputed) $output .= ", ";
		if ($this->resource->getDataValue("publication_date")) {
			$output .= " (".$this->resource->getDataValue("publication_date").") ";
			$published = true;
		}
	}
	if (!$published && $outputed) {
		$output .= ", ";
	}
	if ($this->resource->getDataValue("title")) {
		if ($this->item->getDataValue("part_title")) $output .= "<em>";
		$output .= $this->resource->getDataValue("title");
		$outputed = true;
		if ($this->resource->getDataValue("edition")) {	
			if ($outputed) $output .= ", ";
			$output .= " ".$this->resource->getDataValue("edition");
		}
		if ($this->item->getDataValue("part_title")) $output .= "</em>";
		$outputed = true;
	}
	if ($outputed) $output .= ", ";
	
	// publisher
	if ($this->resource->getDataValue("publisher")) {
		$output .= $this->resource->getDataValue("publisher");
	}
							  					
	if ($this->item->getDataValue('part')) 
		$output .= " ".$this->item->getDataValue('part');
	
	$output = trim($output," ,");
	echo $output;
	