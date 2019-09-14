<?php
  require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');

	//create the object to access the db
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");
?>

	<div class="row justify-content-center mt-5">
		<div class="justify-content-center mt-3">
			<h2>Reading a File</h2>
		</div>
		<div class="justify-content-center mt-3 col-10">
			This code reads a file via php
		</div>
	</div>

	<?php

			//opens part of the index to rewrite it
			echo "<br />";
			
			$needle = "default";

			
			$file_array = file("index_switch.php", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

			$aux = count($file_array);

			$extra_array[] = "\tcase 'memorySpiel':";
			$extra_array[] = "\t\t" . '$page = "memory.inc.php";';
			$extra_array[] = "\t\t" . '$page_title = "Memory Spiel";';
			$extra_array[] = "\t\t" . 'break;';

			for($i = 0; $i < $aux; $i++) {
				if(strpos($file_array[$i], $needle) !== FALSE) {
					$new_array = array_chunk($file_array, $i);
					$new_file_array = array_merge($new_array[0], $extra_array, $new_array[1]);
				}
			}

			$string_entry = "";

			foreach ($new_file_array as $key => $value) {
				//echo "New Key: $key --- New Value = $value <br />";
				$string_entry .= $value . "\r\n";
			}

			if(!$handle = fopen("index_switch.php", "w+")) {
				echo "Datei konnte nicht geöffnet werden.";
			} else {
				echo "<br>The file has been found correctly.<br>";
			}
			
			if(!fwrite($handle, $string_entry)) {
				echo "Could not write into the file";
			} else {
				echo "File rewritten successfully!";
			}
			

			/*
			if(fseek($handle, 260) === 0) {
				if(($handle_out = fread($handle, 10)) === false) {
					echo "There is nothing to be read.";
				} else {
					echo "This is what is in the position:#$handle_out#";
				}
			} else {
				echo "<br>Pointer could not find the spot in the file.";
			}
			*/
			
			//$written = fwrite($handle, "This is the new text.");

			/*
			$written = true;

			if($written === FALSE) {
				echo "<br>There has been an error!!!";
			} else {
				echo "<br>The new text has been added successfully.";
			}
			*/

			fclose($handle);

			/*
			if(mkdir("modules/neues", 0777)) {
				echo "<br>New folder made successfully";
			} else {
				echo "<br>New folder could not be made.";
			}
			*/



			$content = file_get_contents('modules/index_switch.txt');

			$content .= "This is the new text from the file_put_contents method.";

			file_put_contents('index_switch.txt', $content);

	?>