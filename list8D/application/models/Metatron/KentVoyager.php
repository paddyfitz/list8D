<?php

class List8D_Model_Metatron_KentVoyager extends List8D_Model_Metatron_Voyager {

	/**
	 * Each metatron requires a unique namespace. We recommend this matches
	 * the class name. This will be referred to in the database, so changing
	 * it later on will cause pain.
	 *
	 * @return string Namespace for this metatron
	 */
	public static function getNamespace() {
		return "KentVoyager";
	}

	/**
	 * Each metatron should return a friendly name. This is used for display
	 * purposes only.
	 *
	 * @return string Friendly name
	 */
	public static function getName() {
		return "Kent Library Catalogue";
	}

	/**
	 * Class constructor - set up important connection info here
	 */
	function __construct() {
		$this->_conn = yaz_connect("nemesis.kent.ac.uk:7090");
		yaz_syntax($this->_conn, "USMARC");
		yaz_database($this->_conn, "Voyager");
		yaz_range($this->_conn, 1, 10);
	}

	/**
	 * Return link to the opac
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string url
	 */
	protected function getCanonicalURL ($_record) {
		return "https://catalogue.kent.ac.uk/Record/" . $this->getRCN($_record);
	}


	/**
	 * Each metatron should register a UR: regexp match for future bookmarklet
	 * functionality.
	 *
	 * @return string URL regex match
	 */
	public static function registerUrl () {
		return "https:\/\/catalogue.kent.ac.uk\/.*";
	}

}

