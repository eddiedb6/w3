import os
import sys

import W3Helper
import W3Const

from metadata import W3Config

w3HandlerDirBase = os.path.split(os.path.realpath(__file__))[0]
result, stringDef = W3Helper.W3SchemaCheck(W3Config.w3StringDefPath)
if not result:
    print "String schema check error"
    sys.exit(0)

# Generate php string file
languagePHPPath = os.path.join(w3HandlerDirBase,
                               W3Const.w3DirServer,
                               W3Const.w3DirPHP,
                               W3Const.w3DirGenerated,
                               W3Const.w3FileStringPHP)
languagePHP = open(languagePHPPath, "w")
languagePHP.write("<?php\n\n")
languagePHP.write("$w3Lan = ")
languagePHP.write(W3Helper.W3ValueToPHP(stringDef, 1))
languagePHP.write(";\n\n")
languagePHP.write(" ?>")
languagePHP.close()

# Generate js string file
languageJSPath = os.path.join(w3HandlerDirBase,
                              W3Const.w3DirServer,
                              W3Const.w3DirJS,
                              W3Const.w3DirGenerated,
                              W3Const.w3FileStringJS)
languageJS = open(languageJSPath, "w")
languageJS.write("var w3Lan = ")
languageJS.write(W3Helper.W3ValueToJS(stringDef, 1))
languageJS.write(";")
languageJS.close()
