<?php
/* Diese class baut die Übung auf, die auf der Seite angezeigt werden soll;
** Es werden folgende Aufgaben durchgeführt
** setzt die Nummer wo das Bild eingefügt wird und das Tag dazu		-> set_include_Image()
** macht eine DB Abfrage mit der '$selection' als Array 		-> start_Request()
** baut den gesamten Text auf, der in der Seite gezeigt wird 		-> build_Text()
**			'$select_replace' = Selectwert der DB der ersetzt werden soll (üblicherweise eine Nummer), 
**			'$select_ex' = Wert aus der DB, der den Satz beinhaltet, der eingefügt wird, 
**			'$num_ex = -1' wenn jedes Item aus einem Satz besteht, wenn man die Sätze aus mehreren Items 
**					zusammensetzen muss '$num_ex = 0' normalerweise
** private Funktion, um den 'input' zu konstruieren, der den $select_replace ersetzen soll 		
**			-> build_Replace()
** private Funktion, die den Zähler baut, falls 1 Item/Satz entspricht 		-> build_Counter()
** 4 Get-Funktionen
*/
class Build_Exercise {
	protected $_table = '';				// table to access the DB
	protected $_text = '';					// text to finally include in the exercise
	protected $_replace = '';				// item that replaces the numeration in the sentences with 'input' tags
	protected $_img_num = -1;				// number where the image has to be included
	protected $_img = '';						// image to be included
	protected $_result;					// result of a query

	private $_counter = '';				// enumerates the sentences when they are not subdivided
	private $_ex = 0;							// counts the number of exercises/gaps

	function __construct($table) {
		$this->_table = $table;
	}

	function set_include_Image($img_num, $img) {
		$this->_img_num = $img_num;
		$this->_img = $img;
	}

	// selection must be an array, in case there are several selection items
	function start_Request($mysqli, $selection, $condition){
		$sel = '';
		foreach ($selection as $value) {
			$sel .= "$value,";
		}
		$sel = rtrim($sel, ',');		
		$this->_result = $mysqli->query("SELECT $sel FROM $this->_table WHERE $condition");
	}

	function build_Text($mysqli, $select_replace, $select_ex, $condition, $num_ex = -1) {
		$satz = '';		// aux variable to build the text
		
		while($row = $this->_result->fetch_array(MYSQLI_ASSOC)) {

			$this->build_Replace($row[$select_replace]);

			$this->build_Counter($row, $select_replace, $num_ex); 	
		
			$row[$select_ex] = $this->_counter . str_replace("-{$row[$select_replace]}-", $this->_replace, $row[$select_ex]);

			if(isset($num_ex) && $num_ex > -1) {

				// returns position of "#" in the string "$row[$select_ex]" or false if not found
				$pos = strpos($row[$select_ex], "#");

				if ($pos === false) {		
					// if the is no "#" at the beginning of the sentence
					// the part of the sentence is added to the one saved in "$satz"
					$satz .= $row[$select_ex];
				} else {
					if($satz != '') {
						$this->_text .= "<p class='tbs_small'>$satz</p>";
					}		
					// only if $num_ex > -1, i.e. there are partitioned sentences
					$aux = ++$num_ex . ". ";	
					$satz = str_replace("#", $aux, $row['exercise']);
				}

				// gets the total sentences in the exercise	
				$res = $mysqli->query("SELECT id FROM $this->_table WHERE $select_ex LIKE '%#%' && $condition");			

				// writes the final sentence into the exercise 
				// if "$num_ex" = the number of row form last query
				if($num_ex == $mysqli->affected_rows) {
					$this->_text .= "<p class='tbs_small'>$satz</p>";
				}
			} else  {
				$this->_text .= "<p class='tbs_small'>{$row[$select_ex]}</p>";
			}

			// includes the image if there is one
			if($this->_img_num != -1 && $row[$select_replace] == $this->_img_num) {
				$this->_text .= $this->_img;				
			}

			// counts the number of exercises
			 $this->_ex++;
		}
		$this->_result->free();
	}

	// creates the replace item that replaces the numeration in the sentences
	public function build_Replace($replace, $letter = 'g', $size = 12, $maxlength = '', $name = '') {
		$tag_aux = '';
		$aux = $letter . $replace;
		$aux2 = $letter . $aux;
		if($maxlength != '') {
			$tag_aux = "maxlength=$maxlength ";
		}
		if($name != '') {
			$tag_aux .= "name=$name "; 
		}		 	
		$this->_replace = "<span id='$aux'><input class='form-control form-low' id='$aux2' type='text' size=$size $tag_aux></span>";
	} 

	// builds counter to numerate sentences if they are not subdivided (usually taken from $row['gap'])
	private function build_Counter($row, $select_replace, $num_ex) {
		if($num_ex == -1) {			
			$this->_counter = ($row[$select_replace] + 1) . ". ";
		}	
	}

	function get_Result() {
		return $this->_result;
	}

	function get_Number_Gaps() {
		return $this->_ex;
	}

	function get_Text() {
		return $this->_text;
	}

	function get_Replace() {
		return $this->_replace;
	}
}
?>