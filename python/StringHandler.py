import os

import Helper
import Const

from metadata import String
from metadata import UI
from metadata import Def

def GenerateUIString(uid, string):
    return "\t$(\"#" + uid + "\").text(\"" + string + "\");\n"

def GenerateLanguagePackName(languagePack, lan):
    return languagePack.replace(".js", "_" + lan + ".js")

# For each UI component, display string based on language
for language in String.lan.keys():
    languagePackPath = os.path.join(Def.dirBase,
                                    Const.dirServer,
                                    Const.dirJS,
                                    Const.dirGenerated,
                                    GenerateLanguagePackName(Const.fileStringJS, language))
    languagePack = open(languagePackPath, "w")
    languagePack.write("$(document).ready(function() { \n")
    for uid in UI.ui.keys():
        if not UI.ui[uid].has_key(Const.propString):
            continue
        sid = UI.ui[uid][Const.propString]
        if String.lan[language].has_key(sid):
            languagePack.write(GenerateUIString(uid, String.lan[language][sid]))

    languagePack.write("});");
    languagePack.close()

# Generate php string file
languagePHPPath = os.path.join(Def.dirBase,
                               Const.dirServer,
                               Const.dirPHP,
                               Const.dirGenerated,
                               Const.fileStringPHP)
languagePHP = open(languagePHPPath, "w")
languagePHP.write("<?php\n\n")
languagePHP.write("$lan = ")
languagePHP.write(Helper.ValueToPHP(String.lan, 1))
languagePHP.write(";\n\n")
languagePHP.write(" ?>")
languagePHP.close()
