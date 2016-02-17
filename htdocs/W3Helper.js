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
