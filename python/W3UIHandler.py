import os

import W3Helper
import W3Const

from metadata import W3UI
from metadata import W3Def

uiPhpPath = os.path.join(W3Def.w3DirBase,
                         W3Const.w3DirServer,
                         W3Const.w3DirPHP,
                         W3Const.w3DirGenerated,
                         W3Const.w3FileUI)
uiPhp = open(uiPhpPath, "w")
uiPhp.write("<?php\n\n")

# Generate UI components
uiPhp.write("$w3UI = ")
uiPhp.write(W3Helper.W3ValueToPHP(W3UI.w3UI, 1))
uiPhp.write(";\n\n")

uiPhp.write(" ?>")
uiPhp.close()

# Generate Const for PHP reference
constPhpPath = os.path.join(W3Def.w3DirBase,
                            W3Const.w3DirServer,
                            W3Const.w3DirPHP,
                            W3Const.w3DirGenerated,
                            W3Const.w3FileConst)
constPhp = open(constPhpPath, "w")
constPhp.write("<?php\n\n")
constPhp.write(W3Helper.W3ConstToPHP(W3Const.__dict__))
constPhp.write("\n\n")
constPhp.write(" ?>")
constPhp.close()
