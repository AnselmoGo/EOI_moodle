<?php
/*
**	This class builds the exercise text out of an ordinary text with numbers
**	Properties:	$_table		-> name of the working table
**				$_tbsId 	-> name of the "textbausteinId" in case there is an entry in the db
**				$_num_gap 	-> flag to indicate that the entry does not correspond to a gap
**				$_num_img	-> flag to indicate that the entry corresponds to an image
**				$_num_gaps	-> number of gaps in the exercise
**				$_select_option	-> string to build the multiple select-optionsç
**				$_txt		-> text that will be composed in the end
**				$_padding	-> a class of the html tag, indicates where the padding will be
**				$_floating	-> floating class of the html tag, indicates where to float the image
**
**	Methods:	set_TbsId()		-> sets the "TextbausteinID"
**				private get_Max_gap()	-> gets the maximum number of gaps in the exercise
**				private build_Select_Option()	-> builds the options for the select in the text
**				private build_Gap()		-> builds the gap as a text input or a dropdown menu (select)
**				get_Images()	-> gets an image from the db and places it in the corresponding position in the text
**				get_Text()		-> creates the text from the db, replaces the numbers with gaps
**				get_Solution()	-> extracts the solutions (array) from the db 			
**				get_Gaps()		-> gets and returns the number of gaps in the exercise	
*/

class Build_Text {
	private $_table = '';
	private $_tbsId = 0;
	private $_num_gap = -1;
	private $_num_img = -3;
	private $_num_gaps = 0;	
	private $_select_option = '';
	private $_txt = '';
	private $_padding = "pad_right";
	private $_floating = "float-left";
	

	function __construct($table){
		$this->_table = $table;
		
	}

	function set_TbsId($TbsId) {
		$this->_tbsId = $TbsId;
	}
	
	private function get_Max_gaps($mysqli) {
		$result = $mysqli->query("SELECT MAX(gap) FROM $this->_table");
		$row = $result->fetch_assoc();
		$this->_num_gaps = $row['MAX(gap)'] + 1;   //incremented because starts with 0, we are counting gaps
	}

	private function build_Select_Option($mysqli) {		
		$this->_select_option .= "<option>- ??? -</option>";

		$result = $mysqli->query("SELECT solution FROM $this->_table WHERE gap != $this->_num_gap AND gap != $this->_num_img");
		while($row = $result->fetch_assoc()) {			
			$solution[] = $row['solution'];
		}

		shuffle($solution);

		foreach ($solution as $value) {
			$this->_select_option .= "<option>$value</option>";
		}
	}

	private function build_Gap($forms) {

		for($i = 0; $i < $this->_num_gaps; $i++) {
			$select = sprintf("<span name='outer_item%d' id='outer_item%d'>", $i, $i);
			if($forms == "text") {
				$select .= sprintf("<input class='form-control form-low' id='item%d' type='text' size='10' maxlength='20'>", $i);
			} else {				
				$select .= sprintf("<select name='item%d' id='item%d' class='form-control-sm'>", $i, $i);
				$select .= $this->_select_option;
				$select .= "</select>";
			}
			$select .= "</span>";
			
			$this->_txt = str_replace("-$i-", $select, $this->_txt);
		}
	}

	function get_Images($mysqli) {
		$result = $mysqli->query("SELECT MAX(imgID) FROM $this->_table");
		$row = $result->fetch_assoc();

		$num_img = $row['MAX(imgID)'];

		for($i = 1; $i <= $num_img; $i++) {
			$result = $mysqli->query("SELECT img FROM $this->_table WHERE textbausteinID = $this->_tbsId AND imgID = $i");
			$row = $result->fetch_assoc();

			$img = "<img class='$this->_floating $this->_padding' src='{$row['img']}' alt='img$i'>";
			
			if($this->_padding == "pad_right") {
				$this->_padding = "pad_left";
				$this->_floating = "float-right";
			} else {
				$this->_padding = "pad_right";
				$this->_floating = "float-left";
			}

			$this->_txt = str_replace("#$i#", $img, $this->_txt);
		}

		return $this->_txt;
	}

	function get_Text($mysqli, $forms){
		$result = $mysqli->query("SELECT exercise FROM $this->_table WHERE gap = $this->_num_gap ORDER BY id");
		while ($row = $result->fetch_assoc()) {
			$this->_txt .= $row['exercise'];
		}
		
		$this->get_Max_gaps($mysqli);
		
		if($forms != "text") {
			$this->build_Select_Option($mysqli);
		}

		$this->build_Gap($forms);

		return $this->_txt;
	}

	function get_Solution($mysqli) {
		$result = $mysqli->query("SELECT solution FROM $this->_table WHERE gap >= 0 ORDER BY gap");		

		while($row = $result->fetch_assoc()) {
			$solution[] = $row['solution'];
		}

		return $solution;
	}

	function get_Gaps() {
		return $this->_num_gaps;
	}
}
?>