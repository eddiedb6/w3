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
// Language 
//

function W3GetStringValue(sid) {
    var language = w3Lan[W3GetLanguage()];
    if (!language.hasOwnProperty(sid)) {
	W3LogError("No sid defined: " + sid);
	return "";
    }
    
    return language[sid];
}

//
// API
//

function W3CreateAPI() {
    var argLen = arguments.length;
    if (argLen <= 0) {
	W3LogError("There is no API parameters");
	return "";
    }

    var aid = arguments[0];
    var apiDef = W3GetAPIDef(aid);
    if (apiDef == null) {
	return "";
    }
    
    var api = apiDef[w3ApiName];
    var len = W3GetAPIParamCount(aid);
    if (len + 1 != argLen) {
	W3LogError("API parameters do not match: " + aid);
	return "";
    }
    
    if (len < 1) {
	return api;
    }

    api += "?";

    for (var i = 0; i < len; ++i) {
	api += apiDef[w3ApiParams][i][w3ApiDataValue] + "=" + arguments[i + 1];
	if (i != len - 1) {
	    api += "&";
	}
    }
    
    return api;
}

//
// Event
//

function W3OnTabClicked(uid, currentTab, tabSize) {
    for (i = 1; i <= tabSize; ++i) {
	var display = i == currentTab ? "block" : "none";
	W3SetUICSS(uid + "content" + i.toString(), {"display": display});

	var borderStyle = "solid";
	var borderWidth = "1px";
	var bgColor = "white";
	var cssDef = W3TryGetUIProperty(uid, w3PropCSS);
	if (cssDef != null) {
	    if (cssDef.hasOwnProperty("border-style")) {
		borderStyle = cssDef["border-style"];
	    }
	    if (cssDef.hasOwnProperty("border-width")) {
		borderWidth = cssDef["border-width"];
	    }
	    if (cssDef.hasOwnProperty("background-color")) {
		bgColor = cssDef["background-color"];
	    }
	}

	var uidHeader = uid + "header" + i.toString();
	var css = {};
	if (i == currentTab) {
	    var style = borderWidth + " " + borderStyle;
	    var bottomStyle = style + " " + bgColor;
	    css["border-bottom"] = bottomStyle;
	    css["border-top"] = style;
	    css["border-left"] = style;
	    css["border-right"] = style;
	} else {
	    css["border"] = "none";
	}
	W3SetUICSS(uidHeader, css);
    }
}

function W3TriggerAPIFromUI(uid) {
    var apiTrigger = W3TryGetUIProperty(uid, w3PropTriggerApi);
    if (apiTrigger == null) {
	W3LogError("No API trigger defined for ui: " + uid);
	return;
    }
    
    var apiDef = W3GetAPIDef(apiTrigger[w3ApiID]);
    if (apiDef == null) {
	return;
    }

    var request = W3CreateAPIFromUI(uid);
    if (request == "") {
	W3LogWarning("No API to create from UI:  " + uid);
	return;
    }

    W3LogDebug("Trigger API: " + request);
    
    var api = apiDef[w3ApiName];

    var listeners = [];
    if (apiDef.hasOwnProperty(w3ApiListener) &&
	apiDef[w3ApiListener].length > 0) {
	for (var index in apiDef[w3ApiListener])
	{
	    listeners.push(apiDef[w3ApiListener][index]);
	}
    } else {
	listeners.push("W3OnAPIDefaultListener(w3PlaceHolder_1, w3PlaceHolder_2)");
    }
    
    $.get(request, function(data, status) {
	W3LogDebug("status: " + status);
	W3LogDebug("data: " + data);
	
	for (var index in listeners) {
	    W3ExecuteFuncFromString(listeners[index], data, status);
	}		    
    });
}

function W3GoBack() {
    javascript:history.back(-1);
}

//
// UI
//

function W3DisplayUI(uid) {
    $("#" + uid).css("display", "block");
}

function W3HideUI(uid) {
    $("#" + uid).css("display", "none");
}

function W3UpdateTable(uidTable, data, status) {
    var apiResult = eval("(" + data + ")")[w3ApiResultStatus];
    if (apiResult != w3ApiResultSuccessful) {
	W3LogWarning("Update table failed");
	return;
    }
    
    var tableDef = W3GetUIDef(uidTable);
    if (tableDef == null)  {
	return;
    }
    
    if (tableDef[w3PropType] != w3TypeTable) {
	W3LogError("Sinker is not table when update table by API: " + uidTable)
	return;
    }

    var bindingDef = W3TryGetUIProperty(uidTable, w3PropBindingApi);
    if (bindingDef == null) {
	W3LogError("There is no API property defined to update table for uid: " + uidTable);
	return;
    }

    // Check binding style manually
    if (bindingDef.hasOwnProperty(w3BindingRow)) {
	W3UpdateTableByRow(uidTable, data, status);
    } else if (bindingDef.hasOwnProperty(w3BindingMatrix)) {
	W3UpdateTableByMatrix(uidTable, data, status);
    } else {
	W3LogError("No binding style for " + uidTable);
    }
}

//
// Formatter
// 
// Formatter is one type of processor
// All processor accept the first param as [value, {key: css}]
// And return result as [value, {key: css}]
//

function W3FormatCurrency(paramArray) {
    var currencyNum = paramArray[0];

    return [currencyNum.toFixed(2).toString(), paramArray[1]];
}

function W3FormatCurrencyColor(paramArray) {
    var currencyNum = paramArray[0];
    var css = paramArray[1];

    var color = "";
    if (typeof currencyNum == "string") {
	if (currencyNum.indexOf("-") == 0) {
	    color = "red";
	} else {
	    color = "green";
	}
    } else if (typeof currencyNum == "number") {
	if (currencyNum < 0) {
	    color = "red";
	} else {
	    color = "green";
	}	
    } else {
	W3LogError("Currency data format is not expected: " + typeof currencyNum);
    }

    if (color.length > 0) {
	css["color"] = color;
    }

    return [currencyNum, css];
}

function W3FormatDatetime(paramArray, format) {
    var datetime = paramArray[0];
    
    // w3DatetimeFormat = "YYYY-MM-DD HH:MM:SS"
    var pattern = new RegExp("^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$");
    var result = pattern.exec(datetime);
    if (result == null) {
	W3LogError("It's not W3 datetime format: " + datetime);
	return [datetime, paramArray[1]];
    }

    var formatArray = format.split(" ");
    if (formatArray.length <= 0 || formatArray.length > 2) {
	W3LogError("Unexpected datetime format required: " + format);
	return [datetime, paramArray[1]];
    }

    var date = formatArray[0];
    date = date.replace(/yyyy/i, result[1]);
    date = date.replace(/mm/i, result[2]);
    date = date.replace(/dd/i, result[3]);

    var resultDatetime = date;
    
    if (formatArray.length > 1) {
	var time = formatArray[1];
	time = time.replace(/hh/i, result[4]);
	time = time.replace(/mm/i, result[5]);
	time = time.replace(/ss/i, result[6]);

	resultDatetime += " " + time;
    }

    return [resultDatetime, paramArray[1]];	
}

//
// Graphic
//

function W3DrawPercentageReport(uid, percentage, text, padding) {
    if (percentage.length != text.length) {
	W3LogError("Percentage data and text do not match!");
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

//
// Variable
//

function W3SetVariable(variable, value) {
    variable[w3VariableValue] = value;

    for (var uidListener in variable[w3VariableListeners]) {
	var uiDef = W3GetUIDef(uidListener);
	if (uiDef[w3PropType] == w3TypeText || uiDef[w3PropType] == w3TypeLabel) {
	    var varStr = value.toString();
	    var format = variable[w3VariableListeners][uidListener];
	    if (format != "") {
		if (format[0] == "F") {
		    var fixNum = parseInt(format.substring(1));
		    varStr = value.toFixed(fixNum).toString();
		} else {
		    W3LogWarning("Variable format is not supported yet: " + format);
		}
	    }
	    W3SetUIText(uidListener, varStr);
	} else {
	    W3LogWarning("UI type is not supported for variable binding: " + uiDef[w3PropType]);
	}
    }
}

function W3GetVariable(variable) {
    return variable[w3VariableValue];
}
