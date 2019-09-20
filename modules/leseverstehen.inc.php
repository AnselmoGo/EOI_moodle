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
		<div class="center mt-3 col-4">
			<h1>Lückentext</h1>
		</div>
		<div class="center mt-3 mb-5 col-10">
			In diesem Text musst du entscheiden welches von den Wörtern in den Kästen zu dem Satz passt. Viel Glück!!
			<br />
			Vorsicht, es gibt 5 Wörter zu viel!!
		</div>
	</div>
	
	<div class="row justify-content-center">
		<div class="my-3 col-10 line_high">							
			<?php
				$table = 'lv_select';
				$tsbId = 1;

				$text_builder = new build_Text($table);
				$text_builder->set_TbsId($tsbId);
				$text = $text_builder->get_Text($mysqli);
				echo $text;

			?>
		</div>
	</div>