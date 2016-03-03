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
	var paramValueUI = w3UI[uid][w3PropApi][paramIndex];
	api += apiDef[paramIndex] + "=" + W3GetUIValue(paramValueUI);

	if (i != len) {
	    api += "&";
	}
    }
    
    return api;
}

function W3GetParamNameFromIndex(i) {
    return "param" + i;
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

//
// Event Helper
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
