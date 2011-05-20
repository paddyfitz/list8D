<?php

class List8D_Theme_Root_Helper_Date extends List8D_ViewHelper {

	public function date($date,$style='default') {
		$themeSettings = List8D_Config_Ini::getThemeSettings();
		$d = new DateTime($date);
		return $d->format($themeSettings->dateFormat->$style);
	}

}