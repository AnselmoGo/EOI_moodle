<?php
  require_once('./modules/base_url.php');
	require_once('./includes/config_db_access.php');
	require_once('./php/build_Text.class.php');
	require_once('./php/uniqueRandom.class.php');
	require_once('./php/buildDivs.class.php');

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
			<?php
				if(isset($distract)) {
					echo "Vorsicht, es gibt ";
					if($distract > 1) {
						echo "$distract Wörter ";
					} else {
						echo "$distract Wort ";
					}
					echo "zu viel!!";
				}
			?>
		</div>
	</div>
	<hr>

	<div class="row">
		<div class="col-12 center">
			<div class="big" id="all"></div>
		</div>
	</div>
	
	<form name="form" class="form-inline line" id="myForm" method="post" action="<?php BASE_URL . $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
		<div class="row justify-content-center">
			<div class="my-3 col-10 line_high">							
				<?php
					$tsbId = 1;

					$text_builder = new build_Text($table);
					$text_builder->set_TbsId($tsbId);
					// $forms = variable that is built in the "parent"-file that contructs the exercise
					// when creating the file in createLV.inc.php 
					$text = $text_builder->get_Text($mysqli, $forms);
					echo $text;

					$js_solution = json_encode($text_builder->get_Solution($mysqli));
					$js_rows = json_encode($text_builder->get_Gaps());
					echo "<script>var solution = $js_solution, rows = $js_rows;</script>";
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-12 center mb-3">
				<input type="button" name="test_btn" value="Testen" class="btn btn-primary" onclick="check()" />
				<input type="button" name="losung_btn" value="Lösung" class="btn btn-primary" onclick="loesung()" />
				<input type="submit" name="wiederholen" value="Übung wiederholen" class="btn btn-primary" />				
			</div>
		</div>		
	</form>
	<script>
		function check() {
			let count = 0;
			for(i = 0; i < rows; i++) {
				let outer = 'outer_item' + i;
				let gap = 'item' + i;
				if(document.getElementById(gap).value == solution[i]) {
					count++;
					document.getElementById(outer).innerHTML = "<span class='green'><strong>" + solution[i] + "</strong></span>";
				}
			}
			document.getElementById("all").innerHTML = "Korrekte Antworten: " + (count / rows * 100).toFixed(2) + "%";
		}

		function loesung() {			
			for(i = 0; i < rows; i++) {
				let outer = 'outer_item' + i;
				let gap = 'item'+ i;
				if((typeof document.getElementById(gap) === 'undefined') || (typeof document.getElementById(gap) === null)) {
					continue;
				} else {
					if(document.getElementById(gap) !== null) {
						document.getElementById(outer).innerHTML = "<span class='red'><strong>" + solution[i] + "</strong></span>";
					}
				}			
			}
		}
	</script>