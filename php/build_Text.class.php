<?php
/*
**	
**	This class builds the exercise text out of an ordinary text with numbers
**	
*/
class Build_Text {
	private $_table = '';
	private $_tbsId = 0;
	private $_num_gap = -1;
	private $_num_img = -3;
	private $_num_gaps = 0;
	private $_num_rows = 0;
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
		$this->_num_gaps = $row['MAX(gap)'];
	}

	private function get_Images($mysqli, $num) {
		$result = $mysqli->query("SELECT img FROM $this->_table WHERE textbausteinID = $this->_tbsId AND imgID = $num");
		$row = $result->fetch_assoc();

		$img = "<img class='$this->_floating $this->_padding' src='{$row['img']}' alt='img$num'>";
		
		if($this->_padding == "pad_right") {
			$this->_padding = "pad_left";
			$this->_floating = "float-right";
		} else {
			$this->_padding = "pad_right";
			$this->_floating = "float-left";
		}

		$this->_txt = str_replace("#$num#", $img, $this->_txt);
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

	function get_Text($mysqli, $forms){
		$result = $mysqli->query("SELECT exercise FROM $this->_table WHERE gap = $this->_num_gap ORDER BY id");
		while ($row = $result->fetch_assoc()) {
			$this->_txt .= $row['exercise'];
		}
		
		$this->get_Max_gaps($mysqli);
		
		if($forms != "text") {
			$this->build_Select_Option($mysqli);
		}		

		for($i = 0; $i <= $this->_num_gaps; $i++) {
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

			if(strstr($this->_txt, "#$i#") !== false) {
				$this->get_Images($mysqli, $i);
			}
		}

		return $this->_txt;
	}

	function get_Solution($mysqli) {
		$result = $mysqli->query("SELECT solution FROM $this->_table WHERE gap >= 0 ORDER BY gap");
		$this->_num_rows = $mysqli->affected_rows;

		while($row = $result->fetch_assoc()) {
			$solution[] = $row['solution'];
		}

		return $solution;
	}

	function get_Rows() {
		return $this->_num_rows;
	}
}
?>