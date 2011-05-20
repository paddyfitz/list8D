<?php 
	$outputed = false;
	$published = false;
	$output = "";
	// part_author date part_title
	if ($this->item->getDataValue("part_title")) {
		if ($this->item->getDataValue("part_author")) {
			$output .= $this->item->getDataValue("part_author");
			$outputed = true;
		}
		if ($this->item->getResource()->getDataValue("published")) {
			$output .= " (".$this->item->getResource()->getDataValue("published").") ";
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
	if ($this->item->getResource()->getDataValue("authors")) {
		$output .= $this->item->getResource()->getDataValue("authors");
		$outputed = true;
	} 
	if (!$this->item->getDataValue("part_title")) {
		if ($outputed) $output .= ", ";
		if ($this->item->getResource()->getDataValue("published")) {
			$output .= " (".$this->item->getResource()->getDataValue("published").") ";
			$published = true;
		}
	}
	if (!$published && $outputed) {
		$output .= ", ";
	}
	if ($this->item->getResource()->getDataValue("title")) {
		if ($this->item->getDataValue("part_title")) $output .= "<em>";
		$output .= $this->item->getResource()->getDataValue("title");
		$outputed = true;
		if ($this->item->getResource()->getDataValue("edition")) {	
			if ($outputed) $output .= ", ";
			$output .= " ".$this->item->getResource()->getDataValue("edition");
		}
		if ($this->item->getDataValue("part_title")) $output .= "</em>";
		$outputed = true;
	}
	if ($outputed) $output .= ", ";
	
	// publisher
	if ($this->item->getResource()->getDataValue("publisher")) {
		$output .= $this->item->getResource()->getDataValue("publisher");
	}
							  					
	if ($this->item->getDataValue('part')) 
		$output .= " ".$this->item->getDataValue('part');
	
	$output = trim($output," ,");
	echo $output;
	