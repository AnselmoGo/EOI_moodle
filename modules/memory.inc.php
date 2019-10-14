<?php
  require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');
	require_once('./php/addFile.class.php');

	//create the object to access the db
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");
?>
	<div class="row justify-content-center mt-5">
		<div class="justify-content-center mt-3">
			<h2>Construcción de ejercicio Memory</h2>
		</div>
		<div class="justify-content-center mt-3 col-10">
			En esta página podrás construir tu propio ejercicio de memory incluyendo imágenes que se relacionen con las palabras de eliges. También prodrás, si quieres, generar un ejercicio que relacione las palabras del idioma L1 con el idioma L2.
		</div>
	</div>

	<hr class="my-5">

	<?php
		if(isset($_POST['numero']) && $_POST['numero'] !== 0) {
			$lang = "al";		// language variable (al = alemán)			
			$table = "{$lang}_{$_POST['level']}_{$_POST['actividad']}_{$_POST['tema']}";

			$query = "CREATE TABLE IF NOT EXISTS `$table` (
			  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `item` varchar(255) NOT NULL DEFAULT '',
			  `item_l1` varchar(255) NOT NULL DEFAULT '',
			  `img` varchar(255) NOT NULL DEFAULT '',  
			  PRIMARY KEY (`id`)
			)";

			// create the new table related with the new info introduced by the user
			if(!$mysqli->query($query)) {
				echo "Table creation failed: " . $mysqli->errno . " - " . $mysqli->error;
			} else {
				echo "Table successfully created!!!";
			}

			if (!is_dir("modules/$lang/{$_POST['level']}/{$_POST['actividad']}/{$_POST['tema']}/img")) {
				// creates directories recursively in order to save the pages & pictures in them
				if(mkdir("modules/$lang/{$_POST['level']}/{$_POST['actividad']}/{$_POST['tema']}/img", 0777, true)) {
					echo "<br /><strong>Directory created successfully!!</strong><br />";
				}
			} else {
				echo "<br />Directory already exists.<br />";
			}


			// looping through all the rows included by the user
			for($i = 1; $i <= $_POST['numero']; $i++) {
				//$aux_num = $i + 1;
				$item = "item{$i}";
				$item_l1 = "item{$i}_l1";
				$itemFile = "{$item}_file";

				$item = $_POST[$item];
				$item_l1 = $_POST[$item_l1];
			
				//uploading the user images 
				try {
	   
	    		// Undefined | Multiple Files | $_FILES Corruption Attack
	    		// If this request falls under any of them, treat it invalid.
	    		if (
	        	!isset($_FILES[$itemFile]['error']) ||
	        	is_array($_FILES[$itemFile]['error'])
	    		) {
	        	throw new RuntimeException('Invalid parameters.');
	    		}

	    		// Check $_FILES[$itemFile]['error'] value.
	    		switch ($_FILES[$itemFile]['error']) {
	        	case UPLOAD_ERR_OK:
	            break;
	      		case UPLOAD_ERR_NO_FILE:
	            throw new RuntimeException('No file sent.');
	        	case UPLOAD_ERR_INI_SIZE:
	        	case UPLOAD_ERR_FORM_SIZE:
	            throw new RuntimeException('Exceeded filesize limit.');
	        	default:
	            throw new RuntimeException('Unknown errors.');
	    		}

			    // You should also check filesize here.
			    if ($_FILES[$itemFile]['size'] > 1000000) {
			        throw new RuntimeException('Exceeded filesize limit.');
			    }

			    // DO NOT TRUST $_FILES[$itemFile]['mime'] VALUE !!
			    // Check MIME Type by yourself.
			    $finfo = new finfo(FILEINFO_MIME_TYPE);
			    if (false === $ext = array_search(
			        $finfo->file($_FILES[$itemFile]['tmp_name']),
			        array(
			            'jpg' => 'image/jpeg',
			            'png' => 'image/png',
			            'gif' => 'image/gif',
			        ),
			        true
			    )) {
			        throw new RuntimeException('Invalid file format.');
			    }

			    //image dimensions
			    list($width, $height) = getimagesize($_FILES[$itemFile]['tmp_name']);
			    $newwidth = $width * 0.2;
			    $newheight = $height * 0.2;

			    // create the source
			    $thumb = imagecreatetruecolor($width, $height);
			    
			    switch ($ext) {
			    	case 'jpg':
			    		$source = imagecreatefromjpeg($_FILES[$itemFile]['tmp_name']);
			    		break;
			    	case 'png':
			    		$source = imagecreatefrompng($_FILES[$itemFile]['tmp_name']);
			    		break;
			    	default:
			    		$source = imagecreatefromgif($_FILES[$itemFile]['tmp_name']);
			    		break;
			    }

			    if($source === false) {
			    	throw new RuntimeException("<br />Source could not be created.");
			    }

			    if(!(false === $thumb = imagescale($source, 200))) {
			    	echo "<br/>Scaled correctly";
			    }

			    imagejpeg($thumb, $new_file = sprintf("./modules/$lang/{$_POST['level']}/{$_POST['actividad']}/{$_POST['tema']}/img/%s.%s",			            
			        		$item,
			            $ext
			        ));

	    		echo '<br />File is uploaded successfully.';

				} catch (RuntimeException $e) {

	    		echo $e->getMessage();

				}

				// writing the information into de DB
				if(isset($new_file) && $new_file != '') {
					$new_file = substr($new_file, 2);
								
					$query = "INSERT INTO $table (item, item_l1, img) VALUES ('$item', '$item_l1', '$new_file')";
				} else {
					$query = "INSERT INTO $table (item, item_l1) VALUES ('$item', '$item_l1')";
				}

				if($mysqli->query($query) === TRUE) {
					echo "<br />New record $i created successfully.<br />";
				} else {
					echo "<br />New record $i could not be created!!!<br />";
				}
			}

			// --- include the entry into the corresponding space in the index_switch.php file ---
			$needle = 'default';
			$file = 'index_switch.php';

			//include all the necessary variables & POSTs into an array to create the addArray
			$addArray[] = $_POST['tema'];
			$addArray[] = $lang;
			$addArray[] = $_POST['level'];
			$addArray[] = $_POST['actividad'];

			//get the topic of the activity
			$topic = "Memoryspiel";


			//create the array that is going to be included into the $file
/*
			$addArray[] = "\tcase '" . $_POST['tema'] . "':";
			$addArray[] = "\t\t" . '$page = "' . $lang . '/' . $_POST['level'] . '/' . $_POST['actividad'] . '/' . $_POST['tema'] . '/' . $_POST['tema'] . '.inc.php";';
			$addArray[] = "\t\t" . '$page_title = "$head_title - Memoryspiel - ' . ucfirst($_POST['tema']) . '";';
			$addArray[] = "\t\t" . 'break;';	
*/			

			$addFile = new AddFile($file);
			//call the corresponding method to create the additional case entry
			$addFile->addArray($addArray, $topic);
			// divides the array into several parts depending on where the $needle is
			$addFile->splitArray($needle);
			// returns true if the array was merged correctly
			//if(($fdbck = $addFile->mergeArrays($addArray)) === true) {
			if(($fdbck = $addFile->mergeArrays()) === true) {
				$addFile->makeString();
				$addFile->writeFile();
				echo "<br />New CASE successfully included!!!<br />";
			} else {
				echo $fdbck;
			}
			// --- END WRITE INTO THE index_switch.php file

			// write the file including the code to build the new page (create new file and fill with info)
			$path = "./modules/$lang/buildMemory.inc.php";

			$includeString = "<?php\r\n";
			$includeString .= "\t" . '$table = \'' . strtolower($table) . "';\r\n";
			//$includeString .= "\t" . '$secondURL = ' . "'/$lang/{$_POST['level']}/{$_POST['actividad']}/{$_POST['tema']}';\r\n";
			$includeString .= "\trequire_once('$path');\r\n";
			$includeString .= "?>";

			$handle = fopen("modules/$lang/{$_POST['level']}/{$_POST['actividad']}/{$_POST['tema']}/{$_POST['tema']}.inc.php", "w+");
			if(!fwrite($handle, $includeString)) {
				echo "<br />Entry could not be included into the new file.<br />";
			} else {
				echo "<br />The new entry has been included successfully!!!<br />";
			}

			echo "<br />Deine Seite ist unter folgender Adresse zu erreichen:<br />";
			echo "<a href='" . BASE_URL . "/" . $_POST['tema'] . "' target='_blank'>". BASE_URL . "/" . $_POST['tema'] . "</a><br />";
			
		} else {		
	?>

	<div class="row justify-content-center mt-3">
		<div class="col-10">
			<form name="form" id="myForm" method="post" action="<?php BASE_URL . $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
				<div class="row"> 
					<div class="col-md-2 col-4">
						Nivel:
					</div>
					<div class="col-md-4 col-8">
						<select name="level" id="level" class="custom-select" required>
							<option value="">elige el nivel</option>
							<?php
								$table = "level";

								$result = $mysqli->query("SELECT levels FROM $table");
								while($row = $result->fetch_assoc()) {
									$level = str_replace(".", "", $row['levels']);
									//echo "<option value='" . $row['levels'] . "'>{$row['levels']}</option>";
									echo "<option value='" . $level . "'>{$row['levels']}</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row my-2">
					<div class="col-md-6">
						<div class="row">
							<div class="col-4">
								Actividad:
							</div>				
							<div class="col-8">
								<select name="actividad" id="actividad" class="custom-select" required>
									<option value="">elige el tipo de actividad</option>
									<?php									
										$table = "activity";

										$result = $mysqli->query("SELECT activity_name FROM $table");
										while($row = $result->fetch_assoc()) {
											echo "<option value='" . $row['activity_name'] . "'>{$row['activity_name']}</option>";
										}										
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<div class="col-4">
								Tema:
							</div>				
							<div class="col-8">
								<input type="text" name="tema" class="form-control" id="usuario" placeholder="Indica el tema" required>
							</div>
						</div>			
					</div>
				</div>

				<hr>

				<div class="row my-5">
					<div class="col-5">
						Decide cuántos items vas a cargar para jugar el juego:						
					</div>
					<div class="col-2">
						<input type="text" name="numero" class="form-control" placeholder="nº de items" onblur="buildItems(this.value)" required>
					</div>
					<div class="col-2">
						<input type="button" class="btn btn-primary" value="Crear rejilla">
					</div>
				</div>

				<div class="row my-4">
					<div id="inc_text" class="col-10"></div>					
				</div>
				<div class="row my-2">
					<div id="include" class="col-10"></div>
				</div>
				<div id="end_button" class="row justify-content-center my-4">					
				</div>
			</form>
		</div>
	</div>
	<?php
		}
	?>
	<script>
		function buildItems(item) {
			alert("Seguro que quieres crear " + item + " items?");
			
			var txt = "";
			var txt2 = "Rellena los siguientes campos. Para cada palabra podrás adjuntar una imagen que elijas. + INSTRUCCIONES!!";

			for(i = 0; i < item; i++) {
				aux = i + 1;
				name1 = "item" + aux;
				name2 = name1 + "_l1";
				name3 = name1 + "_file";

				txt += "<div class='row my-1'>";
				txt += "<div class='col-3'>";
				txt += "<input type='text' name='" + name1 + "' class='form-control' id='usuario' placeholder='item " + aux + "'>";
				txt += "</div>";
				txt += "<div class='col-3'>";
				txt += "<input type='text' name='" + name2 + "' class='form-control' id='usuario' placeholder='traduccion'>";
				txt += "</div>";
				txt += "<div class='col-6'>";
				txt += "<input type='file' name='" + name3 + "' class='form-control' id='usuario' placeholder='imagen de item " + aux + "'>";
				txt += "</div>";
				txt += "</div>";

			}
			document.getElementById("include").innerHTML = txt;
			document.getElementById("inc_text").innerHTML = txt2;

			btn = "<button type='submit' class='btn btn-primary' name='build_memory'>Seite erstellen</button>";
			document.getElementById("end_button").innerHTML = btn;
		}
	</script>