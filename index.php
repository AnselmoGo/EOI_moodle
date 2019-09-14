<?php
/* 
 *  This is the main page.
 *  This page includes the configuration file, 
 *  the templates, and any content-specific modules.
 */

// Require the configuration file before any PHP code:
require('./includes/config.inc.php');

$head_title = '';

// Validate what page to show:
if (isset($_GET['p'])) {
    $p = htmlspecialchars(strip_tags($_GET['p']));
} elseif (isset($_POST['p'])) { // Forms
    $p = htmlspecialchars(strip_tags($_POST['p']));
} else {
    $p = NULL;
}

if (isset($_GET['d'])) {
    $file = htmlspecialchars(strip_tags($_GET['d'])) . '/';
} elseif (isset($_POST['d'])) { // Forms
    $file = htmlspecialchars(strip_tags($_POST['d'])) . '/';
} else {
    $file = NULL;
}

if (isset($_GET['f'])) {
    $file .= htmlspecialchars(strip_tags($_GET['f'])) . '/';
} elseif (isset($_POST['f'])) { // Forms
    $file .= htmlspecialchars(strip_tags($_POST['f'])) . '/';
}

if (isset($_GET['g'])) {
    $file .= htmlspecialchars(strip_tags($_GET['g'])) . '/';
} elseif (isset($_POST['g'])) { // Forms
    $file .= htmlspecialchars(strip_tags($_POST['g'])) . '/';
}

if(isset($_GET['d'])) {
  $head_title = ucfirst(htmlspecialchars(strip_tags($_GET['d'])));
} elseif(isset($_POST['d'])) {
  $head_title = ucfirst(htmlspecialchars(strip_tags($_POST['d'])));
}
if(isset($_GET['f'])) {
  $head_title .= ' '.ucfirst(htmlspecialchars(strip_tags($_GET['f'])));
} elseif(isset($_POST['f'])) {
  $head_title .= ' '.ucfirst(htmlspecialchars(strip_tags($_POST['f'])));
} elseif(isset($_GET['p']) && $head_title != '') {
  $head_title .= ' '.ucfirst(htmlspecialchars(strip_tags($_GET['p'])));
} elseif (isset($_POST['p']) && $head_title != '') {
  $head_title .= ' '.ucfirst(htmlspecialchars(strip_tags($_POST['p'])));
}

  include('./index_switch.php');


/*
// Determine what page to display:
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

  // Default is to include the main page.
  default:
    $page = 'main.inc.php';
    $page_title = 'Acceso -  Página principal';
    break;
        
} // End of main switch.

*/

// Make sure the file exists:
if (!file_exists('./modules/' . $page)) {
    $file = '';
    $page = '404.php';
    $page_title = 'Prueba de clasificación';
}

// Include the header file:
include('./includes/header.html');

// Include the content-specific module:
// $page is determined from the above switch.
include('./modules/' . $page);

// Include the footer file to complete the template:
include('./includes/footer.html');
?>