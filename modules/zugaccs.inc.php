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

	//check the login
	if(isset($_POST['login']) && $_POST['login'] !== "") {		
		$login = trim($_POST['login']);
		$result = $mysqli->query("SELECT name FROM $table WHERE login='$login'");			
		if($result->num_rows > 0) {				
			if(isset($_POST['pass']) && $_POST['pass'] !== "") {
				$pass = hash("sha512", trim($_POST['pass']));
				$result = $mysqli->query("SELECT name,acces FROM $table WHERE (login='$login' AND pass='$pass' AND result = 1)");
				if($result->num_rows == 0) {						
					$avisa .= "Tu contraseña no coincide con el usuario.";
				} else {
					$row = $result->fetch_assoc();					
					$_SESSION['name'] = $row['name'];
					$_SESSION['fecha'] = date("d/m/Y");
          $_SESSION['hora'] = date("H:i:s");            
          //$_SESSION['num_exer'] = 0; //indicates number of exercises done								
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
			$action = BASE_URL . "/mostrar";
		} else {
			$file = $p;
			$action = BASE_URL . "/acceso";
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
<!--
	<div class="row justify-content-center">
		<div id="brd" class="col-sm-10 justify-content-center mt-2 mb-2">
			<p>Elige los campos que quieras que figuren en la tabla de resultados.</p>
			<div class="row">
//-->				
			<?php
				$table = "resultados";
				if(isset($_SESSION['name']) && $_SESSION['name'] != '') {
					$res = $mysqli->query("SHOW COLUMNS FROM $table");
					$field_aux = 0;

					if($res->num_rows > 0) {
						echo "<div class='row justify-content-center'>";
						echo "<div id='brd' class='col-sm-10 justify-content-center mt-2 mb-2'>";						
						echo "<div class='row my-2'>";
						echo "<div class='col-9'>Elige los campos que quieras que figuren en la tabla de resultados.</div>";
						echo "<div class=''>";						
						echo "<input type='checkbox' name='sample' class='eligetodo'>";
						echo "</div>";
						echo "<div class='col-2'>";
						echo "elegir todo</div>";					
						echo "</div>";
						echo "<div class='row campos'>";
					}

					while($row = $res->fetch_assoc()) {
						if($row['Field'] != "id" && $row['Field'] != "level" && $row['Field'] != "num_items") {
							echo "<div class='col-2'>";
							echo "<input type='checkbox' name='field$field_aux' value='" . $row['Field']. "'>";
							echo " {$row['Field']} ";
							echo "</div>";
							$field_aux++;
						}					
					}

					$res = $mysqli->query("SHOW COLUMNS FROM level");
					while($row = $res->fetch_assoc()) {
						if($row['Field'] != "id") {
							echo "<div class='col-2'>";
							echo "<input type='checkbox' name='field$field_aux' value='" . $row['Field']. "'>";
							echo " {$row['Field']} ";
							echo "</div>";
							$field_aux++;
						}
					}
					$_SESSION['fieldNum'] = $field_aux;

					if($res->num_rows > 0) {
						echo "</div></div></div>";
					}
				}
			?>

<!--			
			</div>
		</div>
	</div>
//-->

	<div class="row justify-content-center my-4">
		<div class="col-md-4 col-12">
			<?php
				if(isset($_SESSION['name']) && ($_SESSION['name'] !== "")) {
					$button = "Seguir";
					$_SESSION['start'] = 1;
					$table = "resultados";
					$aux = 0;

					$result = $mysqli->query("SELECT DISTINCT name FROM $table");					

					while ($row = $result->fetch_assoc()) {
			?>
			<div class="row align-items-center">
				<div class="col-1 student">
					<input type="checkbox" name="result<?=$aux?>" value="<?=$row['name']?>">
				</div>
				<div class="col-11">
					<?php echo ucfirst($row['name']); ?>
				</div>
			</div>

			<?php
						$aux++;
					}

					// checkbox to check all students		
					echo "<div class='row align-items-center mt-4'>";
					echo "<div class='col-1'>";
					echo "<input type='checkbox' name='sample' class='selectall'>";
					echo "</div>";
					echo "<div class='col-11'>";
					echo "seleccionar todos los alumnos</div></div>";

					$_SESSION['checkboxNum'] = $aux;
				} else {
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
				}					
			?>

		</div>
	</div>

	<div class="row justify-content-center my-2 form-group">
		<div class="col-4 mb-5">
			<button type="submit" class="btn btn-primary btn-block"><?= $button ?></button>
		</div>
	</div>
</form>
<script>
	$('.selectall').click(function() {
    if ($(this).is(':checked')) {
        $('div.student input').attr('checked', true);
    } else {
        $('div.student input').attr('checked', false);
    }
	});
	$('.eligetodo').click(function() {
    if ($(this).is(':checked')) {
        $('div.campos input').attr('checked', true);
    } else {
        $('div.campos input').attr('checked', false);
    }
	});
</script>