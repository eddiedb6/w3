import os

import Helper
import Const

from metadata import API
from metadata import Def

apiDefPath = os.path.join(Def.dirBase, Const.dirServer, Const.dirPHP, Const.dirGenerated, Const.fileAPI)
apiDef = open(apiDefPath, "w")

apiDef.write("<?php\n\n")

# Write APIs
apiDef.write("$api = ")
apiDef.write(Helper.ValueToPHP(API.api, 1))
apiDef.write(";\n\n")

#
# Write helpers
#

# API check function for each 
for aid in API.api.keys():
    apiDef.write("function IsRequest_" + API.api[aid][Const.apiName] + "($request) {\n")
    apiDef.write("    return preg_match(CreateAPIReg(\"" + aid + "\"), $request);\n")
    apiDef.write("}\n\n")             

# API create function                 
funCreateAPI = "function CreateAPI($aid, $paramArray) {\n"
funCreateAPI += "    global $api;\n"
funCreateAPI += "    $paramCount = sizeof($paramArray);\n"
funCreateAPI += "    $apiString = $api[$aid][apiName];\n" 
funCreateAPI += "    if ($paramCount == 0) {\n" 
funCreateAPI += "        return $apiString;\n" 
funCreateAPI += "    } else {\n"
funCreateAPI += "        $apiString .= \"?\";\n"
funCreateAPI += "    }\n"
funCreateAPI += "    for ($i = 0; $i < $paramCount; $i++) {\n" 
funCreateAPI += "        $param = \"param\" . strval($i + 1);\n" 
funCreateAPI += "        $apiString .= $api[$aid][$param] . \"=\" . $paramArray[$i];\n" 
funCreateAPI += "        if ($i != $paramCount - 1) {\n" 
funCreateAPI += "            $apiString .= \"&\";\n" 
funCreateAPI += "        }\n" 
funCreateAPI += "    }\n" 
funCreateAPI += "    return $apiString;\n" 
funCreateAPI += "};\n\n"
apiDef.write(funCreateAPI)

# API regular match string create function                 
funCreateAPIReg = "function CreateAPIReg($aid) {\n"
funCreateAPIReg += "    global $api;\n"
funCreateAPIReg += "    $paramCount = sizeof($api[$aid]) - 1;\n"
funCreateAPIReg += "    $apiReg = \"/^\\/\" . $api[$aid][apiName];\n" 
funCreateAPIReg += "    if ($paramCount < 1) {\n" 
funCreateAPIReg += "        return $apiReg . \"$/\";\n" 
funCreateAPIReg += "    } else {\n"
funCreateAPIReg += "        $apiReg .= \"\\?\";\n"
funCreateAPIReg += "    }\n"
funCreateAPIReg += "    for ($i = 1; $i <= $paramCount; $i++) {\n" 
funCreateAPIReg += "        $param = \"param\" . strval($i);\n" 
funCreateAPIReg += "        $apiReg .= $api[$aid][$param] . \"=([\\w\\-]+)\";\n" 
funCreateAPIReg += "        if ($i != $paramCount) {\n" 
funCreateAPIReg += "            $apiReg .= \"&\";\n" 
funCreateAPIReg += "        } else {\n"
funCreateAPIReg += "            $apiReg .= \"$/\";\n"
funCreateAPIReg += "        }\n"
funCreateAPIReg += "    }\n" 
funCreateAPIReg += "    return $apiReg;\n" 
funCreateAPIReg += "};\n\n"
apiDef.write(funCreateAPIReg)

apiDef.write(" ?>\n")
apiDef.close()
