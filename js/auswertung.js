	/* Diese Funktion speichert die Antwort als korrekt (correct == true) in der
	** "hits" Variable oder als falsch (correct == false) in der "falsche" Variable. 
	** Im letzten Falle wird auch der "key" des Satzes in die "falsche_liste" Variable 
	** eingefügt, um später alle falschen Sätze hervorholen zu können.
	*/
	function save_answers(correct, chosen = 0) {
		var frm = document.forms["myForm"];
		var match = 0;		//= no match found (1 == match found)
		var str, res;
		var options = 4;
		if(correct == true) {
			str = document.getElementById("question").innerHTML;
			res = str.replace("___", frm.elements[chosen].value);
			//speichert die korrekten Antworten in die Variable "hits"			
			hits += res + "<br />\n";		
		} else {
			//Unterstrich wird mit der gewählten Option ersetzt
			str = document.getElementById("question").innerHTML;			
			//if the option doesn't contain a "___", the whole answer is included
			var inc = str.includes("___");
			if(chosen != -1) {
				if(inc == true) {
					var res_student = str.replace("___", "<u>"+frm.elements[chosen].value+"</u>");
					var res_correct = str.replace("___", "<u>"+frm.elements[options].value+"</u>");
				} else {
					var res_student = str + " - <u>"+frm.elements[chosen].value+"</u>";
					var res_correct = str + " - <u>"+frm.elements[options].value+"</u>";
				}
			} else {
				var res_student = " --- No answer given! ---";		//if no option has been checked
				if(inc == true) {				
					var res_correct = str.replace("___", "<u>"+frm.elements[options].value+"</u>");
				} else {					
					var res_correct = str + " - <u>"+frm.elements[options].value+"</u>";
				}
			}
			
			var res_aux = "Deine Lösung: <span class='red'>" + res_student + "</span><br />\nKorrekte Lösung: <span class='green'>" + res_correct + "</span><br />";

			if(falsche.length == 0) {
				falsche[0] = new Array();
				falsche[0][0] = frm.elements[(options+1)].value + "<br>";
				falsche[0][1] = frm.elements[(options+2)].value + "<br>";
				falsche[0][2] = res_aux;
			} else {
				var aux = falsche.length;
console.log(aux);
				/*
				if(aux == 1) {
					if(falsche[0][0] == frm.elements[(options+1)].value + "<br>") {
						x = falsche[0].length;
						falsche[0][x] = res_aux;
						match = 1;						
					}
					*/
				// } else {
					for(i = 0; i < aux; i++) {
						//The topic corresponds to one that is already on the list
						if (falsche[i][0] == frm.elements[(options+1)].value + "<br>") {
							var aux2 = falsche[i].length;
							falsche[i][aux2] = res_aux;
							match = 1;
						}								
					}
				//}
				if(match == 0) {		//no match found
					//we have to create a new entry to include the wrong answer
					falsche[aux] = new Array();
					falsche[aux][0] = frm.elements[(options+1)].value + "<br>";
					falsche[aux][1] = frm.elements[(options+2)].value + "<br>";
					falsche[aux][2] = res_aux;
				}
			}
		}
	}

	/* Diese Funktion testet die ausgesuchte Antwort auf korrekt oder falsch.
	** Die Variable "count_hits" zählt die korrekten Antworten und gibt sie in dem div "counting" aus.
	*/
	function testen_radio(options = 4) {
		var frm = document.forms["myForm"];
		var counting = 0;
		for(i=0; i<options; i++){
			if(typeof frm.elements[i] != "undefined") {			
				if(frm.elements[i].checked){
					//evaluates that the checked item = the solution item
					if(frm.elements[i].value == frm.elements[options].value) {					
						count_hits++;																				
					} 
					//calls on the function with true of false depending on what the result gives
					save_answers(frm.elements[i].value == frm.elements[options].value, i);
				} else {
					counting++;				
				}
			}
		}

		if(counting == options) {
			save_answers(false, -1);		//no option checked!!!
		}

		//Hier wird jeder Versuch gezählt
		count_all++;
		// Wenn count_all = hit_items & = count_hits, wird der user zum nächsten Level geführt
		// wenn count_all == count_hits ==> alle geklickten waren korrekt
		if((count_all == hit_items) && (count_all == count_hits)) {
			prozent = (count_hits/count_all*100).toFixed(2);
			AjaxSaving("speichern.php", hits, prozent, count_all, levelID);
			hits = "<p><u>Das sind deine korrekten Antworten:</u></p>\n";;
			levelID = levelID + 1;
alert("LevelID is: " + levelID);
			count_all = 0;
			count_hits = 0;
		}

		//Wenn die max Anzahl von items erreicht ist, wird das Niveau errechnet
		if((count_all == ex_num)) {
			if((count_hits/count_all*100) > pass && levelID < 8) {
				levelID = levelID + 1;			
alert("Du bist ein level weiter gestiegen.");
			} else if((count_hits/count_all*100) < back && levelID > 1) {
				levelID = levelID - 1;			//das Niveau wird zurückgesetzt			
alert("Du bist ein level zurückgestuft worden.");
			} else {			
				alert("This is your levelID: " + levelID);				
				prozent = (count_hits/count_all*100).toFixed(2);
				//we have to chose one: AjaxSaving or feedback - both write into de db
				//AjaxSaving has an error here; writes only hits, should write hits and fails!!!!!!! see feedback
				AjaxSaving("speichern.php", hits, prozent, count_all, levelID);
				feedback();
				window.open("/eoi/lv", "_self");		//here starts the page that checks the reading
				//exit();
			}
			AjaxRequest(datei, table, levelID);		//is called with the new variables
			count_all = 0;
			count_hits = 0;
		}

		document.getElementById("counting").innerHTML = count_hits;
		if(count_all == ex_num) {				
			document.getElementById("ende").innerHTML = "<h2>Das ist das Ende der Übung.</h2><br />";
			feedback();
		}
	}

	/* Diese Funktion überprüft Antworten, die im Textfeld eingegeben worden sind, und gleicht sie mit
	** der angegebenen Lösung ab. Es können mehrere Wörter gecheckt werden. Wenn es mehrere Lücken zwischen
	** den Wörtern gibt, werden sie eliminiert.
	*/ 
	function testen_text() {
		var count_answers = 0;
		if(document.getElementById("test1").value != '') {
			student = document.getElementById("test1").value.trim();					
			// removes all double blanks in the answer		
			while(student.search("  ") != -1) {
				student = student.replace("  ", " ");
			}
			answer = student.split(" ");		
			// result gets the correct solution		
			result = document.getElementById("sol").value.split(" ");			
			for(i=0; i<result.length; i++){
				if(answer[i] == result[i]){
					count_answers++;
				}				
			}
			if(count_answers == result.length) {
				if(document.getElementById("no_feed").checked == false ) {
					alert("Das war richtig!");
				}
				document.getElementById("counting").innerHTML = ++count_hits;
			} else {
				// Wenn der Artikel fehlt oder falsch ist, gibt es hier eine Meldung
				if(result[0] == "der" || result[0] == "die" || result[0] == "das") {
					if(result.length > answer.length) {
						alert("Es fehlt der Artikel!");
						exit;
					}	else if (result.length == answer.length && result[0] != answer[0]) {
						alert("Der Artikel ist nicht richtig!");
						exit;
					}	else {
						//falsche Antwort wird gespeichert
						save_answers(false, 0);
						if(document.getElementById("no_feed").checked == false ) {
							alert("Das war leider falsch!");
						}
					}		
				} else {
					//falsche Antwort wird gespeichert
					save_answers(false, 0);
					if(document.getElementById("no_feed").checked == false ) {
						alert("Das war leider falsch!");
					}
				}
			}
			count_all++
		} else {
			//Wenn nichts angegeben wurde wird das Script gestoppt!
			exit()
		}		
	}

	/* In dieser Funktion werden die Antworten, die "geübt" wurden, aus den verschiedenen Variablen
	** herausgelesen und in die einzelnen divs hineingeschrieben, sodass man eine endgültige Auswertung
	** hat. Es wird außerdem der Prozentsatz der korrekten Angaben eingefügt.
	*/
	function feedback() {
		//buttons werden versteckt oder angezeigt
		document.getElementById("ajax_response").className += " d-none";
		document.getElementById("btns").className += " d-none";
		//document.getElementById("repeat").className = "btn btn-primary tbs";
		// wenn sie vorher gespeichert wurden, werden
		// die korrekten Antworten in den DIV "hit" eingegeben			
		if((count_hits > 0) && (typeof hits !== 'undefined')) {
			document.getElementById("hit").innerHTML = hits;
		}		
		//falls es eine Liste der falschen Antworten gibt, und diese vorher
		// gespeichert wurde, wird sie in dem DIV "falsch" angezeigt	
		if(typeof falsche !== 'undefined') {
			var aux = falsche.length;
			for(i = 0; i < aux; i++) {
				//starts at 1 because 0 is the key; we don't want it in the evaluation
				for(j = 1; j < falsche[i].length; j++) {
					fails += falsche[i][j] + "\n";					
				}				
			}

			if(count_hits < count_all) {
				document.getElementById("falsch").innerHTML = fails;
			}
		}
			
		//Prozensatz der korrekten Antworten wird hier angegeben falls die Übung gemacht wurde
		if(count_all > 0) {
			prozent = (count_hits/count_all*100).toFixed(2);
			$feedbck = "Korrekte Antworten: " + prozent + "%";
		} else {
			$feedbck = "Die Übung ist nicht korrekt durchgeführt worden.";
		}
		document.getElementById("all").innerHTML = $feedbck;
		

    txt = hits + " <br>\n\n " + fails;
    AjaxSaving("speichern.php", txt, prozent, count_all, levelID);

    //initialize for the next level
    count_all = 0;
		count_hits = 0;
		FinishTimer();
	}