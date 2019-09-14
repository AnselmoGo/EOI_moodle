//sets the Answers object
function Answers(txttype = ''){
	this.count_all = 0;
	this.count_hits = 0;
	this.txttype = txttype;
	this.txt = '';
	this.crrct = "<br>\nCorrect answers: <br>\n";
	this.fls = "<br>False answers: <br>\n";
	this.fdbck = "Korrekte Antworten: ";
	this.file = "speichern.php";
}

Answers.prototype.reset = function()  {
	this.count_all = 0;
	this.count_hits = 0;
	this.crrct = "<br>\nCorrect answers: <br>\n";
	this.fls = "<br>False answers: <br>\n";	
}

Answers.prototype.save = function(correct, quest = 0, chosen = 0, solution = 0) {		
	if(correct) {
		this.crrct += "Q: " + document.getElementById("question" + quest).innerHTML;
		this.crrct += "<br>" + frm.elements[chosen].value + "<br>\n";
	} else {			
		this.fls += "Q: " + document.getElementById("question" + quest).innerHTML;
		if(chosen != -1) {
			this.fls += "<br>Falsch: " + frm.elements[chosen].value + "<br>\n";				
		} else {
			this.fls += "<br>No answer given.<br>\n";
		}
		this.fls += "Korrekt: " + frm.elements[solution].value + "<br>\n";
	}
}

//object that includes the answers from the student (right or wrong)
function WrongAnswers(clave, grammar) {
	this.clave = clave;
	this.grammar = grammar;
	this.answer = new Array();
}

WrongAnswers.prototype.includeAnswer = function(answer) {
	this.answer[this.answer.length] = answer;
}

var wrongAnswers = [];

Answers.prototype.intoObject = function(answer, options) {
	var _match = 0;			//= no match found (1 == match found)

	for(var i = 0; i < wrongAnswers.length; i++) {
		if(wrongAnswers[i].clave == frm.elements[(options+1)].value) {
			wrongAnswers[i].includeAnswer(answer);
			_match = 1;
		}
	}

	if(_match == 0) {
		var _answer = new WrongAnswers(frm.elements[(options+1)].value, frm.elements[(options+2)].value);
		_answer.includeAnswer(answer);

		wrongAnswers.push(_answer);
	}
}

//constructs the answer string that is sent to the this.intoObject method if wrong
//or is stored in this.crrct if correct
Answers.prototype.saveCL = function(correct, chosen = 0, options = 4) {
	var _str = document.getElementById("question0").innerHTML;
	if(correct) {		
		_str = _str.replace("___", "<u>"+frm.elements[chosen].value+"</u>");
		this.crrct += _str + "<br />\n";
	} else {
		//if the option doesn't contain a "___", the whole answer is included
		var _inc = _str.includes("___");
		if(chosen != -1) {
			if(_inc == true) {
				var _res_student = _str.replace("___", "<u>"+frm.elements[chosen].value+"</u>");
				var _res_correct = _str.replace("___", "<u>"+frm.elements[options].value+"</u>");
			} else {
				var _res_student = _str + " - <u>"+frm.elements[chosen].value+"</u>";
				var _res_correct = _str + " - <u>"+frm.elements[options].value+"</u>";
			}
		} else {
			var _res_student = " --- No answer given! ---";		//if no option has been checked
			if(_inc == true) {				
				var _res_correct = _str.replace("___", "<u>"+frm.elements[options].value+"</u>");
			} else {					
				var _res_correct = _str + " - <u>"+frm.elements[options].value+"</u>";
			}
		}

		var _answer = "Deine Lösung: <span class='red'>" + _res_student + "</span><br />\nKorrekte Lösung: <span class='green'>" + _res_correct + "</span><br />";

		this.intoObject(_answer, options);		//calling to save the items into the array
	}
}

Answers.prototype.createFls = function() {
		if(wrongAnswers.length > 0) {
		for(var i = 0; i < wrongAnswers.length; i++) {
			this.fls += wrongAnswers[i].grammar + "<br>\n";
			for(var j = 0; j < wrongAnswers[i].answer.length; j++) {
				this.fls += wrongAnswers[i].answer[j] + "<br>\n";
			}			
		}		
	}
	wrongAnswers = [];
}

//can throw this out!!!!!
/*
Answers.prototype.setText = function(txt) {
	this.txt = txt + " <br>\n";
}
*/

Answers.prototype.buildText = function() {
	this.txt = this.txttype + this.crrct + this.fls;
}

Answers.prototype.hide = function(text = '', buttons = '', other = '') {
	document.getElementById(text).className += " d-none";
	document.getElementById(buttons).className += " d-none";
	if(other != '') {
		document.getElementById(other).className += " d-none";
	}
}

// shows the answers on screen
Answers.prototype.show = function() {
	this.fdbck += (this.count_hits/this.count_all*100).toFixed(2) + "%";
	document.getElementById("all").innerHTML = this.fdbck + "<p>" + this.txt + "</p>";
}


// shows the answers on screen
/*
Answers.prototype.show = function(text = '', buttons = '') {
	document.getElementById(text).className += " d-none";
	document.getElementById(buttons).className += " d-none";

	this.fdbck += (this.count_hits/this.count_all*100).toFixed(2) + "%";

	document.getElementById("all").innerHTML = this.fdbck + "<p>" + this.txt + "</p>";
}
*/

// saves the answers in the db
Answers.prototype.saving = function() {
	AjaxSaving(this.file, this.txt, (this.count_hits/this.count_all*100).toFixed(2), this.count_all, levelID);
}

Answers.prototype.feedback = function() {
	this.hide("ajax_response", "btns", "Timer");	

	this.createFls();

	//Prozensatz der korrekten Antworten wird hier angegeben falls die Übung gemacht wurde
	if(ans.count_all > 0) {
		this.buildText();
		this.show(); 
	} else {
		document.getElementById("all").innerHTML = "Die Übung ist nicht korrekt durchgeführt worden.";
	}

	this.saving();
}

// function to evaluate the answers, can eliminate txt parameter
/*
function testenRadio(txt = '', options = 4) {
	//ans.setText(txt);

	for(i=0; i<num_quest; i++) {
		var _counting = 0;
		for(j=0; j<options; j++) {
			if(typeof frm.elements[(i*num_quest)+j] != "undefined") {			
				if(frm.elements[(i*num_quest)+j].checked){
					//evaluates that the checked item = the solution item
					if(frm.elements[(i*num_quest)+j].value == frm.elements[(i*num_quest)+options].value) {
						ans.count_hits++;																				
					} 
					//calls on the function with true of false depending on what the result gives
					//other parameters are to identify the radio elements treated
					ans.save(frm.elements[(i*num_quest)+j].value == frm.elements[(i*num_quest)+options].value, i, ((i*num_quest)+j), ((i*num_quest)+options));
				} else {
					_counting++;				
				}
			}

			if(_counting == options) {
				ans.save(false, i, -1, ((i*num_quest)+options));		//no options checked!!!
				_counting = 0;
			}
		}	
		ans.count_all++;
	}

	if(ans.count_all == num_quest) {		
		//buttons werden versteckt oder angezeigt			
		ans.buildText();
		ans.show("ajax_response", "btns");
		ans.saving();
		if(txt == 'LV') {			
			setTimeout(directing, 500);
		}
	}
}
*/

function testenRadio(options = 4) {

	for(i=0; i<num_quest; i++) {
		var _counting = 0;
		for(j=0; j<options; j++) {
			if(typeof frm.elements[(i*num_quest)+j] != "undefined") {			
				if(frm.elements[(i*num_quest)+j].checked){
					//evaluates that the checked item = the solution item
					if(frm.elements[(i*num_quest)+j].value == frm.elements[(i*num_quest)+options].value) {
						ans.count_hits++;																				
					} 
					//calls on the function with true of false depending on what the result gives
					//other parameters are to identify the radio elements treated
					if(ans.txttype == 'CL') {
						ans.saveCL(frm.elements[(i*num_quest)+j].value == frm.elements[(i*num_quest)+options].value, j)
					} else {
						ans.save(frm.elements[(i*num_quest)+j].value == frm.elements[(i*num_quest)+options].value, i, ((i*num_quest)+j), ((i*num_quest)+options));
					}					
				} else {
					_counting++;
				}
			}

			if(_counting == options) {
				if(ans.txttype == 'CL') {
					ans.saveCL(false, -1)
				} else {
					ans.save(false, i, -1, ((i*num_quest)+options));		//no options checked!!!
					_counting = 0;
				}				
			}
		}	
		ans.count_all++;
	}

	if(ans.txttype == "CL") {
		//check if it has answered all hit_items correct (result = 100%)
		if((ans.count_all == hit_items) && (ans.count_all == ans.count_hits)) {
			
			ans.buildText();		//create entry with all corrects
			ans.saving();				//save it in the DB
			ans.reset();				//resets all necessary variables
			wrongAnswers = [];	//resets the wrongAnswers array

			levelID = levelID + 1;	//goes to the next level
alert("LevelID is: " + levelID);
		}

		//Wenn die max Anzahl an items erreicht ist, wird das Niveau errechnet
		if((ans.count_all == ex_num)) {
			ans.createFls();		//saves the wrong answers into the fls variable
			ans.buildText();		//create entry with all corrects
			ans.saving();				//save it in the DB
			//if you are above pass level you pass onto the next level
			if((ans.count_hits/ans.count_all*100) > pass && levelID < 8) {
				levelID = levelID + 1;				
alert("Du bist ein level weiter gestiegen.");
			//if you are below back level you get to the previous level
			} else if((ans.count_hits/ans.count_all*100) < back && levelID > 1) {
				levelID = levelID - 1;			//das Niveau wird zurückgesetzt			
alert("Du bist ein level zurückgestuft worden.");
			} else {			
				// Das Niveau ist erreicht worden
				//alert("This is your levelID: " + levelID);
				FinishTimer();
				setTimeout(directing, 500);
				//window.open("/eoi/lv", "_self");		//here starts the page that checks the reading				
			}
			ans.reset();				//resets all necessary variables
			wrongAnswers = [];	//resets the wrongAnswers array
			AjaxRequest(datei, table, levelID);		//is called with the new variables			
		}
	} else {
		if(ans.count_all == num_quest) {		
			//buttons werden versteckt oder angezeigt			
			ans.buildText();
			ans.hide("ajax_response", "btns");
			//ans.hide("ajax_response", "btns", "Timer");
			ans.show();
			ans.saving();
			if(ans.txttype == 'LV') {			
				setTimeout(directing, 500);
			}
		}
	}
}


function directing() {
	window.open("/eoi/lv", "_self");		//here starts the page that checks the reading
}

function feedback() {
	ans.feedback();
}