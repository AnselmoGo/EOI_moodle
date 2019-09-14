<?php
/*
** This class is to substitute the German Umlaute into "ascii equivalent"
** Properties:	$_value -> value to be replaced
** Methods:		replace()	-> replaces the Umlaute & the ß 
** 
*/

class Substitute_Umlaute {
	private $_value = '';

	function __construct($value) {
		$this->_value = $value;
	}

	function replace() {
		if(strstr($this->_value, "ä") !== false) {									
			$this->_value = str_replace("ä", "ae", $this->_value);								
		}
		if(strstr($this->_value, "ö") !== false) {
			$this->_value = str_replace("ö", "oe", $this->_value);
		}
		if(strstr($this->_value, "ü") !== false) {
			$this->_value = str_replace("ü", "ue", $this->_value);
		}
		if(strstr($this->_value, "ß") !== false) {
			$this->_value = str_replace("ß", "ss", $this->_value);
		}
		if(strstr($this->_value, "Ä") !== false) {
			$this->_value = str_replace("Ä", "ae", $this->_value);
		}
		if(strstr($this->_value, "Ö") !== false) {
			$this->_value = str_replace("Ö", "oe", $this->_value);
		}
		if(strstr($this->_value, "Ü") !== false) {
			$this->_value = str_replace("Ü", "ue", $this->_value);
		}
		return $this->_value;
	}

}
?>