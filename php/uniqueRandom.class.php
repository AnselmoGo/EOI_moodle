<?php
/*
**	This class gets a unique number betweet $_minId & $_maxId that is not in the $_number array
**	Properties:	$_minId	-> the lowest number 
**							$_maxId	-> the hightest number (has to be set in the instantiation)
**							$_num 	-> the selected number
**							$_number-> the array that includes the selected numbers
**	Methods:	setMinId()		-> sets the lowest number if it's other than 1
**						getUniqueId()	-> chooses a unique random number that is not included in the $_number array 
*/

class UniqueRandom {
	private $_minId = 1;
	private $_number = array();
	private $_maxId = 1;
	private $_num = null;

	function __construct($maxId) {
		$this->_maxId = $maxId;
	}

	function setMinId($minId) {
		$this->_minId = $minId;
	}

	function getUniqueId() {
		$this->_num = rand($this->_minId, $this->_maxId);
		//if $this->_num is in the array we have to get another one
		while(array_search($this->_num, $this->_number) !== false) {
			$this->_num = rand($this->_minId, $this->_maxId);
		}
		$this->_number[] = $this->_num;
		return $this->_num;
	}
}
?>