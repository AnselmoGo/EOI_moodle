<?php 		
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");

	//$min_num = 1;
		
	$done = explode(',', $_SESSION['done']);
	//Kondition um weitere Übungen darzustellen
	if(count($done) <= $max_num) {
		$i = rand($min_num,$max_num);

		//checks if the number $i has already appeared in the test
		//if it has, it chooses another number
		while (strstr($_SESSION['done'],"$i")) {		
			$i = rand($min_num,$max_num);	  			
		}
		
		$_SESSION['done'] .= $i . ',';	


		//echo "Liste: #" . $_SESSION['done'] . "#<br>";	

	$result = $mysqli->query("SELECT referencia,pregunta,resp1,resp2,resp3,resp4,solucion,clave,gram FROM $table WHERE id = $i");
	$row = $result->fetch_array(MYSQLI_ASSOC);

	$ubung = "";
$ubung = <<<EOT
	<span id="question" class="large">{$row['referencia']} - {$row['pregunta']}</span><br />\n
	<div class='tab large'>
		<input id="test1" name='test' value="{$row['resp1']}" type='radio'> {$row['resp1']}</input><br />\n
		<input id="test2" name='test' value="{$row['resp2']}" type='radio'> {$row['resp2']}</input><br />\n
		<input id="test3" name='test' value="{$row['resp3']}" type='radio'> {$row['resp3']}</input><br />\n
		<input id="test4" name='test' value="{$row['resp4']}" type='radio'> {$row['resp4']}</input><br />\n
		<input id="sol" type="hidden" value="{$row['solucion']}" ></input>\n			
		<input id="key" type="hidden" value="{$row['clave']}" ></input>\n			
		<input id="gram" type="hidden" value="{$row['gram']}" ></input>\n			
	</div>	
EOT;

	}

echo $ubung;