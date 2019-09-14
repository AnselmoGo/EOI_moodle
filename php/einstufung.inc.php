<?php 		
	include_once('randomizer.class.php');

	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");
		
	// variable used to create a random item out of a list and increment the list with the item
	$randomize = new Randomizer($_SESSION['min_num'], $_SESSION['max_num']);
	$randomize->setList($_SESSION['done']);

	$i = $randomize->getRandomNumber();
	//saves the chosen number onto the session variable
	$_SESSION['done'] .= "$i,";

	$result = $mysqli->query("SELECT referencia,pregunta,resp0,resp1,resp2,resp3,solucion,clave,gram FROM $table WHERE id = $i AND tipo_ejercicio = '$type'");
	$row = $result->fetch_array(MYSQLI_ASSOC);

	//copy the results in an array
	for($i = 0; $i < 4; $i++) {
		$opt[$i] = $row["resp$i"];
	}

	//randomize the order of the elements in the array
	shuffle($opt);

	$ubung = "";
$ubung = <<<EOT
	<span id="question0" class="large">{$row['referencia']} - {$row['pregunta']}</span><br />\n
	<div class='tab large'>
		<input id="test1" name='test' value="{$opt[0]}" type='radio'> {$opt[0]}</input><br />\n
		<input id="test2" name='test' value="{$opt[1]}" type='radio'> {$opt[1]}</input><br />\n
		<input id="test3" name='test' value="{$opt[2]}" type='radio'> {$opt[2]}</input><br />\n
		<input id="test4" name='test' value="{$opt[3]}" type='radio'> {$opt[3]}</input><br />\n
		<input id="sol" type="hidden" value="{$row['solucion']}" />\n			
		<input id="key" type="hidden" value="{$row['clave']}" />\n			
		<input id="gram" type="hidden" value="{$row['gram']}" />\n
	</div>
EOT;

echo $ubung;