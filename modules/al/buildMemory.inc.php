<?php 
	require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');	
	require_once('./php/uniqueRandom.class.php');
	require_once('./php/buildDivs.class.php');
?>
	<div class="row justify-content-center mt-5">
	<!-- <div class="container">  --->
		<div class="row">
			<div class="col-sm-12">
				<h1>Memory Spiel</h1>
			</div>
		</div>

		<div class="row justify-content-center tbs res_large">
			<div class="col-sm-10 col-sm-offset-1">
			<img class="img-responsive" src="/EOI_moodle/img/mapa.jpg" alt="Mapa Europa"><br />
			</div>
			<div class="col-sm-10 col-sm-offset-1">
				Im folgenden Teil, hast du ein Memoryspiel, indem du zwei gleiche Teile finden musst.
				<hr>
			</div>			
		</div>
		
		<div class="row tbs">
			<div class="col-sm-12">
				<div class="big green" id='all'></div>
				<br />
				<form name="form" class="my_form" id="myForm" method="post" action="<?=$_SERVER['REQUEST_URI']?>">					
					<?php						
						//wird benutzt, um die Anzahl der Memory-Paare festzulegen
						$count = 10;						

						//create the object to access the db
						$config = config_db_access::getInstance();
						$mysqli = $config->getConnection();
						$mysqli->set_charset("utf8");					

						//get the max id from the table
						$result = $mysqli->query("SELECT MAX(id) FROM $table");
						$row = $result->fetch_array(MYSQLI_ASSOC);
						$max_id = $row['MAX(id)'];

						//if we don't have enough items in the DB!!!
						if($max_id < $count) {
							$count = $max_id;
						}
												
						$jobs = array();					
						$construct = array();

						//get a unique number from the db to build the memory game
						$randNum = new UniqueRandom($max_id);
						for($i = 0; $i < $count; $i++) {							
							$num = $randNum->getUniqueId();

							//get the string and the corresponding image to build the memory game
							$result = $mysqli->query("SELECT item,img FROM $table WHERE id = $num");
							$row = $result->fetch_array(MYSQLI_ASSOC);
							$jobs[(2*$i)] = $row['item'];
							$jobs[(2*$i+1)] = "./" . $row['img'];
						}

						shuffle($jobs);

						//build the divs to place into the grid						
						$needle = array('.jpg', '.png', '.gif');

						$buildDiv = new BuildDivs($needle);
						foreach ($jobs as $value) {							
							$buildDiv->setValue($value);
							//checks if $value is an image or a string							
							$buildDiv->checkNeedle();							
							//constructs the Div depending on the result from checkNeedle()
							$construct[] = $buildDiv->buildDiv();							
						}

						//construct the grid container
						echo '<div class="row">';
						echo '<div class="col-sm-10 col-sm-offset-2">';
						echo "<div class='grid_frame'>";
						echo "<div id='grid_container'>";
						
						foreach ($construct as $value) {							
							echo " $value ";
						}
						
						echo '</div>';
						echo "</div>";
						echo '</div>';
						echo '</div>';

					?>
						
						<div class="tbs my-3">
							<div class="col-sm-12 justify-content-center ">
								<input type="submit" name="wiederholen" value="Übung wiederholen" class="btn btn-primary" />
							</div>							
						</div>
					
				</form>
			</div>
		</div>
	</div>

	<?php
		$js_paare = json_encode($count);
		echo "<script>var paare = $js_paare</script>";
	?>

	<script>
		var clck = 0;
		var bild1 = 0, bild2 = 0;
		var identifica = "";
		var parejas = 0;			//indica las parejas encontradas


		function compara(id1, id2) {
			if(id1 !== id2) {
				if(bild1 == 1) {
					id1 = "x" + id1;
				}
				if (bild2 == 1) {
					id2 = "x" + id2;
				}
				document.getElementById(id1).style.transition = "transform 0.8s";
				document.getElementById(id1).style.transform = "rotateY(180deg)";
				document.getElementById(id2).style.transition = "transform 0.8s";
        document.getElementById(id2).style.transform = "rotateY(180deg)";
			} else {
				parejas++;
			}
     	bild1 = 0;
      bild2 = 0;
      id1 = "";
      identifica = "";
      clck = 0;

      if(parejas == paare) {
      	document.getElementById('all').innerHTML = "Herzlichen Glückwunsch. Du hast alle Paare gefunden.";
      }
		}

		function visualiza(obj) {			
			clck++;
			//check what type of "card" we have clicked
			if(clck < 3) {		//prevents opening > 2 items at a time
				if(obj.id.charAt(0) == 'x') {
					id1 = obj.id.substring(1);
					if(clck < 2) {
						bild1 = 1;
					} else {
						bild2 = 1;
					}				
				} else {
					id1 = obj.id;
				}
				if(clck < 2){       
	       	obj.style.transition = "transform 0.8s";
	        obj.style.transform = "rotateY(90deg)";
	        identifica = id1;
	      }

	      if (clck == 2) {
	      	obj.style.transition = "transform 0.8s";
	        obj.style.transform = "rotateY(90deg)";
	      	setTimeout("compara(identifica, id1)", 1500);
	      }
	    }
		}
	</script>