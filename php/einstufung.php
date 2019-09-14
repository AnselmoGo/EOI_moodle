<?php 	
	require_once('../includes/config_db_access.php');
	require_once('./min_Max.class.php');
	session_start();
	
	$table = $_GET['tbl'];
	$type = $_SESSION['type'];

	if(!isset($_SESSION['levelID'])) {
		$_SESSION['levelID'] = $_GET['lvl'];
	}

	if(($_GET['lvl'] != $_SESSION['levelID'])) {
		$_SESSION['levelID'] = $_GET['lvl'];
		
		$config = config_db_access::getInstance();
		$mysqli = $config->getConnection();
		$mysqli->set_charset("utf8");		
		$new_min_max = new Min_Max($_GET['tbl']);
		$new_min_max->setLevelID($_SESSION['levelID']);
		$new_min_max->setMinMax($mysqli, $_SESSION['idioma']);
		$_SESSION['max_num'] = $new_min_max->getMaxNumber();
		$_SESSION['min_num'] = $new_min_max->getMinNumber();
	}

	include_once('./einstufung.inc.php');