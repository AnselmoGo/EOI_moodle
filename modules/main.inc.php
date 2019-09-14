<?php
  require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');

	//create the object to access the db
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");

	$avisa = "";
	$login = "";
	$table = "access";
	$no_access = 0;

	//check the login
	if(isset($_POST['login']) && $_POST['login'] !== "") {
		$login = trim($_POST['login']);
		$result = $mysqli->query("SELECT name FROM $table WHERE login='$login'");			
		if($result->num_rows > 0) {
			if(isset($_POST['pass']) && $_POST['pass'] !== "") {
				$pass = hash("sha512", trim($_POST['pass']));
				$result = $mysqli->query("SELECT name,acces FROM $table WHERE (login='$login' AND pass='$pass' AND result = 0)");
				if($result->num_rows == 0) {						
					$avisa .= "Tu contraseña no coincide con el usuario.";
				} else {
					$row = $result->fetch_assoc();
					if($row['acces'] == 1) {
						$_SESSION['name'] = $row['name'];
						$_SESSION['fecha'] = date("d/m/Y");
            $_SESSION['hora'] = date("H:i:s");
            $date = date('Y-m-d H:i:s');
            $quest = "UPDATE $table SET fecha_acceso='$date', acces=0 WHERE login='$login' AND pass='$pass'";
            if($mysqli->query($quest) !== TRUE) {
            	$avisa = "Ha habido un problema con tus datos.";
            }
            //$_SESSION['num_exer'] = 0; //indicates number of exercises done; we are doing this again
					} else {						
						$no_access = 1;
					}				
				}						
			}
		} else {
			$avisa = "Tu usuario no nos consta. Intenta introducirlo de nuevo.";
			$login = '';
		}
	}
?>
	<div class="row justify-content-center mt-5">
		
		<?php
			// takes the instructions from the correct $file row
			if(isset($_SESSION['name']) && ($_SESSION['name'] !== "")) {
				$file = basename($page, ".inc.php");
				//action element for the form
				$action = BASE_URL . "/prueba";
			} else {
				$file = basename($page, ".inc.php");
				//$file = "access";
				//action element for the form
				$action = $_SERVER['PHP_SELF'];
			}			

			$result = $mysqli->query("SELECT * FROM instruction WHERE page = '$file'");
			$row = $result->fetch_array(MYSQLI_ASSOC);
		?>

		<div class="col-md-8 col-12 mx-3 mt-2">
			<h1><?= $row['title']?></h1>
			<p>
				<?= $row['text1']?>
			</p>
			<p>
				<?= $row['text2']?>
			</p>
		</div>
	</div>

	<form name="form" id="myForm" method="post" action="<?=$action?>">
		<div class="row justify-content-center my-2">
			<div class="col-md-4 col-12">
				<?php
					if(isset($_SESSION['name']) && ($_SESSION['name'] !== "")) {
						$button = "Seguir";
						$_SESSION['start'] = 1;
				?>
					<p>
						<div id="inc" class="text-danger mb-3"></div>
						<div class="row align-items-center">
							<div class="col-md-4 col-12">
								Idioma:
							</div>
						 	<div class="col-md-8 col-12">
						 		<?php						 			
									$result = $mysqli->query("SELECT lang FROM language");
								?>
								<select name="idioma" id="idioma" class="custom-select">
									<option value="elige" selected>elige idioma</option>

									<?php
										while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
											echo "<option value='" . $row['lang'] . "'>{$row['lang']}</option>";
										}										
									?>
								</select>
						 	</div>
						</div>
					</p>

					<p>
						<div class="row align-items-center">
							<div class="col-md-4 col-12">
								Nivel:
							</div>
							<div class="col-md-8 col-12">
								<?php
									$result = $mysqli->query("SELECT id, levels FROM level");
								?>
									<select name="nivel" id="nivel" class="custom-select">
										<option value="-1" selected>elige nivel</option>
										<?php
											while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
												echo "<option value='" . $row['id'] . "'>{$row['levels']}</option>";
											}											
										?>
									</select>
							</div>
						</div>					
					</p>
				<?php
					} elseif ($no_access == 0) {
						$button = "Acceso";
				?>					
						<div class="row align-items-center form-group">							
							<div class="col-12 mb-3 text-danger"><strong><?=$avisa ?></strong></div>
						</div>
						<div class="row align-items-center form-group">
							<label for="usuario" class="col-md-4 col-12">
								Usuario:
							</label>
						 	<div class="col-md-8 col-12">						 		
								<input type="text" name="login" class="form-control" id="usuario" placeholder="Login" value="<?=$login ?>" required>
						 	</div>
						</div>
					
						<div class="row align-items-center form-group">
							<label for="pass" class="col-md-4 col-12">
								Contraseña:
							</label>
							<div class="col-md-8 col-12">								
								<input type="password" name="pass" class="form-control" id="pass" placeholder="Contraseña" required>
							</div>
						</div>					
					
				<?php				
					}	else {
				?>
					<div class="row align-items-center form-group">						
						<div class="col-12 mb-3 text-danger">
							<strong>Lo sentimos, ya has accedido a la prueba de clasificación. No puedes acceder una segunda vez.</strong>
						</div>
					</div>
				<?php
					}				
				?>

			</div>
		</div>

		<?php
			if($no_access == 0) {
		?>

			<div class="row justify-content-center my-2 form-group">
				<div class="col-4 mb-5">
					<button type="submit" class="btn btn-primary btn-block" onclick="check(event);"><?= $button ?></button>
				</div>
			</div>

		<?php
			}		
		?>

	</form>
	<script type="text/javascript">
		function check(event) {
			if(typeof idioma != 'undefined' && document.getElementById("idioma").value == "elige") {
				document.getElementById("inc").innerHTML = "Debes elegir un idioma de la selección";
				event.preventDefault();
			}
		}			
	</script>