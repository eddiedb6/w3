//
// API Helper
//

function W3GetAPIParamValue(apiInputParam) {
    if (apiInputParam[w3ApiDataType] == w3ApiDataTypeUID) {
	return W3GetUIValue(apiInputParam[w3ApiDataValue]);
    } else if (apiInputParam[w3ApiDataType] == w3ApiDataTypeString) {
	return apiInputParam[w3ApiDataValue];
    } else if (apiInputParam[w3ApiDataType] == w3ApiDataTypeNum) {
	return Number(apiInputParam[w3ApiDataValue]);
    }

    W3LogFatal("Invalid API param data type: " + apiInputParam[w3ApiDataType]);
    
    return "";
}

function W3GetAPIParamCount(aid) {
    if (!w3API[aid].hasOwnProperty(w3ApiParams)) {
	return 0;
    }
	
    return w3API[aid][w3ApiParams].length;
}

//
// UI Helper
//

function W3GetUIValue(uid) {
    return $("#" + uid).val();
}

function W3IsThereTableHeader(uid) {
    if (w3UI[uid][w3PropType] != w3TypeTable) {
	W3LogError("UI is not table and could not check table header property: " + uid);
	return false;
    }

    if (!w3UI[uid].hasOwnProperty(w3PropSubUI)) {
	return false;
    }

    if (w3UI[uid][w3PropSubUI].length <= 0) {
	return false;
    }

    if (w3UI[uid][w3PropSubUI][0].length <= 0) {
	return false;
    }

    return true;
}

//
// Formatter
//

function W3FormatValue(value, formatter) {
    var index = formatter.indexOf("(");
    if (index < 0) {
	W3LogError("Formatter name not found: " + formatter);
	return value;
    }
    
    var formatterName = formatter.substr(0, index).trim();
    var formatterFunc = eval(formatterName);
    if (formatterFunc == null || formatterFunc == undefined || formatterFunc == "") {
	W3LogError("Formatter could not be found: " + formatterName);
	return value;
    }
    
    var paramStr = formatter.substr(index + 1);
    index = paramStr.indexOf(")");
    if (index < 0) {
	W3LogError("Formatter parameters not match: " + formatter);
	return value;
    }
    paramStr = paramStr.substr(0, index);
    
    var parameters = paramStr.split(",");
    for (var paramIndex in parameters) {
	parameters[paramIndex] = parameters[paramIndex].trim();
	
	if (parameters[paramIndex] == w3PlaceHolder_1) {
	    parameters[paramIndex] = value;
	}
    }

    return formatterFunc.apply(this, parameters);
}
