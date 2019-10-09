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
									if(isset($_POST['level']) && $_POST['level'] == $level) {
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
								<input type="text" name="tema" class="form-control" id="usuario" placeholder="Indica el tema" value="<?php if(isset($_POST['tema'])) echo $_POST['tema']; ?>" required>
							</div>
						</div>			
					</div>
				</div>
			</div>
		</div>

		<hr>
		
			<?php
				if(isset($_POST['createlv']) && $_POST['createlv'] !== null) {
					$lang = "al";
					$table = "{$lang}_{$_POST['level']}_{$_POST['tema']}";

					
					$query = "CREATE TABLE IF NOT EXISTS $table (
					  `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT,					  
					  `gap` int(3) NOT NULL,
					  `exercise` text NOT NULL DEFAULT '',
					  `solution` varchar(65) NOT NULL DEFAULT '',
					  `imgID` int(3) NOT NULL,
					  `img` varchar(255) NOT NULL DEFAULT '',
					  PRIMARY KEY (`id`)
					)";
					

					// create the new table related with the new info introduced by the user
					if(!$mysqli->query($query)) {
						echo "Table creation failed: " . $mysqli->errno . " - " . $mysqli->error;
					} else {
						echo "Table successfully created!!!";
					}
					

					if (!is_dir("modules/$lang/{$_POST['level']}/{$_POST['tema']}/img")) {
						// creates directories recursively in order to save the pages & pictures in them
						if(mkdir("modules/$lang/{$_POST['level']}/{$_POST['tema']}/img", 0777, true)) {
							echo "<br /><strong>Directory created successfully!!</strong><br />";
						}
					} else {
						echo "<br />Directory already exists.<br />";
					}							

					//str_replace("<b>", "<b>", $subject, $cnt);
					str_replace("<b>", "<b>", $_POST['editordata'], $cnt);
					$subject = $_POST['editordata'];

					for($i = 0; $i < $cnt; $i++) {
						$returns[$i] = strstr($subject, "<b>");
						$returns[$i] = str_replace("<b>", "", $returns[$i]);
						$returns[$i] = strstr($returns[$i], "</b>", true); 

						$subject = preg_replace("(<b>[A-z ]*</b>)", "-$i- ", $subject, 1);
					}	

					if(isset($_POST['distractores']) && $_POST['distractores'] > 0) {
						for($i = 0; $i < $_POST['distractores']; $i++) {
							$distract[] = $_POST["distractor$i"];
						}
					}					
					
					// !!!--- before going on we have to replace the img and save the pictures ---!!!							

					if ($_POST['editordata'] != null) {						
						echo $subject . "<br /><br />";
						if(isset($returns)) {
							print_r($returns);
						}
						echo "<br/><br/>";
					} else {
						echo "We have failed to do the correct thing.";
					}
				
					$query = "INSERT INTO $table (gap, exercise) VALUES (-1, '$subject')";
					if($mysqli->query($query) !== TRUE) {
						echo "There has been a problem!";
					}
					for($i = 0; $i < $cnt; $i++) {
						$query = "INSERT INTO $table (gap, solution) VALUES ($i, '{$returns[$i]}')";
						if($mysqli->query($query) !== TRUE) {
							echo "There has been a problem!";
						}
					}
					foreach ($distract as $value) {
						$query = "INSERT INTO $table (gap, solution) VALUES (-2, '{$value}')";
						if($mysqli->query($query) !== true) {
							echo "There has been a problem!";
						}
					}
				}


				if(!isset($_POST["createlv"])) {
					echo "<textarea id='summernote'name='editordata'>";
				}

				if(isset($_POST["build_memory"])) {
					$data = file_get_contents($_FILES["userFile"]["tmp_name"]);
					echo htmlspecialchars($data);									
				}

				if(!isset($_POST["createlv"])) {
					echo "</textarea>";
				}
				
				unset($_POST["createlv"]);

				if(isset($_POST["build_memory"])) {
					echo "<div id='include'>";
					echo "<div class='row mt-3'>";
						echo "<div class='col-md-5 offset-md-1 col-12'>";
							echo "Incluye el número de distractores que quieres añadir: ";
						echo "</div>";
						echo "<div class='col-md-3 col-6'>";													
							echo "<input type='text' name='distractor' class='form-control' id='distract' placeholder='número de distractores'>";
						echo "</div>";
						echo "<div class='col-md-3 col-6'>";
							echo "<input type='button' class='btn btn-primary' name='distractor' value='Incluir distractores' onclick='distractores();'></input>";
						echo "</div>";											
					echo "</div>";
					echo "</div>";

					unset($_POST["build_memory"]);
				}

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
			
			alert("Se van a incluir " + document.getElementById("distract").value + " distractores.");
			count = document.getElementById("distract").value;

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