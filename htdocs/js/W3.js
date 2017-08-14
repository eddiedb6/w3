//
// Logger
//

function W3LogDebug(msg) {
    if (w3LogLevel <= w3LogDebug) {
        console.log("[W3 Debug]" + msg);
    }
}

function W3LogInfo(msg) {
    if (w3LogLevel <= w3LogInfo) {
        console.log("[W3 Info]" + msg);
    }
}

function W3LogWarning(msg) {
    if (w3LogLevel <= w3LogWarning) {
        console.log("[W3 Warning]" + msg);
    }
}

function W3LogError(msg) {
    if (w3LogLevel <= w3LogError) {
        console.log("[W3 Error]" + msg);
    }
}

function W3LogFatal(msg) {
    if (w3LogLevel <= w3LogFatal) {
        console.log("[W3 Fatal]" + msg);
    }
}

//
// API 
//

function W3CreateAPI(uid) {
    if (!w3UI[uid].hasOwnProperty(w3PropApi))
    {
	W3LogError("There is no API property defined to create API for uid: " + uid);
	return "";
    }

    // w3ApiID is mandatory for w3PropApi
    var apiBinding = w3UI[uid][w3PropApi];
    var apiDef = w3API[apiBinding[w3ApiID]];
    var api = apiDef[w3ApiName];
    
    var len = W3GetAPIParamCount(apiBinding[w3ApiID]);

    if (len < 1) {
	return api;
    }

    api += "?";

    inputParamLen = 0;
    if (apiBinding.hasOwnProperty(w3ApiParams)) {
	inputParamLen = apiBinding[w3ApiParams].length;
    }

    if (len != inputParamLen) {
	W3LogWarning("The api binding does not match exactly to api def: " + uid);
    }
    
    for (var i = 0; i < len; ++i) {
	if (i < inputParamLen) {
	    var paramValueUI = apiBinding[w3ApiParams][i][w3ApiDataValue];
	    api += apiDef[w3ApiParams][i][w3ApiDataValue] + "=" + W3GetAPIParamValue(apiBinding[w3ApiParams][i]);
	} else {
	    api += apiDef[w3ApiParams][i][w3ApiDataValue] + "=";
	}

	if (i != len - 1) {
	    api += "&";
	}
    }
    
    return api;
}

//
// Language 
//

function W3GetLanguage() {
    return w3LanEnglish; // TODO, need to handle language selection
}

function W3GetStringValue(sid) {
    return w3Lan[W3GetLanguage()][sid]
}

//
// Event
//

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

function W3Submit(uid) {
    var request = W3CreateAPI(uid);
    if (request == "") {
	W3LogWarning("Nothing to submit");
	return;
    }

    W3LogDebug("Submit: " + request);
    
    $.get(request, function(data, status) {
	alert("data: " + data);
    });
}

function W3GoBack() {
    javascript:history.back(-1);
}

//
// UI
//

function W3CreateCanvas(uid) {
    return "<canvas id='" + uid + "'></canvas>";
}

function W3DisplayUI(uid) {
    $("#" + uid).css("display", "block");
}

function W3HideUI(uid) {
    $("#" + uid).css("display", "none");
}

function W3UpdateTableByAPI(uidSender, uidSinker) {
    // w3PropType is mandatory for UI
    if (w3UI[uidSinker][w3PropType] != w3TypeTable) {
	W3LogError("Sinker is not table when update table by API for uid: " + uidSinker);
	return;
    }

    if (!w3UI[uidSinker].hasOwnProperty(w3PropApi))
    {
	W3LogError("There is no API property defined to update table for uid: " + uidSinker);
	return;
    }

    if (!w3UI[uidSinker][w3PropApi].hasOwnProperty(w3ApiResult)) {
	W3LogError("No API result binding defined for " + uidSinker);
	return;
    }

    if (!w3UI[uidSinker][w3PropApi][w3ApiResult].hasOwnProperty(w3ApiResultData)) {
	W3LogError("No API result data binding defined for " + uidSinker);
	return;
    }

    var request = W3CreateAPI(uidSender);
    if (request == "") {
	return;
    }

    W3LogDebug("Update Table by API: " + request);
    
    $.get(request, function(data, status) {
	W3LogDebug("data: " + data);
	W3LogDebug("status: " + status);
	var result = eval("(" + data + ")")[w3ApiResultData];

	var isThereTableHeader = W3IsThereTableHeader(uidSinker);
	if (isThereTableHeader) {
	    $("#" + uidSinker + " tr:not(:first)").remove();
	} else {
	    $("#" + uidSinker).empty();
	}

	var apiResultBinding = w3UI[uidSinker][w3PropApi][w3ApiResult][w3ApiResultData];

	for (var rowIndex in result) {
	    var rowData = "<tr>";
	    for (var columnIndex in  apiResultBinding) {
		var resultField = apiResultBinding[columnIndex][w3ApiDataValue];
		var columnDataType = apiResultBinding[columnIndex][w3ApiDataType];

		if (columnDataType == w3ApiDataTypeSID) {
		    rowData += "<td>" + W3GetStringValue(result[rowIndex][resultField]) + "</td>";
		} else {
		    var columnElementHeader = "<td";
		    var columnElementValue = result[rowIndex][resultField];

		    if (isThereTableHeader) {
			var uidTableHeader = w3UI[uidSinker][w3PropSubUI][0][columnIndex];

			if (w3UI[uidTableHeader].hasOwnProperty(w3PropCSS)) {
			    columnElementHeader += " style='";
			    for (var key in w3UI[uidTableHeader][w3PropCSS]) {
				columnElementHeader += key + ":" + w3UI[uidTableHeader][w3PropCSS][key] + ";";
			    }
			    columnElementHeader += "'";
			}

			if (w3UI[uidTableHeader].hasOwnProperty(w3PropFunc)) {
			    if (w3UI[uidTableHeader][w3PropFunc].hasOwnProperty(w3FuncCreator)) {
				columnElementValue = W3FormatTableValue(columnElementValue, w3UI[uidTableHeader][w3PropFunc][w3FuncCreator]);
			    }
			}
		    } 

		    rowData += columnElementHeader + ">" + columnElementValue + "</td>";
		}
	    }
	    rowData += "</tr>";
	    $("#" + uidSinker + " tr:last").after(rowData);
	}
    });
}

//
// Formatter
//

function W3FormatDatetime(datetime, format) {
    // TODO: do real format for datetime
    return datetime.split(" ")[0];
}

function W3FormatTableValue(value, formatter) {
    // TODO: do real format calld
    var index = formatter.indexOf("(");
    if (index < 0) {
	W3LogError("Formatter name not found: " + formatter);
	return value;
    }
    
    var formatterName = formatter.substr(0, index);
    
    var paramStr = formatter.substr(index + 1);
    index = paramStr.indexOf(")");
    if (index < 0) {
	W3LogError("Formatter parameters not match: " + formatter);
	return value;
    }
    paramStr = paramStr.substr(0, index);
    var paramters = paramStr.split(",");

    var func = eval(formatterName);
    return func(value, paramters[1]);
}

//
// Graphic
//

function W3DrawPercentageReport(uid, percentage, text, padding) {
    // TODO
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
