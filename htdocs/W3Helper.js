//
// Logger
//

function W3LogDebug(msg) {
    if (w3LogLevel >= w3LogDebug) {
        console.log("[W3 Debug]" + msg);
    }
}

function W3LogInfo(msg) {
    if (w3LogLevel >= w3LogInfo) {
        console.log("[W3 Info]" + msg);
    }
}

function W3LogWarning(msg) {
    if (w3LogLevel >= w3LogWarning) {
        console.log("[W3 Warning]" + msg);
    }
}

function W3LogError(msg) {
    if (w3LogLevel >= w3LogError) {
        console.log("[W3 Error]" + msg);
    }
}

function W3LogFatal(msg) {
    if (w3LogLevel >= w3LogFatal) {
        console.log("[W3 Fatal]" + msg);
    }
}

//
// API Helper
//

function W3CreateAPI(uid) {
    var apiDef = w3API[w3UI[uid][w3PropApi][w3ApiID]];
    var api = apiDef[w3ApiName];
    
    var len = W3GetAPIParamCount(w3UI[uid][w3PropApi][w3ApiID]);

    if (len < 1) {
	return api;
    }

    api += "?";
    
    for (var i = 1; i <= len; ++i) {
	var paramIndex = W3GetParamNameFromIndex(i);
	if (w3UI[uid][w3PropApi].hasOwnProperty(paramIndex)) {
	    var paramValueUI = w3UI[uid][w3PropApi][paramIndex];
	    api += apiDef[paramIndex][1] + "=" + W3GetUIValue(paramValueUI);
	} else {
	    api += apiDef[paramIndex][1] + "=";
	}

	if (i != len) {
	    api += "&";
	}
    }
    
    return api;
}

function W3GetParamNameFromIndex(i) {
    return "apiparam" + i;
}

function W3GetAPIParamCount(aid) {
    var count = Object.keys(w3API[aid]).length;

    if (w3API[aid].hasOwnProperty(w3ApiName)) {
	count -= 1;
    }
    if (w3API[aid].hasOwnProperty(w3ApiResult)) {
	count -= 1;
    }
    
    return count;
}

//
// UI Helper
//

function W3GetUIValue(uid) {
    return $("#" + uid).val();
}

function W3CreateCanvas(uid) {
    return "<canvas id='" + uid + "'></canvas>";
}

//
// Language Helper
//
function W3GetLanguage() {
    return w3LanEnglish; // TODO, need to handle language selection
}

//
// Event Helper
//

function W3DrawPercentageReport(uid, percentage, text, padding) {
    if (percentage.length != text.length) {
	throw Error("Percentage data and text do not match!");
	return;
    }
    
    var color = [
	"black",
	"#616D7E",
	"blue",
	"#3090C7",
	"#4E9258",
	"green",
	"orange",
	"brown",
	"coral",
	"red",
	"magenta",
	"purple",
	"#D2B9D3"
    ];

    var canvas = document.getElementById(uid);
    var canvasContex = canvas.getContext("2d");

    var radius = canvas.height / 2 - padding;
    var x = radius + padding;
    var y = radius + padding;

    var rectWidth = 15;
    var rectHeight = 10;
    var rectX = x * 2 + padding;
    var rectY = padding;
    var textX = rectX + rectWidth + padding;
    var textY = rectY + rectHeight;

    var startAngle = 0;
    var endAngle = 0;

    var key = [];
    for (var i in percentage) {
	key.push(i);
    }
    key.sort(function (a, b) {
	if (percentage[a] > percentage[b]) {
	    return -1;
	}
	if (percentage[a] < percentage[b]) {
	    return 1;
	}
	return 0;
    });

    color.sort(function (a, b) {
	var rand = Math.round(Math.random() * 9);
	if (rand <=3 ) {
	    return -1;
	}
	if (rand >= 6) {
	    return 1;
	}
	return 0;
    });

    for (var i = 0; i < key.length; ++i) {
	// Check whether color is enough
	if (i == color.length) {
	    break;
	}
	
	endAngle = endAngle + percentage[key[i]] * Math.PI * 2;
	canvasContex.fillStyle = color[i];
	canvasContex.beginPath();
	canvasContex.moveTo(x, y);
	canvasContex.arc(x, y, radius, startAngle, endAngle, false);
	canvasContex.closePath();
	canvasContex.fill();
	startAngle = endAngle;


	canvasContex.fillRect(rectX, rectY, rectWidth, rectHeight);
	canvasContex.moveTo(rectX, rectY);
	canvasContex.font = 'bold 12px';
	var percent = text[key[i]] + ": " + (100 * percentage[key[i]]).toFixed(2) + "%";
	canvasContex.fillText(percent, textX, textY);
	rectY += rectHeight + padding;
	textY += rectHeight + padding;
    }
}

function W3SetTab(uid, currentTab, tabSize) {
    for (i = 1; i <= tabSize; ++i) {
	var display = i == currentTab ? "block" : "none";
	$("#" + uid + "content" + i.toString()).css("display", display);

	var borderStyle = "solid";
	var borderWidth = "1px";
	var bgColor = "white";
	if (w3UI[uid].hasOwnProperty(w3PropCSS)) {
	    if (w3UI[uid][w3PropCSS].hasOwnProperty("border-style")) {
		borderStyle = w3UI[uid][w3PropCSS]["border-style"];
	    }
	    if (w3UI[uid][w3PropCSS].hasOwnProperty("border-width")) {
		borderWidth = w3UI[uid][w3PropCSS]["border-width"];
	    }
	    if (w3UI[uid][w3PropCSS].hasOwnProperty("background-color")) {
		bgColor = w3UI[uid][w3PropCSS]["background-color"];
	    }
	}

	if (i == currentTab) {
	    var style = borderWidth + " " + borderStyle;
	    var bottomStyle = style + " " + bgColor;
	    $("#" + uid + "header" + i.toString()).css("border-bottom", bottomStyle);
	    $("#" + uid + "header" + i.toString()).css("border-top", style);
	    $("#" + uid + "header" + i.toString()).css("border-left", style);
	    $("#" + uid + "header" + i.toString()).css("border-right", style);
	} else {
	    $("#" + uid + "header" + i.toString()).css("border", "none");
	}
    }
}

function W3UpdateTable(uidSender, uidSinker, updater) {
    var request = W3CreateAPI(uidSender);
    $.get(request, function(data, status) {
	var result = eval("(" + data + ")")[w3ApiResultData];

	$("#" + uidSinker + " tr:not(:first)").remove();

	if (!w3UI[uidSinker].hasOwnProperty(w3PropApi)) {
	    W3LogError("No API binding defined for " + uidSinker);
	    return;
	}

	var data = new Array();
	var totalRow = result.length;
	for (var rowIndex in result) {
	    var rowData = "<tr>";
	    var totalColumn = w3UI[uidSinker][w3PropApi].length;
	    for (var columnIndex in  w3UI[uidSinker][w3PropApi]) {
		var column = w3UI[uidSinker][w3PropApi][columnIndex];
		if (typeof updater != 'undefined') {
		    rowData += updater(column, result[rowIndex][column],
				       rowIndex, totalRow,
				       columnIndex, totalColumn,
				       data);
		} else {
		    rowData += "<td>" + result[rowIndex][column] + "</td>";
		}
	    }
	    rowData += "</tr>";
	    $("#" + uidSinker + " tr:last").after(rowData);
	}
    });
}

function W3Submit(uid) {
    var request = W3CreateAPI(uid);
    $.get(request, function(data, status) {
	alert("data: " + data);
    });
}
