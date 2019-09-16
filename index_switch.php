<?php
switch ($p) {
  case 'acceso':
    $page = 'zugaccs.inc.php';
    $page_title = 'Acceso interno';
    break;
  case 'mostrar':
    $page = 'mostrar.inc.php';
    $page_title = 'Mostrar resultados prueba de clasificación';
    break;
  case 'prueba':  
    $page = 'prueba.inc.php';
    $page_title = 'Prueba de clasificación';
    break;  
  case 'lv':
    $page = 'lv.inc.php';
    $page_title = 'Comprensión lectora';
    break;
  case 'hv':
    $page = 'hv.inc.php';
    $page_title = 'Comprensión oral';
    break;   
  case 'csv':
    $page = 'csv.inc.php';
    $page_title = 'Archivo CSV';
    break;
  case 'memory':
    $page = 'memory.inc.php';
    $page_title = "Constructing a Memory Game";
    break;
  case 'leseverstehen':
    $page = 'leseverstehen.inc.php';
    $page_title = "Leseverstehen mit Übungsteil";
    break;
  case 'read':
    $page = 'readFile.inc.php';
    $page_title = 'Reading the File';
    break;
	case 'testAddFile':
		$page = "test_addFileClass.php";
		$page_title = "Test AddFile";
		break;
  default:
    $page = 'main.inc.php';
    $page_title = 'Acceso - Página principal';
    break;       
}
