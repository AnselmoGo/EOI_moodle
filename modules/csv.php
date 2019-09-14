<?php
	require_once('../includes/config_db_access.php');
	session_start();

	//create the object to access the db
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=data.csv');
	$output = fopen("php://output", "w");

	fputcsv($output, $_SESSION["csv_field"], ";");

	$query_select = '';

	foreach ($_SESSION["csv_field"] as $value) {
		if($value != "levels") {
			$query_select .= "resultados.$value,";			
		} else {
			$query_select .= "level.$value,";
		}
	}

	//take the last comma out of the string
	$query_select = substr($query_select, 0, -1);

	foreach($_SESSION['student'] as $value) {
		$query = "SELECT $query_select FROM level INNER JOIN resultados ON level.id = resultados.level WHERE resultados.name = '$value'";
		$result = $mysqli->query($query);
		while($row = $result->fetch_assoc()) {
			fputcsv($output, $row, ";");
		}
	}
	
	fclose($output);
	session_destroy();
?>