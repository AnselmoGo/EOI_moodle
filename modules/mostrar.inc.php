<?php
  require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');

	//create the object to access the db
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");

	$table = "resultados";
	$columns = '';
	$columns_join = '';
?>
	
	<div class="row justify-content-center mt-5">
		<div class="col-10 justify-content-center mt-3">
			
			<form name="form" id="myForm" method="post" action="modules/csv.php">
				<div class="row justify-content-center my-2 form-group">
					<div class="col-4 mb-5">
						<button type="submit" class="btn btn-primary btn-block">descargar archivo</button>
					</div>
				</div>
			</form>

			<div class="row align-items-center">				

<?php
	// extract the columns needed the query the table
	for($i = 0; $i < $_SESSION['fieldNum']; $i++) {
		if(!empty($_POST["field$i"])) {
			if($_POST["field$i"] != "levels") {
				if($_POST["field$i"] == "text") {
					$width = "col-6";
				} else {
					$width = "col-2";
				}
				echo "<div class='$width'>" . $_POST["field$i"] . "</div>";
				$columns .= $_POST["field$i"] . ",";
				$col[] = $_POST["field$i"];				 
			} else {
				echo "<div class='col-1'>" . $_POST["field$i"] . "</div>";
				$columns_join = $_POST["field$i"];
			}
			$csv_field[] = $_POST["field$i"];
		}
	}
	echo "</div>";
	echo "<hr>";

	//take the last comma from the $columns variable
	$columns = substr($columns, 0, -1);

	for($i = 0; $i < $_SESSION['checkboxNum']; $i++) {
		if(!empty($_POST["result$i"])) {
			$query = "SELECT $columns,level FROM $table WHERE name = '" . $_POST["result$i"] . "' ORDER BY id ASC";
			$result = $mysqli->query($query);

			$colsNum = count($col);

			while($row = $result->fetch_assoc()) {
				
				echo '<div class="row align-items-center">';
				echo "<br><br>";
				
				for($j = 0; $j < $colsNum; $j++) {
					if($col[$j] == "text") {
						$width = "col-6";
					} else {
						$width = "col-2";
					}
					echo "<div class='$width'>" . $row["$col[$j]"] . "</div>";
				}

				if($columns_join != '') {
					$res = $mysqli->query("SELECT $columns_join FROM level WHERE id={$row['level']}");
					$rw = $res->fetch_assoc();
					echo "<div class='col-2'>" . $rw["$columns_join"] . "</div>";
				}

				echo '</div>';

				echo "<hr>";
			}
			$csv_student[] = $_POST["result$i"];
		}
	}	
	//to be used in the csv.php file
	$_SESSION['csv_field'] = $csv_field;
	$_SESSION['student'] = $csv_student;
?>				
			</div>
		</div>
		<form name="form" id="myForm" method="post" action="<?=BASE_URL . '/acceso' ?>">
			<div class="row justify-content-center my-2 form-group">
				<div class="col-4 mb-5">
					<button type="submit" class="btn btn-primary btn-block">volver</button>
				</div>
			</div>
		</form>