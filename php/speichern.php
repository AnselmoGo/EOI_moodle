<?php
	// Das muss geändert werden, da sich die Speicherung der Daten in drei Phasen vollzieht.
	// 1. Nach der kompletten Durchführung der Multiple-Choice Aufgaben.
	// 2. Nach der Durchführung der LV Aufgaben
	// 3. nach der Durchführung der HV Aufgaben
	require_once('../includes/config_db_access.php');
	session_start();

	$find_txt = 'HV';

	$txt = mysql_real_escape_string($_POST['txt']);		//feedback text
	$hts = mysql_real_escape_string($_POST['hts']);		//hits in the exercise
	$itms = mysql_real_escape_string($_POST['itms']);		//total items given
	$lvl = mysql_real_escape_string($_POST['lvl']);		//level

	$date = date('Y-m-d H:i:s', $_SESSION['time']);
	
	$time2 = time();
	$difference = $time2 - $_SESSION['time'];

	//calculating the hours:minutes:seconds for duration
	//put into seperate file!!!
	$mm = floor($difference / 60);
	$ss = $difference % 60;
	if($ss < 10) {
		$ss = "0$ss";
	}
	
	if($mm > 60) {
		$hh = floor($mm / 60);
		if($hh < 10) {
			$hh = "0$hh";
		}
		$mm = $mm % 60;
	} else {
		$hh = "00";
	}

	if($mm < 10) {
		$mm = "0$mm";
	}

	$duration = "$hh:$mm:$ss";


	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");

	$name = $_SESSION['name'];	//user name

	$quest = "INSERT INTO resultados (name, level, text, num_items, aciertos, fecha, duracion) VALUES ('$name', '$lvl', '$txt', '$itms', '$hts', '$date', '$duration')";

	if($mysqli->query($quest) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: $quest <br> {$mysqli->error}";
	}
	//session_destroy();
	$_SESSION['done'] = ',';		// reset $_SESSION['done']
	if(strstr($txt, $find_txt)) {
		session_destroy();
	}