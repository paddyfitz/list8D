<?php

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
	echo $output;
