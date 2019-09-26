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
			In dieser Seite hast du die Möglichkeit, einen Text zu schreiben oder ihn als .txt Datei hochzuladen, um aus ihm dann eine Leseverstehensübung zu erstellen. Viel Spaß bei der Arbeit.			
		</div>
	</div>
	<hr>
	<div class="row buttons">
		<div class="col-8 offset-sm-1">			
			<button type="button" class="btn btn-default glyphs" onclick="execCmd('bold')">
				<span class="glyphicon glyphicon-bold"></span>
			</button>
			<button type="button" class="btn btn-default glyphs" onclick="execCmd('italic')">
				<span class="glyphicon glyphicon-italic"></span>
			</button>
			<button type="button" class="btn btn-default glyphs" onclick="execCmd('underline')">
				<span class="glyphicon glyphicon-text-color"></span>
			</button>

			<button type="button" class="btn btn-default glyphs" onclick="execCmd('justifyLeft')">
				<span class="glyphicon glyphicon-align-left"></span>
			</button>
			<button type="button" class="btn btn-default glyphs" onclick="execCmd('justifyCenter')">
				<span class="glyphicon glyphicon-align-center"></span>
			</button>
			<button type="button" class="btn btn-default glyphs" onclick="execCmd('justifyRight')">
				<span class="glyphicon glyphicon-align-right"></span>
			</button>
			<button type="button" class="btn btn-default glyphs" onclick="execCmd('justifyFull')">
				<span class="glyphicon glyphicon-align-justify"></span>
			</button>
		</div> 
	</div>
	<div class="row">
		<div class="col-10 offset-sm-1">
			<iframe name="richTextField" class="richTF" src="" onload="enableEditMode();"></iframe>
		</div>
	</div>

	<script>
		function enableEditMode() {
			richTextField.document.designMode = 'On';
		}

		function execCmd(command) {
			richTextField.document.execCommand(command, false, null);
		}

	</script>