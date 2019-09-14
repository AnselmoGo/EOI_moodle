<?php
/*
** This class builds an exercise with radio buttons from the different inputs given
** Properties:	$_id				-> ID that is used to distinguish the different radio groups
**							$_question	-> question used at the start of the radio group
**							$_options		-> different options that will be exposed in the radio group
**							$_solution	-> solution of the exercise
**							$_ex				-> variable to store the whole exercise
** Methods:	setId(), setQuestion(), setOptions(), setSolution()	-> used to set the different variables
**					buildRadio()	-> used to build the radio group, including the question and the hidden solution;
**														builds a number of options depending on the $_num_options property
*/

class Builder {
	private $_id = 0;
	private $_question = '';
	private $_options = array();
	private $_num_options = 0;
	private $_solution = '';
	private $_ex = '';

	function __construct() {}

	function setId($id) {
		$this->_id = $id;
	}

	function setQuestion($quest) {
		$this->_question = $quest;
	}

	function setOptions($options) {
		$this->_options = $options;
		$this->_num_options = count($options);
	}

	function setSolution($solution) {
		$this->_solution = $solution;
	}

	function buildRadio() {
$this->_ex = <<<EOT
	<span id="question$this->_id" class="large">$this->_question</span><br />\n
	<div class='tab large'>
EOT;
	for($i = 0; $i < $this->_num_options; $i++) {
$this->_ex .= <<<EOF
	<input id="test{$this->_id}_$i" name='$this->_id' value="{$this->_options[$i]}" type='radio'> {$this->_options[$i]}</input><br />\n
EOF;
	}
$this->_ex .= <<<EOG
		<input id="sol$this->_id" type="hidden" value="{$this->_solution}" />\n
	</div>
EOG;
	return $this->_ex;
	}

}
?>