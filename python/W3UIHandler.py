import os

import W3Helper
import W3Const

from metadata import W3UI
from metadata import W3Def

# Generate UI components for PHP
uiPhpPath = os.path.join(W3Def.w3DirBase,
                         W3Const.w3DirServer,
                         W3Const.w3DirPHP,
                         W3Const.w3DirGenerated,
                         W3Const.w3FileUIPHP)
uiPhp = open(uiPhpPath, "w")
uiPhp.write("<?php\n\n")

uiPhp.write("$w3UI = ")
uiPhp.write(W3Helper.W3ValueToPHP(W3UI.w3UI, 1))
uiPhp.write(";\n\n")

uiPhp.write(" ?>")
uiPhp.close()

# Generate UI components for JS
uiJSPath = os.path.join(W3Def.w3DirBase,
                        W3Const.w3DirServer,
                        W3Const.w3DirJS,
                        W3Const.w3DirGenerated,
                        W3Const.w3FileUIJS)
uiJS = open(uiJSPath, "w")
uiJS.write("var w3UI = ")
uiJS.write(W3Helper.W3ValueToJS(W3UI.w3UI, 1))
uiJS.write(";")
uiJS.close()

# Generate Const for PHP reference
constPhpPath = os.path.join(W3Def.w3DirBase,
                            W3Const.w3DirServer,
                            W3Const.w3DirPHP,
                            W3Const.w3DirGenerated,
                            W3Const.w3FileConstPHP)
constPhp = open(constPhpPath, "w")
constPhp.write("<?php\n\n")
constPhp.write(W3Helper.W3ConstToPHP(W3Const.__dict__))
constPhp.write("\n\n")
constPhp.write(" ?>")
constPhp.close()

# Generate Const for JS reference
constJSPath = os.path.join(W3Def.w3DirBase,
                           W3Const.w3DirServer,
                           W3Const.w3DirJS,
                           W3Const.w3DirGenerated,
                           W3Const.w3FileConstJS)
constJS = open(constJSPath, "w")
constJS.write(W3Helper.W3ConstToJS(W3Const.__dict__))
constJS.close()
