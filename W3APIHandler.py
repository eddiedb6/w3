import os
import sys

import W3Helper
import W3Const

from metadata import W3Config

w3HandlerDirBase = os.path.split(os.path.realpath(__file__))[0]
result, apiSchema = W3Helper.W3SchemaCheck(W3Config.w3APIDefPath)
if not result:
    print "API schema check error"
    sys.exit(0)

apiDefPath = os.path.join(w3HandlerDirBase,
                          W3Const.w3DirServer,
                          W3Const.w3DirPHP,
                          W3Const.w3DirGenerated,
                          W3Const.w3FileAPIPHP)
apiDef = open(apiDefPath, "w")

apiDef.write("<?php\n\n")

# Write APIs
apiDef.write("$w3API = ")
apiDef.write(W3Helper.W3ValueToPHP(apiSchema, 1))
apiDef.write(";\n\n")

#
# Write helpers
#

# API check function for each 
for aid in apiSchema.keys():
    apiDef.write("function W3IsRequest_" +
                 apiSchema[aid][W3Const.w3ApiName] +
                 "($request, &$parameters = NULL) {\n")
    apiDef.write("    return preg_match(W3CreateAPIReg(\"" + aid + "\"), $request, $parameters);\n")
    apiDef.write("}\n\n")             

# API create function                 
funCreateAPI = "function W3CreateAPI($aid, $paramArray) {\n"
funCreateAPI += "    global $w3API;\n"
funCreateAPI += "    $paramCount = sizeof($paramArray);\n"
funCreateAPI += "    $apiString = $w3API[$aid][w3ApiName];\n" 
funCreateAPI += "    if ($paramCount == 0) {\n" 
funCreateAPI += "        return $apiString;\n" 
funCreateAPI += "    } else {\n"
funCreateAPI += "        $apiString .= \"?\";\n"
funCreateAPI += "    }\n"
funCreateAPI += "    for ($i = 0; $i < $paramCount; $i++) {\n" 
funCreateAPI += "        $param = W3GetParamNameFromIndex($i + 1);\n" 
funCreateAPI += "        $apiString .= $w3API[$aid][$param][1] . \"=\" . $paramArray[$i];\n" 
funCreateAPI += "        if ($i != $paramCount - 1) {\n" 
funCreateAPI += "            $apiString .= \"&\";\n" 
funCreateAPI += "        }\n" 
funCreateAPI += "    }\n" 
funCreateAPI += "    return $apiString;\n" 
funCreateAPI += "};\n\n"
apiDef.write(funCreateAPI)

# API regular match string create function                 
funCreateAPIReg = "function W3CreateAPIReg($aid) {\n"
funCreateAPIReg += "    global $w3API;\n"
funCreateAPIReg += "    $paramCount = W3GetAPIParamCount($aid);\n"
funCreateAPIReg += "    $apiReg = \"/^\\/\" . $w3API[$aid][w3ApiName];\n" 
funCreateAPIReg += "    if ($paramCount < 1) {\n" 
funCreateAPIReg += "        return $apiReg . \"$/\";\n" 
funCreateAPIReg += "    } else {\n"
funCreateAPIReg += "        $apiReg .= \"\\?\";\n"
funCreateAPIReg += "    }\n"
funCreateAPIReg += "    for ($i = 1; $i <= $paramCount; $i++) {\n" 
funCreateAPIReg += "        $param = W3GetParamNameFromIndex($i);\n" 
funCreateAPIReg += "        $apiReg .= $w3API[$aid][$param][1] . \"=([\\w\\-\\.\\,]*)\";\n" 
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

# Generate JS api file
apiDefPathJS = os.path.join(w3HandlerDirBase,
                            W3Const.w3DirServer,
                            W3Const.w3DirJS,
                            W3Const.w3DirGenerated,
                            W3Const.w3FileAPIJS)
apiDefJS = open(apiDefPathJS, "w")
apiDefJS.write("var w3API = ")
apiDefJS.write(W3Helper.W3ValueToJS(apiSchema, 1))
apiDefJS.write(";")
apiDefJS.close()

