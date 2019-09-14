function AjaxSaving(datei, text, hits, items, level) {
  var xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("ajax_response").innerHTML = this.responseText;
    }
  };

  xhttp.open("POST", "/eoi/php/" + datei, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("txt=" + text + "&hts=" + hits + "&itms=" + items + "&lvl=" + level);
}