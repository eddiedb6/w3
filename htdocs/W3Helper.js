function W3CreateAPI(uid) {
    var apiDef = w3API[w3UI[uid][w3PropApi][w3ApiID]];
    var api = apiDef[w3ApiName];
    
    var len = Object.keys(apiDef).length;
    if (len <= 1) {
	return api;
    }

    api += "?";
    
    for (var i = 1; i < len; ++i) {
	var paramIndex = W3GetParamName(i);
	var paramValueUI = w3UI[uid][w3PropApi][paramIndex];
	api += apiDef[paramIndex] + "=" + W3GetUIValue(paramValueUI);

	if (i != len - 1) {
	    api += "&";
	}
    }
    
    return api;
}

function W3GetParamName(i) {
    return "param" + i;
}

function W3GetUIValue(uid) {
    return $("#" + uid).val();
}
