import W3Const

def W3ConstToPHP(const):
    if not isinstance(const, dict):
        return ""

    constValues = ""
    for k in const.keys():
        if k.startswith('w3'):
            if (isinstance(const[k], basestring)):
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
            if (isinstance(const[k], basestring)):
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

def W3InitDatePicker(ui):
    js = "\n$(document).ready(function() {\n"
    for key in ui.keys():
        if ui[key].has_key(W3Const.w3PropType):
            if (ui[key][W3Const.w3PropType] == W3Const.w3TypeDatePicker):
                js = js + "\t$(\"#" + key + "\").datepicker();\n"

    js = js + "});"
    return js
