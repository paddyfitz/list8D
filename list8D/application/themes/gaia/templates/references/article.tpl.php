<?php 
	$outputed = false;
	$published = false;
	$output = "";
	
	if ($this->item->getResource()->getDataValue("authors")) {
		$output .= $this->item->getResource()->getDataValue("authors");
		$outputed = true;
	}
	if ($this->item->getResource()->getDataValue("published")) {
		$output .= " (".$this->item->getResource()->getDataValue("published").") ";
		$published = true;
	}
	if (!$published && $outputed) {
		$output .= ", ";
	}

	if($this->item->getDataValue("year") && $this->item->getDataValue("year") != "") {
		$output .= " ".$this->item->getDataValue("year").", ";
	} elseif($this->item->getResource()->getDataValue("year") && $this->item->getResource()->getDataValue("year") != "") {
		$output .= " ".$this->item->getResource()->getDataValue("year").", ";
	}

	$output .= "'".$this->item->getResource()->getDataValue("title")."'";
	$output .= " in ";
	$outputed = true;
	
	$outputed = false;
	$published = false;
	
	
	if ($this->item->getResource()->getDataValue("journal")) {
		$output .= "<em>";
		$output .= $this->item->getResource()->getDataValue("journal");
		$outputed = true;
		$output .= "</em>";
	}
	
	if ($this->item->getResource()->getDataValue('volume')) {
		$output .= " ".$this->item->getResource()->getDataValue('volume');
		$outputed = true;					  						
	}
	
	if($outputed) $output .= ", ";
	
	if ($this->item->getResource()->getDataValue('issue')) {
		$output .= $this->item->getResource()->getDataValue('issue').":";
	}
	
	if ($this->item->getDataValue('part')) 
		$output .= $this->item->getDataValue('part');
	
	$output = trim($output," ,:");
	echo $output;
	