import os
import sys

import W3Helper
import W3Const

from metadata import W3Config

w3HandlerDirBase = os.path.split(os.path.realpath(__file__))[0]
result, classDef = W3Helper.W3SchemaCheck(W3Config.w3ClassDefPath)
if not result:
    print "Class schema check error"
    sys.exit(0)
result, uiDef = W3Helper.W3SchemaCheck(W3Config.w3UIDefPath)
if not result:
    print "UI schema check error"
    sys.exit(0)

# Generate UI components for PHP
uiPhpPath = os.path.join(w3HandlerDirBase,
                         W3Const.w3DirServer,
                         W3Const.w3DirPHP,
                         W3Const.w3DirGenerated,
                         W3Const.w3FileUIPHP)
uiPhp = open(uiPhpPath, "w")
uiPhp.write("<?php\n\n")

uiPhp.write("$w3UI = ")
uiPhp.write(W3Helper.W3ValueToPHP(uiDef, 1))
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
uiJS.write(W3Helper.W3ValueToJS(uiDef, 1))
uiJS.write(";")
uiJS.write(W3Helper.W3InitDatePicker(uiDef))
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
constPhp.write(W3Helper.W3ConstToPHP(constDef))
constPhp.write("define('w3LogLevel', " + str(W3Config.w3LogLevel) + ");\n");
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
constJS.write(W3Helper.W3ConstToJS(constDef))
constJS.write("const w3LogLevel = " + str(W3Config.w3LogLevel) + ";\n")
constJS.close()
