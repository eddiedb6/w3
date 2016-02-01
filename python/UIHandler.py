import os

import Helper
import Const

from metadata import UI
from metadata import Def

uiPhpPath = os.path.join(Def.dirBase, Const.dirServer, Const.dirPHP, Const.dirGenerated, Const.fileUI)
uiPhp = open(uiPhpPath, "w")
uiPhp.write("<?php\n\n")

# Generate UI components
uiPhp.write("$ui = ")
uiPhp.write(Helper.ValueToPHP(UI.ui, 1))
uiPhp.write(";\n\n")

uiPhp.write(" ?>")
uiPhp.close()

# Generate Const for PHP reference
constPhpPath = os.path.join(Def.dirBase, Const.dirServer, Const.dirPHP, Const.dirGenerated, Const.fileConst)
constPhp = open(constPhpPath, "w")
constPhp.write("<?php\n\n")
constPhp.write(Helper.ConstToPHP(Const.__dict__))
constPhp.write("\n\n")
constPhp.write(" ?>")
constPhp.close()
