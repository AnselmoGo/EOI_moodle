<?php
  require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');
	require_once('./php/build_Text.class.php');

	//create the object to access the db
	$config = config_db_access::getInstance();
	$mysqli = $config->getConnection();
	$mysqli->set_charset("utf8");
?>
	<div class="row justify-content-center mt-5">
		<div class="justify-content-center mt-3 col-4">
			<h2>Lückentext</h2>
		</div>
		<div class="justify-content-center mt-3 col-10">
			In diesem Text musst du entscheiden welches von den Wörtern in den Kästen zu dem Satz passt. Viel Glück!!
			<br />
			Vorsicht, es gibt 5 Wörter zu viel!!
		</div>
	</div>

<?php
	$table = 'lv_select';

	$exercise_builder = new build_Text($table);

	// items that will be selected in the DB
	$selection[] = 'solution';
	$selection[] = 'exercise';
?>
