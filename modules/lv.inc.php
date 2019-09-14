<?php
  require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');
	require_once('./php/randomizer.class.php');
	require_once('./php/min_Max.class.php');
	require_once('./php/builder.class.php');

	$_SESSION['done'] = ',';
	$_SESSION['time'] = time();

	//create the object to access the db
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");
?>

	<div class="row justify-content-center mt-5">
		<div class="justify-content-center mt-3 mb-3">
			<h2>Comprensión lectora</h2>
		</div>		
	</div>

	<div class="row">
		<div class="col-sm-6 scll">			
			<?php
				$table = "items";
				$ex_type = "tx";
				//OPEN UP WHEN FINISHED!!! ---			
				if(isset($_SESSION['levelID']) && $_SESSION['levelID'] != '') {
					$levelID = $_SESSION['levelID'];
				} else {					
					$levelID = "3";
				}


				$result = $mysqli->query("SELECT id,texto FROM $table WHERE levelID = '$levelID' AND pid = -1 AND tipo_ejercicio = '$ex_type'");
				if($row = $result->fetch_assoc()) {
					echo $row['texto'];
				} else {
					echo "There is not text for your level.";					
				}
			?>
		</div>

		<div class="col-sm-6 scll">
			<div id="all"></div>
			<form name="miForm" id="myForm" method="post">
				<?php
					if(!isset($row)) {
						echo "There are no questions on this side.";
					} else {
						$num_quest = 5;	//number of questions - and radio groups - in the exercise
						$options = 4;		// 1 to maximum 4 options available
						$quest = "referencia,pregunta,";	// building the mysql quest
						for($i=0; $i<$options; $i++) {
							$quest .= "resp$i,";
						}
						$quest .= "solucion,clave,gram";
						
						$min_max = new Min_Max($table);

						$min_max->setLevelID($levelID);
						$min_max->setMinMax($mysqli, $_SESSION['idioma'], $row['id']);
					 	$_SESSION['min_num'] = $min_max->getMinNumber();
					 	$_SESSION['max_num'] = $min_max->getMaxNumber();

						echo "<div id='ajax_response' class='left'>";
						
						// variable used to create a random item out of a list and increment the list with the item
						$randomize = new Randomizer($_SESSION['min_num'], $_SESSION['max_num']);
						
						for ($i=0; $i < $num_quest; $i++) {					
							$randomize->setList($_SESSION['done']);

							$k = $randomize->getRandomNumber();
							//saves the chosen number onto the session variable
							$_SESSION['done'] .= "$k,";
							//array to sort later on
							$arr[$i] = $k;

						}
						if (sort($arr) === true) {
							$build = new Builder();		//builds the radio buttons
							for ($i=0; $i < $num_quest; $i++) { 
								$result = $mysqli->query("SELECT $quest FROM $table WHERE id = {$arr[$i]}");
								$row = $result->fetch_array(MYSQLI_ASSOC);

								//copy the results in an array
								for($j = 0; $j < $options; $j++) {
									$opt[$j] = $row["resp$j"];
								}

								//randomize the order of the elements in the array
								shuffle($opt);

								$build->setId($i);
								$build->setQuestion("{$row['referencia']} - {$row['pregunta']}");
								$build->setOptions($opt);		//the method has to be changed to be more flexible
								$build->setSolution($row['solucion']);
								echo $ubung = $build->buildRadio() . "<br>";						
							}
						}
					echo "</div>";				
				?>

				<div id="btns" class="row justify-content-center mt-3 mb-3">
					<div class="col-4">				
						<input type="button" name="auswertung_btn" value="Auswertung" class="btn btn-primary" onclick="testenRadio()" />
					</div>
				</div>

			<?php
				} //finish "else" if there is no text for the level
			?>

			</form>
		</div>

	</div>
<script type="text/javascript" src="./js/answers.js"></script>	
<script>
	<?php
		if(isset($num_quest)) {
	?>
		var num_quest = "<?= $num_quest; ?>";
	<?php
		}
		if(isset($levelID)) {
	?>
		var levelID = "<?= $levelID; ?>";
	<?php		
		}
	?>
	var frm = document.forms["myForm"];

	//intantiates the answers object
	var ans = new Answers('LV');
</script>
<script type="text/javascript" src="./js/AJAX_Saving.js"></script>