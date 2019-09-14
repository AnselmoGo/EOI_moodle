//CODE FOR HANDLING TIMER
//Timer code
var Seconds;
var Interval = null;

function StartUp() {
  setSeconds();
  StartTimer();
}

//initiates the seconds counter
function setSeconds() {
  Seconds = 10;
}

function TimesUp() {
  setSeconds();
  testenRadio();   //this function is called to register the not given answer
  AjaxRequest(datei, table, levelID);
  StartTimer();
  //document.getElementById('Timer').innerHTML = 'Deine Zeit ist leider vorbei!';
}

function StartTimer(){
  Interval = window.setInterval('DownTime()',1000);
  document.getElementById('TimerText').style.display = 'inline';
}

function FinishTimer() {  
  window.clearInterval(Interval);
  document.getElementById('Timer').innerHTML = '';
}

function DownTime(){
  var ss = Seconds % 60;
  if (ss<10){
    ss='0' + ss + '';
  }

  var mm = Math.floor(Seconds / 60);

  if (document.getElementById('Timer') == null){
    return;
  }

  document.getElementById('TimerText').innerHTML = mm + ':' + ss;
  if (Seconds < 1 && ans.count_all < ex_num){
    window.clearInterval(Interval);
    TimesUp();
    //Interval = null;
  }
  Seconds--;
}