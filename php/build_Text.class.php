<?php
require_once('./php/build_Exercise.class.php');
/*
**	As an extension to Build_Exercise this class builds the exercise when there is a large text
**	Es wird ein Text aus verschiedenen größeren Elementen (Textbausteine) erstellt
*/
class Build_Text extends Build_Exercise {
	private $_number_rows = 0;
	private $_solution = Array();


	function __construct($table){
		parent::__construct($table);
	}
	
	// sets the number of rows for a given sql query
	function set_Number_Rows($mysqli, $select, $condition) {
		$this->_result = $mysqli->query("SELECT $select FROM $this->_table WHERE $condition");
		$this->_number_rows = $mysqli->affected_rows;
	}

	// builds a text with several gaps
	// needs parameters to call build_Replace()
	function build_Text_Long($select_ex, $letter = 'g', $size = 12, $maxlength = '', $name = '') {
		$set_img = 0;
		while($row = $this->_result->fetch_array(MYSQLI_ASSOC)) {					
			for($i = 0; $i < $this->_number_rows; $i++) {
				// calls function from parent class
				$this->build_Replace($i, $letter, $size, $maxlength, $name);
				$row[$select_ex] = str_replace("-$i-", $this->_replace, $row[$select_ex]);
				// includes the image if there is one
				if($this->_img_num != -1 && $set_img == 0) {
					$this->_text .= $this->_img;
					$set_img = 1;
				}
			}
			$this->_text .= "<p class='tbs_small'>{$row[$select_ex]}</p>";
		}		
		$this->_result->free();	
	}

	// sets the solution array
	function set_Solution($solution) {
		while($row = $this->_result->fetch_array(MYSQLI_ASSOC)) {
			$this->_solution[] = $row[$solution];
		}
		$this->_result->free();
	}

	// return the number of rows for a given query
	function get_Number_Rows(){
		return $this->_number_rows;
	}

	// returns the solution array
	function get_Solution() {
		return $this->_solution;
	}
}
?>