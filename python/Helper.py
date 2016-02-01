
def ConstToPHP(const):
    if not isinstance(const, dict):
        return ""

    constValues = ""
    for k in const.keys():
        if isinstance(const[k], basestring) and not k.startswith('__'):
            constValues = constValues + "define('" + k + "', '" + const[k] + "');\n"

    return constValues;
    

def ValueToPHP(value, indent):
    if isinstance(value, dict):
        return DictToPHP(value, indent)
    elif isinstance(value, list) or isinstance(value, tuple):
        return ArrayToPHP(value, indent)
    else:
        return "\"" + value + "\""

def ArrayToPHP(value, indent):
    indentString = "    " * indent
    phpValue = "array (\n"
    index = len(value)
    
    if (index == 0):
        phpValue = phpValue + indentString + ")"
        return phpValue
        
    for v in value:
        index = index - 1
        phpValue = phpValue + indentString + ValueToPHP(v, indent + 1)
        if index == 0:
            phpValue = phpValue + ")"
        else:
            phpValue = phpValue + ",\n"

    return phpValue

def DictToPHP(value, indent):
    indentString = "    " * indent
    phpValue = "array (\n"
    index = len(value)

    if (index == 0):
        phpValue = phpValue + indentString + ")"
        return phpValue
        
    for key in value.keys():
        index = index - 1
        phpValue = phpValue + indentString + "\"" + key + "\" => " + ValueToPHP(value[key], indent + 1)
        if index == 0:
            phpValue = phpValue + ")"
        else:
            phpValue = phpValue + ",\n"

    return phpValue
