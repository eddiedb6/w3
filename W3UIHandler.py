import os
import sys

import W3Util
import W3Const

from metadata import W3Config

sys.path.append(os.path.join(os.path.split(os.path.realpath(__file__))[0], "schema"))

w3HandlerDirBase = os.path.split(os.path.realpath(__file__))[0]
result, uiDef = W3Util.W3SchemaCheck(W3Config.w3UIDefPath)
if not result:
    print("UI schema check error")
    sys.exit(0)

# Check if there is dead loop for prototype
for uid in uiDef.keys():
    uiPrototypes = []
    currentUI = uiDef[uid]
    currentUID = uid
    while W3Const.w3PropPrototype in currentUI:
        if currentUID in uiPrototypes:
            print("There is dead loop for prototype define: " + currentUID)
            sys.exit(0);
        uiPrototypes.append(currentUID)
        currentUID = currentUI[W3Const.w3PropPrototype]
        currentUI = uiDef[currentUID]

# Generate UI components for PHP
uiPhpPath = os.path.join(w3HandlerDirBase,
                         W3Const.w3DirServer,
                         W3Const.w3DirPHP,
                         W3Const.w3DirGenerated,
                         W3Const.w3FileUIPHP)
uiPhp = open(uiPhpPath, "w")
uiPhp.write("<?php\n\n")

uiPhp.write("$w3UI = ")
uiPhp.write(W3Util.W3ValueToPHP(uiDef, 1))
uiPhp.write(";\n\n")

uiPhp.write(" ?>")
uiPhp.close()

# Generate UI components for JS
uiJSPath = os.path.join(w3HandlerDirBase,
                        W3Const.w3DirServer,
                        W3Const.w3DirJS,
                        W3Const.w3DirGenerated,
                        W3Const.w3FileUIJS)
uiJS = open(uiJSPath, "w")
uiJS.write("var w3UI = ")
uiJS.write(W3Util.W3ValueToJS(uiDef, 1))
uiJS.write(";\n")
# Generate JS variable defined in UI
varList = {}
for uid in uiDef.keys():
    currentUI = uiDef[uid]
    if W3Const.w3PropBindingVar not in currentUI:
        continue
    varName = currentUI[W3Const.w3PropBindingVar][W3Const.w3BindingVarName]
    varFormat = ""
    if W3Const.w3BindingFormat in currentUI[W3Const.w3PropBindingVar]:
        varFormat = currentUI[W3Const.w3PropBindingVar][W3Const.w3BindingFormat]
    listenerInfo = "\"" + uid + "\": \"" + varFormat + "\""
    if varName in varList:
        if uid not in varList[varName]:
            varList[varName].append(listenerInfo)
        else:
            print("Error: uid already in var listener")
            sys.exit(0)
    else:
        varList[varName] = [listenerInfo]
for var in varList:
    uiJS.write("var " + var + " = {\n")
    uiJS.write("    \"" + W3Const.w3VariableValue + "\": 0,\n")
    uiJS.write("    \"" + W3Const.w3VariableListeners + "\": {")
    if len(varList[var]) > 0:
        uiJS.write(",".join(varList[var]))
    uiJS.write("}\n};\n")
# Init date picker for JS
uiJS.write(W3Util.W3InitDatePicker(uiDef))
uiJS.close()

# Clone W3 const and remove collections
constDef = {}
for key in W3Const.__dict__:
    if not isinstance(W3Const.__dict__[key], list):
        constDef[key] = W3Const.__dict__[key]

# Generate Const for PHP reference
constPhpPath = os.path.join(w3HandlerDirBase,
                            W3Const.w3DirServer,
                            W3Const.w3DirPHP,
                            W3Const.w3DirGenerated,
                            W3Const.w3FileConstPHP)
constPhp = open(constPhpPath, "w")
constPhp.write("<?php\n\n")
constPhp.write(W3Util.W3ConstToPHP(constDef))
constPhp.write("define('w3LogLevel', " + str(W3Config.w3LogLevel) + ");\n");
constPhp.write("define('w3RSAPublicKey', '" + str(W3Config.w3RSAPublicKey) + "');\n");
constPhp.write("define('w3RSAPrivateKey', '" + str(W3Config.w3RSAPrivateKey) + "');\n");
constPhp.write("\n\n")
constPhp.write(" ?>")
constPhp.close()

# Generate Const for JS reference
constJSPath = os.path.join(w3HandlerDirBase,
                           W3Const.w3DirServer,
                           W3Const.w3DirJS,
                           W3Const.w3DirGenerated,
                           W3Const.w3FileConstJS)
constJS = open(constJSPath, "w")
constJS.write(W3Util.W3ConstToJS(constDef))
constJS.write("const w3LogLevel = " + str(W3Config.w3LogLevel) + ";\n")
constJS.close()
