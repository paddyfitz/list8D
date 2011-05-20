<?php 
	$outputed = false;
	$published = false;
	$output = "";
	
	if ($this->resource->getDataValue("authors")) {
		$output .= $this->resource->getDataValue("authors");
		$outputed = true;
	}
	if ($this->resource->getDataValue("publication_date")) {
		$output .= " (".$this->resource->getDataValue("publication_date").") ";
		$published = true;
	}
	if (!$published && $outputed) {
		$output .= ", ";
	}
	$output .= "'".$this->resource->getDataValue("title")."'";
	$output .= " in ";
	$outputed = true;
	
	$outputed = false;
	$published = false;
	
	
	if ($this->resource->getDataValue("journal")) {
		$output .= "<em>";
		$output .= $this->resource->getDataValue("journal");
		$outputed = true;
		$output .= "</em>";
	}
	
	if ($this->resource->getDataValue('volume')) {
		$output .= " ".$this->resource->getDataValue('volume');
		$outputed = true;					  						
	}
	
	if($outputed) $output .= ", ";
	
	if ($this->resource->getDataValue('issue')) {
		$output .= $this->resource->getDataValue('issue').":";
	}
	
	if ($this->item->getDataValue('part')) 
		$output .= $this->item->getDataValue('part');
	
	$output = trim($output," ,:");
	echo $output;
	