<?php
/*
** This class writes a new text part into the file-position you chose.
** The file that will be changed is given in the constructor
** Properties:	$_file		-> name of the file that is being changed/written
**							$_fArray	-> array to which the file is written to
**							$_splitArray	-> array into which the $_fArray is split depending on the $needle given in 
**															 splitArray()
**							$_string	-> string into which the final array is converted
** Methods:	splitArray()	-> splits the array created from the file depending on the $needle given (not more than 
**													 2 arrays)
						mergeArrays()	-> introduces the new array in between the 2 splitArray parts
						makeString()	-> converts the final array into a string
						writeFile()		-> writes the string into the original file
*/

class AddFile {
	private $_file = '';
	private $_fArray = array();
	private $_splitArray = array();
	private $_string = '';

	function __construct($file) {
		$this->_file = $file;
	}

	function splitArray($needle) {
		$this->_fArray = file($this->_file,  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		$num = count($this->_fArray);
		for($i = 0; $i < $num; $i++) {
			if(strpos($this->_fArray[$i], $needle) !== FALSE) {
				$this->_splitArray = array_chunk($this->_fArray, $i);
				break;
			}
		}
	}

	function mergeArrays($addArray) {

		$feedback = "<br />Sorry, this name is already given. Please choose another to build the page.";		
		
		foreach ($this->_splitArray[0] as $key => $value) {
			if($value == $addArray[0]) {
				return $feedback;
			}
		}
		foreach ($this->_splitArray[1] as $key => $value) {
			if($value == $addArray[0]) {
				return $feedback;
			}
		}
		
		$this->_fArray = array_merge($this->_splitArray[0], $addArray, $this->_splitArray[1]);
		return true;
	}

	function makeString() {
		foreach ($this->_fArray as $value) {
			$this->_string .= "$value\r\n";
		}		
	}

	function writeFile() {
		if(!$handle = fopen($this->_file, "w+")) {
			echo "<br />Datei konnte nicht geöffnet werden.";
		}

		if(!fwrite($handle, $this->_string)) {
			echo "Could not write into the file";
		} else {
			echo "File rewritten successfully!";
		}

		fclose($handle);
	}
}
?>