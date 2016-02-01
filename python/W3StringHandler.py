import os

import W3Helper
import W3Const

from metadata import W3String
from metadata import W3UI
from metadata import W3Def

def GenerateUIString(uid, string):
    return "\t$(\"#" + uid + "\").text(\"" + string + "\");\n"

def GenerateLanguagePackName(languagePack, lan):
    return languagePack.replace(".js", "_" + lan + ".js")

# For each UI component, display string based on language
for language in W3String.w3Lan.keys():
    languagePackPath = os.path.join(W3Def.w3DirBase,
                                    W3Const.w3DirServer,
                                    W3Const.w3DirJS,
                                    W3Const.w3DirGenerated,
                                    GenerateLanguagePackName(W3Const.w3FileStringJS, language))
    languagePack = open(languagePackPath, "w")
    languagePack.write("$(document).ready(function() { \n")
    for uid in W3UI.w3UI.keys():
        if not W3UI.w3UI[uid].has_key(W3Const.w3PropString):
            continue
        sid = W3UI.w3UI[uid][W3Const.w3PropString]
        if W3String.w3Lan[language].has_key(sid):
            languagePack.write(GenerateUIString(uid, W3String.w3Lan[language][sid]))

    languagePack.write("});");
    languagePack.close()

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
