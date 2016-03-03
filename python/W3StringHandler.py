import os

import W3Helper
import W3Const

from metadata import W3String
from metadata import W3UI
from metadata import W3Def

# Generate php string file
languagePHPPath = os.path.join(W3Def.w3DirBase,
                               W3Const.w3DirServer,
                               W3Const.w3DirPHP,
                               W3Const.w3DirGenerated,
                               W3Const.w3FileStringPHP)
languagePHP = open(languagePHPPath, "w")
languagePHP.write("<?php\n\n")
languagePHP.write("$w3Lan = ")
languagePHP.write(W3Helper.W3ValueToPHP(W3String.w3Lan, 1))
languagePHP.write(";\n\n")
languagePHP.write(" ?>")
languagePHP.close()
