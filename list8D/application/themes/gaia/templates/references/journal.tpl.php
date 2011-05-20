<?php 
	$outputed = false;
	$published = false;
	$output = "";
	
	// part_author date part_title
	if ($this->item->getDataValue("part_author")) {
		$output .= $this->item->getDataValue("part_author");
		$outputed = true;
	} else if ($this->item->getResource()->getDataValue("authors")) {
		$output .= $this->item->getResource()->getDataValue("authors");
		$outputed = true;
	}
	
	
	if ($this->item->getDataValue("published")) {
		$output .= " (".$this->item->getDataValue("published").") ";
		$published = true;
		$outputed = true;
	} else if ($this->item->getResource()->getDataValue("published")) {
		$output .= " (".$this->item->getResource()->getDataValue("published").") ";
		$published = true;
		$outputed = true;
	}
	
	if (!$published && $outputed) {
		$output .= ", ";
	}


	if($this->item->getDataValue("year") && $this->item->getDataValue("year") != "") {
		$output .= " ".$this->item->getDataValue("year").", ";
		echo "Hello";
	} elseif($this->item->getResource()->getDataValue("year") && $this->item->getResource()->getDataValue("year") != "") {
		$output .= " ".$this->item->getResource()->getDataValue("year").", ";
		echo "Hi";
	}
	
	if ($this->item->getDataValue("part_title")) {
		$output .= "'".$this->item->getDataValue("part_title")."' ";
	} elseif ($this->item->getDataValue("journal")) {
		$output .= "'".$this->item->getDataValue("title")."' ";
	} else {
		$output .= $this->item->getResource()->getDataValue("title")." ";
	}
	
	if (($this->item->getResource()->getDataValue("title") && $this->item->getDataValue("part_title")) || $this->item->getResource()->getDataValue("journal")) {
		$output .= " in ";
		$outputed = true;
	}
	
	$outputed = false;
	$published = false;
	
	// authors [date] title
	if ($this->item->getResource()->getDataValue("authors") && $this->item->getDataValue("part_author")) {
		$output .= $this->item->getResource()->getDataValue("authors");
		$outputed = true;
	} 
	if (($this->item->getResource()->getDataValue("title") && $this->item->getDataValue("part_title")) || $this->item->getResource()->getDataValue("journal")) {
		if ($this->item->getDataValue("part_title")) $output .= "<em>";
		if ($this->item->getResource()->getDataValue("journal"))
			$output .= $this->item->getResource()->getDataValue("journal")." ";
		elseif ($this->item->getResource()->getDataValue("title") && $this->item->getDataValue("part_title"))
			$output .= $this->item->getResource()->getDataValue("title")." ";
		
		if ($this->item->getDataValue("part_title")) $output .= "</em>";
		$outputed = true;
	}
								  					
	
	if ($this->item->getDataValue('volume')) {
		$output .= $this->item->getDataValue('volume');	
		$outputed = true;					  						
	} else if ($this->item->getResource()->getDataValue('volume')) {
		$output .= $this->item->getResource()->getDataValue('volume');	
		$outputed = true;					  						
	} 

	if ($this->item->getDataValue('issue')) {
		if($outputed) $output .= ", ";
		$output .= $this->item->getDataValue('issue');		
	} else if ($this->item->getResource()->getDataValue('issue')) {
		if($outputed) $output .= ", ";
		$output .= $this->item->getResource()->getDataValue('issue');	
	} 
	
	if ($this->item->getDataValue('part')) {
		if($outputed) $output .= ", ";
		$output .= $this->item->getDataValue('part');
	}
	
	$output = trim($output," ,");
	echo $output;
	