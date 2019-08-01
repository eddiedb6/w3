import sys
import os

import W3Const

sys.path.append(os.path.join(os.path.split(os.path.realpath(__file__))[0], "schema"))

import SchemaChecker

thisDir = os.path.split(os.path.realpath(__file__))[0]
                
def W3SchemaCheck(configPath):
    schemaPath = os.path.join(thisDir, "W3Schema.py")
    constPath = os.path.join(thisDir, "W3Const.py")
    checker = SchemaChecker.SchemaChecker(configPath, schemaPath, constPath)
    return checker.Check()

def W3ConstToPHP(const):
    if not isinstance(const, dict):
        return ""

    constValues = ""
    for k in const.keys():
        if k.startswith('w3'):
            if (isinstance(const[k], str)):
                constValues = constValues + "define('" + k + "', '" + const[k] + "');\n"
            else:
                constValues = constValues + "define('" + k + "', " + str(const[k]) + ");\n"

    return constValues;

def W3ConstToJS(const):
    if not isinstance(const, dict):
        return ""

    constValues = ""
    for k in const.keys():
        if k.startswith('w3'):
            if (isinstance(const[k], str)):
                constValues = constValues + "const " + k + " = \"" + const[k] + "\";\n"
            else:
                constValues = constValues + "const " + k + " = " + str(const[k]) + ";\n"

    return constValues;

def W3ValueToPHP(value, indent):
    if isinstance(value, dict):
        return W3DictToPHP(value, indent)
    elif isinstance(value, list) or isinstance(value, tuple):
        return W3ArrayToPHP(value, indent)
    else:
        return "\"" + value + "\""

def W3ValueToJS(value, indent):
    if isinstance(value, dict):
        return W3DictToJS(value, indent)
    elif isinstance(value, list) or isinstance(value, tuple):
        return W3ArrayToJS(value, indent)
    else:
        return "\"" + value + "\""

def W3ArrayToPHP(value, indent):
    indentString = "    " * indent
    phpValue = "array (\n"
    index = len(value)
    
    if (index == 0):
        phpValue = phpValue + indentString + ")"
        return phpValue
        
    for v in value:
        index = index - 1
        phpValue = phpValue + indentString + W3ValueToPHP(v, indent + 1)
        if index == 0:
            phpValue = phpValue + ")"
        else:
            phpValue = phpValue + ",\n"

    return phpValue

def W3ArrayToJS(value, indent):
    indentString = "    " * indent
    jsValue = "[\n"
    index = len(value)
    
    if (index == 0):
        jsValue = jsValue + indentString + "]"
        return jsValue
        
    for v in value:
        index = index - 1
        jsValue = jsValue + indentString + W3ValueToJS(v, indent + 1)
        if index == 0:
            jsValue = jsValue + "]"
        else:
            jsValue = jsValue + ",\n"

    return jsValue

def W3DictToPHP(value, indent):
    indentString = "    " * indent
    phpValue = "array (\n"
    index = len(value)

    if (index == 0):
        phpValue = phpValue + indentString + ")"
        return phpValue
        
    for key in value.keys():
        index = index - 1
        phpValue = phpValue + indentString + "\"" + key + "\" => " + W3ValueToPHP(value[key], indent + 1)
        if index == 0:
            phpValue = phpValue + ")"
        else:
            phpValue = phpValue + ",\n"

    return phpValue

def W3DictToJS(value, indent):
    indentString = "    " * indent
    jsValue = "{\n"
    index = len(value)

    if (index == 0):
        jsValue = jsValue + indentString + "}"
        return jsValue
        
    for key in value.keys():
        index = index - 1
        jsValue = jsValue + indentString + "\"" + key + "\"" + ": " + W3ValueToJS(value[key], indent + 1)
        if index == 0:
            jsValue = jsValue + "}"
        else:
            jsValue = jsValue + ",\n"

    return jsValue

def W3ConvertToDatePickerFormat(w3Format):
    w3Format = w3Format.replace("YYYY", "yy", 1)
    w3Format = w3Format.replace("MM", "mm", 1)
    w3Format = w3Format.replace("DD", "dd", 1)
    return w3Format
    
def W3InitJSUI(ui):
    js = "\n"
    js = js + "$(document).ready(function() {\n"
    js = js + "    function FormatDatePicker(uid, format) {\n"
    js = js + "        if (document.getElementById(uid) == null) {\n"
    js = js + "            return;\n"
    js = js + "        }\n"
    js = js + "        var element = $(\"#\" + uid);\n"
    js = js + "        if (element != undefined) {\n"
    js = js + "            element.datepicker({dateFormat:format});\n"
    js = js + "        }\n"
    js = js + "    }\n"
    js = js + "    function InitCalendar(uid) {\n"
    js = js + "        if (document.getElementById(uid) == null) {\n"
    js = js + "            return;\n"
    js = js + "        }\n"
    js = js + "        var element = $(\"#\" + uid);\n"
    js = js + "        if (element != undefined) {\n"
    js = js + "            element.calendar();\n"
    js = js + "        }\n"
    js = js + "    }\n"

    for key in ui.keys():
        if W3Const.w3PropType in ui[key]:
            if (ui[key][W3Const.w3PropType] == W3Const.w3TypeDatePicker):
                js = js + "    FormatDatePicker(\"" + key + "\", \"" + W3ConvertToDatePickerFormat(W3Const.w3DateFormat) + "\");\n"
            elif (ui[key][W3Const.w3PropType] == W3Const.w3TypeMonthPicker):
                js = js + "    FormatDatePicker(\"" + key + "\", \"" + W3ConvertToDatePickerFormat(W3Const.w3MonthFormat) + "\");\n"
            elif (ui[key][W3Const.w3PropType] == W3Const.w3TypeCalendar):
                js = js + "    InitCalendar(\"" + key + "\");\n"
    js = js + "});"
    return js
