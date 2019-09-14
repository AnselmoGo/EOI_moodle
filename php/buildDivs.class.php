<?php
/*
** This class takes a string or an image to build a div with Ids related to the string or the name of the image
** Both the string name and the image name should be related, so that they can be used in a memory game.
** Properties:	$_cnt 	-> counts the items to create a numbered class in order to build a grid element
**							$_needle	-> array that contains part of the total string that will be checked against
**							$_value		-> value that is passed on to be checked against the needle array
**							$_checked	-> boolean value that indicates if $_value contains one of the "needles"
**							$_txt			-> build element with 2 divs, one to cover the item, the other the item itself
** Methods: setValue()	-> sets the value that will be checked against the needle
**					checkNeedle()	-> counts the $_needle array and checks if $_value contains one of its items
**					private buildNameDiv()	-> builds the Div from a string, using it as the "id"
**					private buildImgDiv()		-> builds the Div from an image using its name a the "id
**					buildDiv()		-> decides whether the item is a string or an image, calls the private methods and 
**														returns the constructed Div as $this->_txt
*/
require_once('./php/substitute_Umlaute.class.php');

class buildDivs {
	private $_cnt = 1;
	private $_needle = array();
	private $_value = "";
	private $_checked = false;
	private $_txt = '';

	function __construct($needle) {
		$this->_needle = $needle;		
	}

	function setValue($value) {
		$this->_value = $value;
	}

	function checkNeedle() {
		$numNeedle = count($this->_needle);
		for($i = 0; $i < $numNeedle; $i++) {				
			if(strstr($this->_value, $this->_needle[$i]) !== false) {				
				$this->_checked = true;			
				break;
			}			
		}
	}

	private function buildNameDiv() {
		//substitutes the Umlaute
		$umlaute = new Substitute_Umlaute($this->_value);
		$newValue = $umlaute->replace();
		$newValue = strtolower($newValue);

		$this->_txt = "<div id='{$newValue}' class='item{$this->_cnt} front_item' onclick='visualiza(this)'></div>\n";
		$this->_txt .= "<div class='item{$this->_cnt}'>$this->_value</div>\n";
	}

	private function buildImgDiv() {
		$img = $this->_value;
		$job = explode("/", $this->_value);
		$job_name = count($job)-1;	//position where we get the job name
		$job = explode(".", $job[$job_name]);
		$job_name = 0;	//position where we get the job name
		$job[$job_name] = strtolower($job[$job_name]);
		$this->_txt = "<div id='x{$job[$job_name]}' class='item{$this->_cnt} front_item' onclick='visualiza(this)'></div>\n";
		$this->_txt .= "<div class='item{$this->_cnt}'>" . "<img src='$img' alt='$job[$job_name]'></div>\n";
	}

	function buildDiv() {
		if($this->_checked === false) {
			$this->buildNameDiv();
		} else {
			$this->buildImgDiv();
			$this->_checked = false;
		}

		$this->_cnt++;		
		return $this->_txt;
	}
}
?>