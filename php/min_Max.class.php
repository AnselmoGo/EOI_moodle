<?php
/*
** This class establishes the levelID from an exercise and its max & min numbers
** The default table that is given in the constructor can be changed if needed
** Properties: 	$_max_number, $_min_number -> the min & max number in a given table
**							$_levelID	-> the ID related to the level that is being examined
**							$_table		-> the table in which the ID is searched
** Methods:			setLevelID()	-> sets the $_levelID related to the $_table given
**							setMinMax()		-> sets the min & max numbers related to the $_levelID
**							getLevelID()	-> returns the $_levelID
**							getMinNumber()	-> returns the $_min_number
**							getMaxNumber()	-> returns the $_max_number
*/

class Min_Max {
	private $_max_number = 0;
	private $_min_number = 0;
	private $_levelID = 1;
	private $_table = '';

	function __construct($table) {
		$this->_table = $table;
	}

	function setTable($table) {
		$this->_table = $table;
	}

	function setLevelID($level) {		
		$this->_levelID = $level;
	}

	function setMinMax($mysqli, $idioma, $pid = 0) {
		//selects min & max id from table items depending on language and level
		$result = $mysqli->query("SELECT MIN(id), MAX(id) FROM $this->_table WHERE idioma = '$idioma' AND pid = $pid AND levelID = {$this->_levelID}");
		//$result = $mysqli->query("SELECT MIN(id), MAX(id) FROM items WHERE idioma = 'al' AND levelID = 1");
		$row = $result->fetch_array(MYSQLI_ASSOC);
		//Variablen, die an die Ajax Funktion übergeben werden
		$this->_min_number = $row['MIN(id)'];
		$this->_max_number = $row['MAX(id)'];
	}

	function getLevelID() {
		return $this->_levelID;
	}

	function getMinNumber() {
		return $this->_min_number;
	}

	function getMaxNumber() {
		return $this->_max_number;
	}

}
?>