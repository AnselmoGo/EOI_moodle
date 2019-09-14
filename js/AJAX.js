function getXMLHttp() {
	var xmlHttp;
	try {
		xmlHttp = new XMLHttpRequest();
	} catch(e) {
		//Internet Explorer is different that the others
		try {
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
			try {
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(e) {
				alert("Old browser? Upgrade today so you can use AJAX!");
				return false;
			}
		}
	}
	return xmlHttp;	
}

function AjaxRequest(datei, table, level) {
	var xmlHttp = getXMLHttp();

	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4) {
			HandleResponse(xmlHttp.responseText);
		}
	}
	xmlHttp.open("GET", "/eoi/php/" + datei + "?tbl=" + table + "&lvl=" + level, true);
	xmlHttp.send(null);
}

function HandleResponse(response) {
	document.getElementById('ajax_response').innerHTML = response;
}