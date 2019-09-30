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
	<form name="form" id="myForm" method="post" action="<?php BASE_URL . $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
		<div class="row buttons">
			<div class="col-5 offset-sm-1">			
				<button type="button" class="btn btn-default glyphs" data-title="bold" onclick="execCmd('bold')">
					<span class="glyphicon glyphicon-bold"></span>
				</button>
				<button type="button" class="btn btn-default glyphs" onclick="execCmd('italic')">
					<span class="glyphicon glyphicon-italic"></span>
				</button>
				<button type="button" class="btn btn-default glyphs" onclick="execCmd('underline')">
					<span class="glyphicon glyphicon-text-color"></span>
				</button>
				<button type="button" class="btn btn-default glyphs" onclick="execCmd('cut')">
					<span class="glyphicon glyphicon-scissors"></span>
				</button>
				<button type="button" class="btn btn-default glyphs" onclick="execCmd('copy')">
					<span class="glyphicon glyphicon-duplicate"></span>
				</button>
				<button type="button" class="btn btn-default glyphs" onclick="execCmd('paste')">
					<span class="glyphicon glyphicon-copy"></span>
				</button>
				<select name="headers" id="headers" onchange="execCmdWithArg('formatBlock', this.value);">
					<option value="">??</option>
					<option value="H1">H1</option>
					<option value="H2">H2</option>
					<option value="H3">H3</option>
					<option value="H4">H4</option>
					<option value="H5">H5</option>
					<option value="H6">H6</option>
				</select>
			</div>
			<div class="col-4">
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
				<button type="button" class="btn btn-default glyphs" onclick="execCmd('insertUnorderedList')">
					<span class="glyphicon glyphicon-list"></span>
				</button>
			</div> 
		</div>
		<div class="row">
			<div class="col-10 offset-1">
				<!-- <iframe name='richextField' class='rchTF' src='' onload='enableEditMode();'></iframe>  -->
				<?php
					if (isset($_POST["build_memory"])) {
						$data = file_get_contents($_FILES["userFile"]["tmp_name"]);
						//echo "<textarea name='richTextField'>".htmlspecialchars($data)."</textarea>";
						echo "<iframe name='richTextField' class='richTF' srcdoc=\"". htmlspecialchars($data) . "\" onload='enableEditMode();'></iframe>";
					} else {
						//echo "<iframe name='richxtField' class='richTF' src='' onload='enableEditMode();'></iframe>";
					}
				?>			
			</div>
		</div>

		<div class="row">
			<div class="col-3 offset-1">Elige un archivo que quieras cargar: </div>
			<div><input id="userFile" class="form-control" type="file" name="userFile"></div>
		</div>
		<div class="row justify-content-center">
			<button type='submit' class='btn btn-primary my-3' name='build_memory'>Text laden</button>
		</div>
		
	</form>

	<script>
		richTextField.document.designMode = 'On';

		function enableEditMode() {
			richTextField.document.designMode = 'on';
		}
		function execCmd(command) {
			richTextField.document.execCommand(command, false, null);
		}

		function execCmdWithArg(command, arg) {
			richTextField.document.execCommand(command, false, arg);
		}
	</script>