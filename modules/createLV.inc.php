<?php
  require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');
	require_once('./php/addFile.class.php');
	require_once('./php/htmlpurifier/library/HTMLPurifier.auto.php');

	//create the object to access the db
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");

	$config_pure = HTMLPurifier_Config::createDefault();
?>
	<div class="row justify-content-center mt-5">
		<div class="center mt-3 col-8">
			<h1>Erstelle eine Leseverstehensseite</h1>
		</div>
		<div class="center mt-3 mb-5 col-10">
			In dieser Seite hast du die Möglichkeit, einen Text zu schreiben oder ihn als .txt Datei hochzuladen, um aus ihm dann eine Leseverstehensübung zu erstellen. Viel Spaß bei der Arbeit.			
		</div>
	</div>

	<hr>

	<form name="form" id="myForm" method="post" action="<?php BASE_URL . $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
		
		<div class="row justify-content-center ">
			<div class="col-10">						
				<div class="row"> 
					<div class="col-md-1 col-2">
						Nivel:
					</div>
					<div class="col-md-4 col-8">
						<select name="level" id="level" class="custom-select" required>
							
							<?php
								echo "<option value=''>elige el nivel</option>";
							
								$table = "level";

								$result = $mysqli->query("SELECT levels FROM $table");
								while($row = $result->fetch_assoc()) {
									$level = str_replace(".", "", $row['levels']);									
									echo "<option value='" . $level . "' "; 
									if(isset($_POST['level']) && !isset($_POST['createlv']) && $_POST['level'] == $level) {
										echo "selected";
									}
									echo ">{$row['levels']}</option>";									
								}
							?>
						</select>
					</div>
					<div class="offset-1 col-md-6 col-12">
						<div class="row">
							<div class="col-2">
								Tema:
							</div>				
							<div class="col-8">
								<input type="text" name="tema" class="form-control" id="usuario" placeholder="Indica el tema" value="<?php if(isset($_POST['tema']) && !isset($_POST['createlv'])) echo $_POST['tema']; ?>" required>
							</div>
						</div>			
					</div>
				</div>
			</div>
		</div>

		<hr>
		
		<?php
			$lang = "al";

			if(isset($_POST['createlv']) && $_POST['createlv'] !== null) {
				$level = htmlspecialchars($_POST['level']);
				$tema = htmlspecialchars($_POST['tema']);

				$table = "{$lang}_{$level}_$tema";
				$directory = "modules/$lang/$level/$tema";
				$img_directory = "$directory/img";
				
				$query = "CREATE TABLE $table (
				  `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT,					  
				  `gap` int(3) NOT NULL,
				  `exercise` text NOT NULL DEFAULT '',
				  `solution` varchar(65) NOT NULL DEFAULT '',
				  `imgID` int(3) NOT NULL,
				  `img` varchar(255) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`)
				)";
									
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
					echo $e->getMessage();					
				}
				
				try {
					// this is to get the number of items that will be substituted
					if(isset($_POST['editordata'])) {
						str_replace("<u>", "<u>", $_POST['editordata'], $cnt);
						$purifier = new HTMLPurifier($config_pure);
						$subject = $purifier->purify($_POST['editordata']);
						//$subject = $_POST['editordata'];
					} else {
						throw new RuntimeException("You haven't included any text.");
					}
						

					if($cnt <= 0) {
						throw new RuntimeException("You haven't selected any items for the exercise.");						
					}

					for($i = 0; $i < $cnt; $i++) {
						$returns[$i] = strstr($subject, "<u>");
						$returns[$i] = str_replace("<u>", "", $returns[$i]);
						$returns[$i] = strstr($returns[$i], "</u>", true); 

						$subject = preg_replace("(<u>[A-z ]*</u>)", "-$i- ", $subject, 1);
					}

					if(isset($_POST['distractores'])) {
						if($_POST['distractores'] > 0) {
							for($i = 0; $i < $_POST['distractores']; $i++) {
								$distract[] = htmlspecialchars($_POST["distractor$i"]);
							}
						} else {
							throw new RuntimeException("You haven't chosen any distractors.");
						}	
					}

				} catch (RuntimeException $e) {
					echo $e->getMessage() . "<br />";
				}
											
				// !!!--- before going on we have to replace the img and save the pictures ---!!!							
				try {
					//safe the text into the db
					$subject = mysqli_escape_string($mysqli, $subject);
					$query = "INSERT INTO $table (gap, exercise) VALUES (-1, '$subject')";
					if($mysqli->query($query) !== TRUE) {
						throw new RuntimeException("Text could not be safed.");
					}

					//safe the items into the db
					for($i = 0; $i < $cnt; $i++) {
						$return = mysqli_escape_string($mysqli, htmlspecialchars($returns[$i]));
						$query = "INSERT INTO $table (gap, solution) VALUES ($i, '$return')";
						if($mysqli->query($query) !== TRUE) {
							throw new RuntimeException("Some items could not be safed.");
						}
					}

					if(!isset($distract)) {
						throw new RuntimeException("There are no distractors in this exercise.");
					}

					foreach ($distract as $value) {
						$value = mysqli_escape_string($mysqli, htmlspecialchars($value));
						$query = "INSERT INTO $table (gap, solution) VALUES (-2, '{$value}')";
						if($mysqli->query($query) !== true) {
							throw new RuntimeException("Some distractors could not be safed.");
						}
					}

				} catch (RuntimeException $e) {
					echo $e->getMessage() . "<br />";
				}
			
				
				try {
					//include the entry into the corresponding space in the index_switch.php file
					$needle = 'default';
					$file = 'index_switch.php';
					$topic = "Leseverstehensübung";

					$addArray[] = $tema;
					$addArray[] = $lang;
					$addArray[] = $level;

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
					// --- END write into index_switch.php file ---

					$included_file = "buildLV.inc.php";
					$path = "./modules/$lang/$included_file";

					// code to include into the new file
					$includeCode = "<?php\r\n";
					$includeCode .= "\t" . '$table = \'' . strtolower($table) . "';\r\n";
					$includeCode .= "\t" . '$distract = \'' . $_POST['distractores'] . "';\r\n";
					$includeCode .= "\trequire_once('$path');\r\n";
					$includeCode .= "?>";

					// creating the new file
					$handle = fopen("$directory/{$tema}.inc.php", "w+");
					if(!fwrite($handle, $includeCode)) {
						throw new RuntimeException("Entry could not be included into the new file.");
					}

					// show link to the new page
					echo "<br />Deine Seite ist unter folgender Adresse zu erreichen: ";
					$msg = sprintf("<a href='%s/%s' target='_blank'>%s/%s</a>", BASE_URL, $tema, BASE_URL, $tema);
					echo $msg;

				} catch (RuntimeException $e) {
					echo $e->getMessage() . "<br />";
				}

			}

			if(!isset($_POST["createlv"])) {
				echo "<textarea id='summernote' name='editordata'>";
			}

			if(isset($_POST["build_memory"])) {
				$data = file_get_contents($_FILES["userFile"]["tmp_name"]);
				echo htmlspecialchars($data);									
			}

			if(!isset($_POST["createlv"])) {
				echo "</textarea>";
			}								

			if(isset($_POST["build_memory"])) {
				$txt = "<div id='include'>";
				$txt .= "<div class='row mt-3'>";
				$txt .= "<div class='col-md-5 offset-md-1 col-12'>";
				$txt .= "Incluye el número de distractores que quieres añadir: ";
				$txt .= "</div>";
				$txt .= "<div class='col-md-3 col-6'>";
				$txt .= "<input type='text' name='distractor' class='form-control' id='distract' placeholder='número de distractores'>";
				$txt .= "</div>";
				$txt .= "<div class='col-md-3 col-6'>";
				$txt .= "<input type='button' class='btn btn-primary' name='distractor' value='Incluir distractores' onclick='distractores();'></input>";
				$txt .= "</div></div></div>";

				echo $txt;
				unset($_POST["build_memory"]);
			}

			unset($_POST["createlv"]);

		?>
		
		<div class="row mt-3">
			<div class="col-3 offset-1">Elige un archivo que quieras cargar: </div>
			<div class="col-6"><input id="userFile" class="form-control" type="file" name="userFile"></div>
		</div>
		<div class="row justify-content-center">
			<div class="col-2 offset-4">
				<button type='submit' class='btn btn-primary my-3' name='build_memory'>Text laden</button>
			</div>
			<div class="col-6">
				<button type='submit' class='btn btn-primary my-3' name='createlv'>Seite erstellen</button>
			</div>			
		</div>
		
	</form>

	<script>
		$(document).ready(function() {
		  $('#summernote').summernote();
		});

		$('#summernote').summernote({
		  toolbar: [
		    // [groupName, [list of button]]
		    ['style', ['style']],
			  ['font', ['bold', 'italic', 'underline', 'clear']],
			  ['view', ['undo', 'redo']],
			  ['fontname', ['fontname']],
			  ['color', ['color']],
			  ['para', ['ul', 'ol', 'paragraph']],
			  ['table', ['table']],
			  ['insert', ['hr', 'link', 'picture', 'video']],
			  ['view', ['fullscreen', 'codeview', 'help']],
		  ]
		});

		function distractores() {
			var count = document.getElementById("distract").value;
			var txt = "<input type='hidden' name='distractores' value='" + count + "'>";

			for(i=0; i < count; i++) {
				txt += "<div class='row my-1'>";
				txt += "<div class='col-3 offset-1'>";
				txt += "<input type='text' name='distractor" + i + "' class='form-control' id='usuario' placeholder='distractor " + (i+1) + "'>";
				txt += "</div>";
				txt += "</div>";
			}

			document.getElementById("include").innerHTML = txt;
		}

		function enableEditMode() {
			richTextField.document.designMode = 'On';
		}

		function execCmd(command, arg = null) {
			richTextField.document.execCommand(command, false, arg);
		}	
	</script>