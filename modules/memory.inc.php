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
		$lang = "al";		// language variable (al = alemán)

		if(isset($_POST['numero']) && $_POST['numero'] !== 0) {

			//this exception has to go on the outside of the if
			try {
				if(!filter_var($_POST['numero'],  FILTER_VALIDATE_INT)) {
					throw new RuntimeException("{$_POST['numero']} is not a valid number. Try introducing a number again.");
				}

				$level = htmlspecialchars($_POST['level']);
				$actividad = htmlspecialchars($_POST['actividad']);
				$tema = htmlspecialchars($_POST['tema']);

				$table = "{$lang}_{$level}_{$actividad}_$tema";
				$directory = "modules/$lang/$level/$actividad/$tema";
				$img_directory = "$directory/img";

				$query = "CREATE TABLE $table (
				  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				  `item` varchar(255) NOT NULL DEFAULT '',
				  `item_l1` varchar(255) NOT NULL DEFAULT '',
				  `img` varchar(255) NOT NULL DEFAULT '',  
				  PRIMARY KEY (`id`)
				)";

			} catch (RuntimeException $e) {
				echo $e->getMessage() . "<br />";
			}

			try {

				if(!$mysqli->query($query)) {
					$msg = sprintf("Table creation failed. %d - %s<br />", $mysqli->errno, $mysqli->error);
					throw new RuntimeException($msg);
				}

				if(!is_dir($img_directory)) {
					// creates directories recursively in order to save the pages & pictures in them
					if(!mkdir($img_directory, 0777, true)) {							
						throw new RuntimeException("The directory could not be created.<br />");								
					}
				} else {
					throw new RuntimeException("The directory already exists. Please choose another name.<br />");
				}

			} catch (RuntimeException $e) {
				echo $e->getMessage() . "<br />";
			}


			// looping through all the rows included by the user
			for($i = 1; $i <= $_POST['numero']; $i++) {

				$item = "item{$i}";
				$item_l1 = "item{$i}_l1";
				$itemFile = "{$item}_file";

				$item = htmlspecialchars($_POST[$item]);
				$item_l1 = htmlspecialchars($_POST[$item_l1]);
			
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

			    if((false === $thumb = imagescale($source, 200))) {
			    	throw new RuntimeException("File could not be scaled correctly.");
			    }

			    if(imagejpeg($thumb, $new_file = sprintf("./$directory/img/%s.%s",	$item, $ext)) === false ) {
			    	throw new RuntimeException("The file could not be uploaded.");
			    }	    		

				} catch (RuntimeException $e) {

	    			echo $e->getMessage() . "<br />";

				}

				try {
					// writing the information into de DB
					if(isset($new_file) && $new_file != '') {
						$new_file = substr($new_file, 2);									
						$query = "INSERT INTO $table (item, item_l1, img) VALUES ('$item', '$item_l1', '$new_file')";
					} else {
						$query = "INSERT INTO $table (item, item_l1) VALUES ('$item', '$item_l1')";
					}
					if($mysqli->query($query) !== true) {
						throw new RuntimeException("New record $i could not be creted!!!");
					}
				} catch (RuntimeException $e) {
					echo $e->getMessage() . "<br />";
				}
			}

			try {
				// --- include the entry into the corresponding space in the index_switch.php file ---
				$needle = 'default';
				$file = 'index_switch.php';
				$topic = "Memoryspiel";

				//include all the necessary variables & POSTs into an array to create the addArray
				$addArray[] = $tema;
				$addArray[] = $lang;
				$addArray[] = $level;
				$addArray[] = $actividad;

				$addFile = new AddFile($file);
				//call the corresponding method to create the additional case entry
				$addFile->addArray($addArray, $topic);
				// divides the array into several parts depending on where the $needle is
				$addFile->splitArray($needle);
				// returns true if the array was merged correctly
				if(($fdbck = $addFile->mergeArrays($addArray)) === true) {
					$addFile->makeString();
					if(($fdbck = $addFile->writeFile()) !== true) {
						throw new RuntimeException($fdbck);
					}						
				} else {
					throw new RuntimeException($fdbck);
				}
				// --- END WRITE INTO THE index_switch.php file

				$included_file = "buildMemory.inc.php";
				$path = "./modules/$lang/$included_file";

				//code to be included into the new file
				$includeCode = "<?php\r\n";
				$includeCode .= "\t" . '$table = \'' . strtolower($table) . "';\r\n";
				$includeCode .= "\trequire_once('$path');\r\n";
				$includeCode .= "?>";
				// creating the new file
				$handle = fopen("$directory/$tema.inc.php", "w+");
				if(!fwrite($handle, $includeCode)) {
					throw new RuntimeException("Entry could not be included into the new file.");
				} 

				echo "<br />Deine Seite ist unter folgender Adresse zu erreichen:<br />";
				$msg = sprintf("<a href='%s/%s' target='_blank'>%s/%s</a>", BASE_URL, $tema, BASE_URL, $tema);
				echo $msg;

			} catch (RuntimeException $e) {
				echo $e->getMessage() . "<br />";
			}
						
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
									$stg = sprintf("<option value='%s'>%s</option>", $level, $row['levels']);
									echo $stg;
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
											$stg = sprintf("<option value='%s'>%s</option>", $row['activity_name'], $row['activity_name']);
											echo $stg;
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
						<input type="text" id="num_items" name="numero" class="form-control" placeholder="nº de items"required>
					</div>
					<div class="col-2">
						<input type="button" class="btn btn-primary" value="Crear rejilla" onclick="buildItems(document.getElementById('num_items').value);">
					</div>
				</div>

				<div class="row my-4">
					<div id="inc_text" class="col-12"></div>					
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
			
			let num = parseInt(item);
			let result = Number.isInteger(num);
			let not_number = isNaN(item);			

			if(result === false || not_number === true) {
				alert("You introduced \"" + item + "\". This is not a correct value. Please introduce a correct number to start the exercise!");				
			} else {
				item = Math.floor(item);
				alert("Se van a crear " + item + " items.");
			
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
		}
	</script>