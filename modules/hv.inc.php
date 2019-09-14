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
			<h2>Comprensión oral</h2>
		</div>		
	</div>

	<div class="row justify-content-center">
		<div id="audio-contain" class="col-sm-8 justify-content-center mt-3 mb-3">			
				<?php
					$table = "items";
					$ex_type = "hv";						
					if(isset($_SESSION['levelID']) && $_SESSION['levelID'] != '') {
						$levelID = $_SESSION['levelID'];
					} else {					
						$levelID = "3";
					}
					
					$result = $mysqli->query("SELECT id,audio FROM $table WHERE levelID = '$levelID' AND pid = -1 AND tipo_ejercicio = '$ex_type'");
					if($row = $result->fetch_assoc()) {
						$audio = $row['audio'];
						echo "<audio controls id='myAudio' onclick='cnt()'>";
						echo "<source src='$audio.mp3' type='audio/mpeg'>";
						echo "<source src='$audio.ogg' type='audio/ogg'>";
						echo "<source src='$audio.wav' type='audio/wav'>";
						echo "Your browser does not support the audio element.";
						echo "</audio>";						
					} else {
						echo "There is no audio for your level.";						
					}
				?>						
		</div>
	</div>

	<div class="row justify-content-center ">
		<div class="col-sm-8 scll">
			<div id="all"></div>
			<form name="miForm" id="myForm" method="post">
				<?php
					if(!isset($row)) {
						echo "There are no audio and no questions on this side.";
					} else {
						$num_quest = 5;		//number of questions - and radio groups - in the exercises
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
							
							$build = new Builder();

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
	//sets the variables
	var playing = 0;
	<?php
		if(isset($num_quest)) {		//=number of questions in the exercise
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
	var ans = new Answers('HV');

	var audio = document.getElementById("myAudio");
	audio.onended = function() {
		playing++;		
	};

	audio.onpause = function() {
		audio.play();
	}

	audio.onplay = function() {

		if(playing >= 2) {			
			audio.controls = false;
			audio.load();
			document.getElementById("audio-contain").innerHTML = "<strong>Lo sentimos, este audio solo lo puedes oír 2 veces.</strong>";
		}		
	};
</script>
<script type="text/javascript" src="./js/AJAX_Saving.js"></script>