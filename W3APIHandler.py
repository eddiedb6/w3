import os
import sys

import W3Util
import W3Const

from metadata import W3Config

w3HandlerDirBase = os.path.split(os.path.realpath(__file__))[0]
result, apiSchema = W3Util.W3SchemaCheck(W3Config.w3APIDefPath)
if not result:
    print "API schema check error"
    sys.exit(0)

# Insert default API "aidPage"
apiSchema["aidPage"] = {
    W3Const.w3ElementType: W3Const.w3TypeApi,
    W3Const.w3ApiName: "page",
    W3Const.w3ApiParams: [
    {
        W3Const.w3ApiDataType: W3Const.w3ApiDataTypeString,
        W3Const.w3ApiDataValue: "id"
    }]
}

#########################
# Generate PHP api file #
#########################

apiDefPath = os.path.join(w3HandlerDirBase,
                          W3Const.w3DirServer,
                          W3Const.w3DirPHP,
                          W3Const.w3DirGenerated,
                          W3Const.w3FileAPIPHP)
apiDef = open(apiDefPath, "w")

apiDef.write("<?php\n\n")

# Write APIs
apiDef.write("$w3API = ")
apiDef.write(W3Util.W3ValueToPHP(apiSchema, 1))
apiDef.write(";\n\n")

#
# Write helpers
#

# API check function for each 
for aid in apiSchema.keys():
    apiDef.write("function W3IsRequest_" + apiSchema[aid][W3Const.w3ApiName] + "($request, &$parameters = NULL) {\n")
    apiDef.write("    return preg_match(W3CreateAPIReg(\"" + aid + "\"), $request, $parameters);\n")
    apiDef.write("}\n\n")

# API server handler for each
apiDef.write("function W3APIHandleRequest() {\n")
apiDef.write("    $request = $_SERVER[\"REQUEST_URI\"];\n")
apiDef.write("    $parameters = \"\";\n\n")
for aid in apiSchema.keys():
    if aid == "aidPage":
        # Do not need to handle default api "aidPage"
        continue
    apiDef.write("    if (W3IsRequest_" + apiSchema[aid][W3Const.w3ApiName] + "($request, $parameters)) {\n")
    apiDef.write("        echo " + apiSchema[aid][W3Const.w3ApiHandler] + "($parameters);\n")
    apiDef.write("        return true;\n")
    apiDef.write("    }\n")
apiDef.write("\n")
apiDef.write("    W3LogError(\"Request could not be handled: \" . $request);\n")
apiDef.write("    return false;\n")
apiDef.write("}\n")

apiDef.write(" ?>\n")
apiDef.close()

########################
# Generate JS api file #
########################

apiDefPathJS = os.path.join(w3HandlerDirBase,
                            W3Const.w3DirServer,
                            W3Const.w3DirJS,
                            W3Const.w3DirGenerated,
                            W3Const.w3FileAPIJS)
apiDefJS = open(apiDefPathJS, "w")
apiDefJS.write("var w3API = ")
apiDefJS.write(W3Util.W3ValueToJS(apiSchema, 1))
apiDefJS.write(";")
apiDefJS.close()

