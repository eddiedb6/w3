//
// Language Helper
//

function W3GetLanguage() {
    return w3LanEnglish; // [ED]PENDING: Need to handle language selection
}

//
// API Helper
//

function W3GetAPIDef(aid) {
    if (!w3API.hasOwnProperty(aid)) {
	W3LogError("No aid defined: " + aid);
	return null;
    }

    return w3API[aid];
}

function W3GetAPIParamValue(apiInputParam) {
    var paramType = apiInputParam[w3ApiDataType];
    var paramValue = apiInputParam[w3ApiDataValue];
    if (paramType == w3ApiDataTypeUID) {
	return W3GetUIValue(paramValue);
    } else if (paramType == w3ApiDataTypeString) {
	return paramValue;
    } else if (paramType == w3ApiDataTypeNum) {
	return Number(paramValue);
    } else if (paramType == w3ApiDataTypeVar) {
	return W3GetVariable(paramValue);
    }

    W3LogError("Invalid API param data type: " + paramType);
    
    return "";
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

function W3CreateAPIFromUI(uid, index) {
    var apiTriggers = W3TryGetUIProperty(uid, w3PropTriggerApi);
    if (apiTriggers == null) {
	W3LogError("There is no API trigger defined to create API for uid: " + uid);
	return "";
    }
    if (index < 0 || index >= apiTriggers.length) {
	W3LogError("The API trigger index is overflow for uid: " + uid);
	return "";
    }

    var apiTrigger = apiTriggers[index];
    var aid = apiTrigger[w3ApiID];
    var apiDef = W3GetAPIDef(aid);
    if (apiDef == null) {
	return "";
    }
    
    var api = apiDef[w3ApiName];
    var len = W3GetAPIParamCount(aid);
    if (len < 1) {
	return api;
    }

    api += "?";

    inputParamLen = 0;
    if (apiTrigger.hasOwnProperty(w3ApiParams)) {
	inputParamLen = apiTrigger[w3ApiParams].length;
    }

    if (len != inputParamLen) {
	W3LogWarning("The api binding does not match exactly to api def: " + uid);
    }
    
    for (var i = 0; i < len; ++i) {
	if (i < inputParamLen) {
	    var paramValueUI = apiTrigger[w3ApiParams][i][w3ApiDataValue];
	    api += apiDef[w3ApiParams][i][w3ApiDataValue] + "=" + W3GetAPIParamValue(apiTrigger[w3ApiParams][i]);
	} else {
	    api += apiDef[w3ApiParams][i][w3ApiDataValue] + "=";
	}

	if (i != len - 1) {
	    api += "&";
	}
    }
    
    return api;
}

function W3OnAPIDefaultListener(data, status) {
    alert("data: " + data + "\nstatus: " + status);
}

//
// UI Helper
//

function W3GetUIDef(uid) {
    if (!w3UI.hasOwnProperty(uid)) {
	W3LogError("No uid defined: " + uid);
	return null;
    }
    
    return w3UI[uid];
}

function W3SetUIText(uid, text) {
    $("#" + uid).text(text);    
}

function W3SetUICSS(uid, css) {
    for (var key in css) {
	$("#" + uid).css(key, css[key]);
    }
}

function W3UpdateTableCellDisplay(uid, value, valueType) {
    var ui = W3GetUIDef(uid);
    if (ui == null) {
	return;
    }

    var valueArray = W3ProcessUIValue(uid, value);
     
    // Update value
    var uiType = ui[w3PropType];
    if (uiType == w3TypeLabel) {
	W3SetUIText(uid, valueArray[0]);
    } else if (uiType == w3TypeCanvasPanel) {
	W3DrawCanvasPanel(uid, valueArray[0]);
    }

    // Update css
    W3SetUICSS(uid, valueArray[1]);
}

//
// UI Property Helper
//

function W3TryGetProcessorFunc(uid) {
    var propFunc = W3TryGetUIProperty(uid, w3PropFunc);
    if (propFunc != null) {
	if (propFunc.hasOwnProperty(w3FuncProcessor)) {
	    return propFunc[w3FuncProcessor];
	}
    }

    return null;
}

function W3IsUIPropertySupported(uid, property) {
    var ui = W3GetUIDef(uid);
    if (ui == null) {
	return false;
    }
    
    if (ui.hasOwnProperty(property)) {
	return true;
    }

    if (ui.hasOwnProperty(w3PropPrototype)) {
	var uidPrototype = ui[w3PropPrototype];
	return W3IsUIPropertySupported(uidPrototype, property);
    }

    return false;
}

function W3TryGetUIProperty(uid, property) {
    var ui = W3GetUIDef(uid);
    if (ui == null) {
	return null;
    }

    if (ui.hasOwnProperty(property)) {
	return ui[property];
    }

    if (ui.hasOwnProperty(w3PropPrototype)) {
	var uidPrototype = ui[w3PropPrototype];
	return W3TryGetUIProperty(uidPrototype, property);
    }

    return null;
}

//
// UI Table Helper
//

function W3IsThereTableHeader(uid) {
    var uiTable = W3GetUIDef(uid);
    if (uiTable == null) {
	return false;
    }
    
    if (uiTable[w3PropType] != w3TypeTable) {
	W3LogError("UI is not table and could not check table header property: " + uid);
	return false;
    }

    var uiSub = W3TryGetUIProperty(uid, w3PropSubUI);
    if (uiSub == null) {
	return false;
    }
    
    if (uiSub.length <= 0 || uiSub[0].length <= 0) {
	return false;
    }

    return true;
}

function W3UpdateTableCell(uidTable, rowIndex, columnIndex, value, valueType) {
    var cellsDef = W3TryGetUIProperty(uidTable, w3PropSubUI);
    if (cellsDef == null) {
	W3LogError("No cells defined in table: " + uidTable);
	return;
    }
 
    if (rowIndex >= cellsDef.length) {
	W3LogError("Row index overflow in table: " + uidTable + ", " + rowIndex.toString());
	return;
    }
    if (columnIndex >= (cellsDef[rowIndex]).length) {
	W3LogError("Column index overflow in table: " + uidTable + ", " + columnIndex.toString());
	return;
    }

    W3UpdateTableCellDisplay(cellsDef[rowIndex][columnIndex], value, valueType);
}

function W3UpdateTableByMatrix(uidTable, data, status) {
    var apiResult = eval("(" + data + ")")[w3ApiResultData];

    var apiBinding = W3TryGetUIProperty(uidTable, w3PropSinkApi);
    if (apiBinding == null) {
	return;
    }
    
    var bindingMatrix = apiBinding[w3SinkMatrix];
    for (var rowIndex in bindingMatrix) {
	for (var columnIndex in bindingMatrix[rowIndex]) {
	    var binding = bindingMatrix[rowIndex][columnIndex];
	    if (binding[w3ApiDataType] == w3ApiDataTypeNone) {
		// No binding for this cell
		continue;
	    }

	    if (!apiResult.hasOwnProperty(binding[w3ApiDataValue])) {
		W3LogError("There is no such filed in result: " + binding[w3ApiDataValue]);
		return;
	    }

	    W3UpdateTableCell(uidTable, parseInt(rowIndex) + 1, parseInt(columnIndex), apiResult[binding[w3ApiDataValue]], apiResult[binding[w3ApiDataType]]);
	}
    }
}
    
function W3UpdateTableByRow(uidTable, data, status) {
    var apiResult = eval("(" + data + ")")[w3ApiResultData];

    var isThereTableHeader = W3IsThereTableHeader(uidTable);
    if (isThereTableHeader) {
	$("#" + uidTable + " tr:not(:first)").remove();
    } else {
	$("#" + uidTable).empty();
    }

    var apiBinding = W3TryGetUIProperty(uidTable, w3PropSinkApi);
    if (apiBinding == null) {
	return;
    }
    
    var apiResultBinding = apiBinding[w3SinkRow];
    var tableBody = "";
    for (var rowIndex in apiResult) {
        var rowData = "<tr>";
	for (var columnIndex in  apiResultBinding) {
	    var resultField = apiResultBinding[columnIndex][w3ApiDataValue];
	    var columnDataType = apiResultBinding[columnIndex][w3ApiDataType];

	    var columnElementHeader = "<td";
	    var columnElementValue = apiResult[rowIndex][resultField];
	    if (columnDataType == w3ApiDataTypeSID) {
		columnElementValue = W3GetStringValue(apiResult[rowIndex][resultField]);
	    }

	    if (isThereTableHeader) {
		var cellsDef = W3TryGetUIProperty(uidTable, w3PropSubUI);
		if (cellsDef == null) {
		    W3LogError("No table cell defined: " + uidTable);
		    return;
		}
		
		var uidTableHeader = cellsDef[0][columnIndex];
		var uiTableHeader = W3GetUIDef(uidTableHeader);
		if (uiTableHeader == null) {
		    return;
		}
		
		if (uiTableHeader[w3PropType] == w3TypeTableHeader) {
		    // Apply header CSS
		    var headerCSS = {};
		    var cssDef = W3TryGetUIProperty(uidTableHeader, w3PropCSS);
		    if (cssDef != null) {
			for (var key in cssDef) {
			    headerCSS[key] = cssDef[key];
			}
		    }

		    // Apply header func
		    var paramArray = [columnElementValue, headerCSS];
		    var formatters = W3TryGetProcessorFunc(uidTableHeader);
		    if (formatters != null) {
			for (var formatterIndex in formatters) {
			    paramArray = W3ExecuteFuncFromString(formatters[formatterIndex], paramArray);
			}
		    }
		    columnElementValue = paramArray[0];
		    headerCSS = paramArray[1];
		    
		    columnElementHeader += " style='"
		    for (var key in headerCSS) {
			columnElementHeader += key + ":" + headerCSS[key] + ";";
		    }
		    columnElementHeader += "' ";
		}
	    }
	    
	    rowData += columnElementHeader + ">" + columnElementValue + "</td>";
	} // End of column handling

	rowData += "</tr>";
	tableBody += rowData;
    } // End of row handling
    
    $("#" + uidTable + " tr:last").after(tableBody);
}

//
// Canvas Helper
//

function W3DrawCanvasPanel(uid, drawFunc) {
    var uidCanvas = uid + "Canvas";
    
    $("#" + uid).empty();
    $("#" + uid).append(W3CreateCanvas(uidCanvas));

    W3ExecuteFuncFromString(drawFunc, uidCanvas);
}

function W3CreateCanvas(uid) {
    return "<canvas id='" + uid + "'></canvas>";
}

//
// Function Helper
//

function W3ExecuteFuncFromString(func) {
    var args = Array.prototype.slice.call(arguments, 1);
    
    var index = func.indexOf("(");
    if (index < 0) {
	W3LogError("Function name not found: " + func);
	return null;
    }
    
    var funcName = func.substr(0, index).trim();
    var funcObj = eval(funcName);
    if (funcObj == null || funcObj == undefined || funcObj == "") {
	W3LogError("Function could not be found: " + funcName);
	return null;
    }
    
    var paramStr = func.substr(index + 1);
    index = paramStr.indexOf(")");
    if (index < 0) {
	W3LogError("Function parameters not match: " + func);
	return null;
    }
    paramStr = paramStr.substr(0, index);

    var placeholderPattern = new RegExp("w3PlaceHolder_([1-9]{1})");
    var parameters = paramStr.split(",");
    for (var paramIndex in parameters) {
	parameters[paramIndex] = parameters[paramIndex].trim();

	var regResult = placeholderPattern.exec(parameters[paramIndex]);
	if (regResult != null) {
	    // Replace place holder here
	    var funcParamIndex = parseInt(regResult[1], 10);
	    if (funcParamIndex > args.length) {
		W3LogError("Function actual paramter number is less than " + regResult[i]);
		return null;
	    }

	    parameters[paramIndex] = args[funcParamIndex - 1];
	}
    }

    return funcObj.apply(this, parameters);
}

function W3ProcessUIValue(uid, value) {
    var paramArray = [value, {}];
    var processors = W3TryGetProcessorFunc(uid);
    if (processors != null) {
	for (var processorIndex in processors) {
	    paramArray = W3ExecuteFuncFromString(processors[processorIndex], paramArray);
	}
    }

    return paramArray;
}
