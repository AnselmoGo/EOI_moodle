<?php
  require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');
	require_once('./php/addFile.class.php');

	//create the object to access the db
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");
?>
	<div class="row justify-content-center mt-5">
		<div class="center mt-3 col-8">
			<h1>Erstelle eine Leseverstehensseite</h1>
		</div>
		<div class="center mt-3 mb-5 col-10">
			In dieser Seite hast du die Möglichkeit, einen Text zu schreiben oder ihn als .txt Datei hochzuladen, um aus ihm dann eine Leseverstehensübung zu erstellen. Viel Spa bei der Arbeit.
			<br />
			Vorsicht, es gibt 5 Wörter zu viel!!
		</div>
	</div>
	<hr>
	<div class="row buttons">
		<!--SHOULD GET THE GLYPHICONS FROM BOOTSTRAP HERE -->
		<button onclick="exeBold()"><i class="fas fa-bold"></i></button>  
	</div>
	<div class="row justify-content-center">
		<iframe name="richTextField" class="richTF" src="" frameborder="0" onload="enableEditMode();alert('Done');" style="width: 1000px; height: 400px;"></iframe>
	</div>

	<script>
		function enableEditMode() {
			richTextField.document.designMode = 'On';
		}

		function exeBold() {
			richTextField.document.execCommand('bold');
		}
	</script>