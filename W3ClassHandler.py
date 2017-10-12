import os
import sys

import W3Util
import W3Const

from metadata import W3Config

sys.path.append(os.path.join(os.path.split(os.path.realpath(__file__))[0], "schema"))

w3HandlerDirBase = os.path.split(os.path.realpath(__file__))[0]
result, classDef = W3Util.W3SchemaCheck(W3Config.w3ClassDefPath)
if not result:
    print("Class schema check error")
    sys.exit(0)
result, uiDef = W3Util.W3SchemaCheck(W3Config.w3UIDefPath)
if not result:
    print("UI schema check error")
    sys.exit(0)

# Generate CSS for UI
uiCSSPath = os.path.join(w3HandlerDirBase,
                         W3Const.w3DirServer,
                         W3Const.w3DirCSS,
                         W3Const.w3DirGenerated,
                         W3Const.w3FileUICSS)
uiCSS = open(uiCSSPath, "w")

# Handle CSS for each UI 
for uid in uiDef.keys():
    if W3Const.w3PropCSS not in uiDef[uid]:
        continue;
    uiCSS.write("#" + uid + " {\n")
    for key in uiDef[uid][W3Const.w3PropCSS].keys():
        uiCSS.write("    " + key + ":" + uiDef[uid][W3Const.w3PropCSS][key] + ";\n")
    uiCSS.write("}\n\n")

def CopyBaseCSS(cid, cidBase):
    if cid == cidBase:
        return
    if W3Const.w3PropPrototype in classDef[cidBase]:
        CopyBaseCSS(cidBase, classDef[cidBase][W3Const.w3PropPrototype])
    if W3Const.w3PropCSS in classDef[cidBase]:
        # Copy but do not overwrite existed CSS
        for key in classDef[cidBase][W3Const.w3PropCSS].keys():
            if W3Const.w3PropCSS in classDef[cid]:
                if key in classDef[cid][W3Const.w3PropCSS]:
                    continue
            classDef[cid][W3Const.w3PropCSS][key] = classDef[cidBase][W3Const.w3PropCSS][key]
    # Remove prototype so it would no be copied by next derived CSS
    classDef[cid].pop(W3Const.w3PropPrototype, None)
    
# Handle CSS for each class    
for cid in classDef.keys():
    if W3Const.w3PropPrototype in classDef[cid]:
        CopyBaseCSS(cid, classDef[cid][W3Const.w3PropPrototype])
        
    if W3Const.w3PropCSS not in classDef[cid]:
        continue;
    
    uiCSS.write("." + cid + " {\n")
    for key in classDef[cid][W3Const.w3PropCSS].keys():
        uiCSS.write("    " + key + ":" + classDef[cid][W3Const.w3PropCSS][key] + ";\n")
    uiCSS.write("}\n\n")
    
uiCSS.close()
