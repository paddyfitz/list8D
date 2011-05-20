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
	if ($this->item->getResource()->getDataValue('title')) {
		$output .= $this->item->getResource()->getDataValue('title');
		$outputed = true;
	}
	// url
	if ($outputed) $output .= ", ";
	$output .= $this->item->getResource()->getDataValue('url');
	
	$output = trim($output," ,");
	echo $output;
