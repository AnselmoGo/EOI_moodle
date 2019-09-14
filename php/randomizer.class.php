<?php
/*
** This class returns a gets and returns a random number between $_min and $_max.
** Previously, it has been checked that the number is not member of a list $_listaNumeros.
** It is only getting a number if the number of elements in the list is smaller than ($_max - $_min + 1)
** Properties: 	$_min, $_max 	-> range in which the randomization takes place
**							$_number			-> randomized number
**							$_listaNumeros	-> list of the already randomized numbers in a string
**							$_arrayNumeros	-> array of the same randomized numbers
**							$_countLista		-> number of items in the list
**
** Methods:	private setCountList()			-> constructs an array out of the list and counts the items in the array
**					setMinMax()					-> (re)sets the values for $_min & $_max
**					setList()						-> establishes the list and calls on "setCountList()"
**					private includeInList()			-> includes a new number in the list and calls on "setCountList()"
**					getRandomNumber()		-> gets the random number, calls on "includeInList()" to include it and returns it
*/

class Randomizer {
	private $_min = 0;
	private $_max = 0;
	private $_number = 0;
	private $_listaNumeros = '';
	private $_arrayNumeros = array();
	private $_countLista = 0;

	function __construct($min, $max){
		$this->_min = $min;
		$this->_max = $max;
	}
	
	private function setCountList() {
		$this->_arrayNumeros = explode(',', $this->_listaNumeros);
		$this->_countLista = count($this->_arrayNumeros);
	}

	function setMinMax($min, $max) {
		$this->_min = $min;
		$this->_max = $max;
	}

	function setList($lista) {
		$this->_listaNumeros = $lista;
		$this->setCountList();
	}

	private function includeInList($number) {
		$this->_listaNumeros .= "$number,";
		$this->setCountList();
	}

	function getRandomNumber() {
		if($this->_countLista <= ($this->_max - $this->_min + 1)) {
			$this->_number = rand($this->_min, $this->_max);
			//if the number is already in the list the system will choose another one			
			while(strstr($this->_listaNumeros,",$this->_number,")) {
				$this->_number = rand($this->_min, $this->_max);
			}
		}
		$this->includeInList($this->_number);
		return $this->_number;
	}

}
?>