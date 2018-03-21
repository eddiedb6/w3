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

function W3GetLanguage() {
    return w3LanEnglish; // [ED]PENDING: Need to handle language selection
}

function W3GetStringValue(str) {
    var language = w3Lan[W3GetLanguage()];
    var sid = str;
    if (!language.hasOwnProperty(sid)) {
	var type = Object.prototype.toString.call(str);
	if ( type == "[object String]") {
	    return str;
	}

	W3LogError("No sid defined: " + sid);
	return "";
    }
    
    return language[sid];
}

//
// API
//

function W3GetAPIDef(aid) {
    if (!w3API.hasOwnProperty(aid)) {
	W3LogError("No aid defined: " + aid);
	return null;
    }

    return w3API[aid];
}

function W3GetAPIParamCount(aid) {
    var apiDef = W3GetAPIDef(aid);
    if (apiDef == null) {
	return 0;
    }
    
    if (!apiDef.hasOwnProperty(w3ApiParams)) {
	return 0;
    }
	
    return apiDef[w3ApiParams].length;
}

function W3CreateAPI() {
    // Variable parameter length function
    
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

function W3CallAPI(request) {
    W3LogDebug("Trigger API Directly: " + request);
    location.href = request
}

function W3CallAPIAsync(request, callback) {
    W3LogDebug("Trigger API Async: " + request);
    var innerCallback = function(data, status) {
	if (W3OnAPICallback(data, status)) {
	    callback(data, status);
	}
    };
    $.get(request, innerCallback);
}

function W3CallAPISync(request, callback) {
    W3LogDebug("Trigger API Sync: " + request);
    var innerCallback = function(data, status) {
	if (W3OnAPICallback(data, status)) {
	    callback(data, status);
	}
    };

    $.ajax({
	type: "get",
	url: request,
	data: "",
	async: false,
	success: innerCallback
    });
}

function W3PostAPIAsync(request, callback) {
    W3LogDebug("Post API Async: " + request);
    var innerCallback = function(data, status) {
	if (W3OnAPICallback(data, status)) {
	    callback(data, status);
	}
    };
    $.post(request, innerCallback);
}

//
// UI
//

function W3GetUIDef(ui) {
    var uid = w3UIDUndefined;
    var type = Object.prototype.toString.call(ui);
    if ( type == "[object String]") {
	uid = ui;
    } else {
	return ui;
    }
    
    if (!w3UI.hasOwnProperty(uid)) {
	W3LogWarning("No uid defined: " + uid);
	return null;
    }
    
    return w3UI[uid];
}

function W3GetUIText(uid) {
    var text = "";
    var uiDef = W3GetUIDef(uid);
    if (uiDef != null) {
	var uiType = uiDef[w3PropType];
	if (uiType == w3TypeTextEditor) {
	    text = W3Encode($("#" + uid).val());
	} else if (uiType == w3TypeDisplayPanel) {
	    text = W3Encode($("#" + uid).html());
	} else if (uiType == w3TypePanel) {
	    text = $("#" + uid).html();
	} else if (uiType == w3TypeText ||
		   uiType == w3TypePassword ||
		   uiType == w3TypeCombobox ||
		   uiType == w3TypeDatePicker ||
		   uiType == w3TypeMonthPicker) {
	    text = $("#" + uid).val();
	} else {
	    text = $("#" + uid).text();
	}
    } else {
	text = $("#" + uid).text();
    }

    return text;
}

function W3SetUIText(uid, text) {
    var uiDef = W3GetUIDef(uid);
    if (uiDef == null) {
	return;
    }

    var uiType = uiDef[w3PropType];
    if (uiType == w3TypeTextEditor) {
	text = W3Decode(text);
	$("#" + uid).jqteVal(text);
    } else if (uiType == w3TypeText) {
	$("#" + uid).val(text);
    } else if (uiType == w3TypeDisplayPanel) {
	text = W3Decode(text);
	$("#" + uid).html(text);
    } else if (uiType == w3TypePanel) {
	$("#" + uid).html(text);
    } else {
	$("#" + uid).text(text);
    }
}

function W3TryGetUIProperty(ui, property) {
    var uiDef = W3GetUIDef(ui);

    if (uiDef == null) {
	return null;
    }

    if (uiDef.hasOwnProperty(property)) {
	return uiDef[property];
    }

    if (uiDef.hasOwnProperty(w3PropPrototype)) {
	var uidPrototype = uiDef[w3PropPrototype];
	return W3TryGetUIProperty(uidPrototype, property);
    }

    return null;
}

function W3DisplayUI(uid) {
    $("#" + uid).css("display", "block");
}

function W3HideUI(uid) {
    $("#" + uid).css("display", "none");
}

function W3DisableUI(uid) {
    $("#" + uid).attr("disabled", true);
}

function W3EnableUI(uid) {
    $("#" + uid).attr("disabled", false);
}

//
// Formatter
// 

// Formatter is one type of processor
// All processor accept the first param as [value, {key: css}, [row, column]]
// And return result as [value, {key: css}, [row, column]]
//

function W3FormatCurrency(paramArray) {
    var currencyNum = paramArray[0];

    return [currencyNum.toFixed(2).toString(), paramArray[1], paramArray[2]];
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

    return [currencyNum, css, paramArray[2]];
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

    return [resultDatetime, paramArray[1], paramArray[2]];	
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

    for (var index in variable[w3VariableListeners]) {
	var uidListener = variable[w3VariableListeners][index];
	var uiDef = W3GetUIDef(uidListener);
	if (uiDef == null) {
	    W3LogError("Could not get UI def from variable binding: " + uidListener);
	    continue;
	}

	var bindingDef = W3TryGetUIProperty(uiDef, w3PropBindingVar);
	if (bindingDef == null) {
	    W3LogError("Could not get binding from UI: " + uidListener);
	    continue;
	}

	var bindingType = null;
	if (bindingDef.hasOwnProperty(w3BindingType)) {
	    bindingType = bindingDef[w3BindingType];
	} else {
	    W3LogError("Could not get binding type from UI: " + uidListener);
	    continue;
	}

	if (bindingType == w3BindingUIDisplay) {
	    W3UpdateBindingUIDisplay(uidListener, bindingDef, value);
	} else {
	    W3LogError("Binding type is not defined: " + bindingType);
	    continue;
	}
    }
}

function W3GetVariable(variable) {
    return variable[w3VariableValue];
}

//
// Session
//

function W3GetSession() {
    return W3GetVariable(eval(w3Session));
}

//
// Code
//

function W3Encode(text) {
    // Convert html char
    text = text.replace(/&/g, "::;;");
    text = text.replace(/#/g, ";;::");

    // Convert URI char
    text = encodeURI(text);
    
    return text;
}

function W3Decode(text) {
    // Convert URI char
    text = decodeURI(text);
    
    // Convert html char
    text = text.replace(/::;;/g, "&");
    text = text.replace(/;;::/g, "#");

    return text;
}

//
// Other
//

function W3GoBack() {
    javascript:history.back(-1);
}

function W3Debug() {
    alert("Debug done");
}
