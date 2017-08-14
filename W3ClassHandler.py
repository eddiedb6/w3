import os

import W3Util
import W3Const

from metadata import W3Config

w3HandlerDirBase = os.path.split(os.path.realpath(__file__))[0]
result, classDef = W3Util.W3SchemaCheck(W3Config.w3ClassDefPath)
if not result:
    print "Class schema check error"
    sys.exit(0)
result, uiDef = W3Util.W3SchemaCheck(W3Config.w3UIDefPath)
if not result:
    print "UI schema check error"
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
    if not uiDef[uid].has_key(W3Const.w3PropCSS):
        continue;
    uiCSS.write("#" + uid + " {\n")
    for key in uiDef[uid][W3Const.w3PropCSS].keys():
        uiCSS.write("    " + key + ":" + uiDef[uid][W3Const.w3PropCSS][key] + ";\n")
    uiCSS.write("}\n\n")

def CopyBaseCSS(cid, cidBase):
    if cid == cidBase:
        return
    if classDef[cidBase].has_key(W3Const.w3PropPrototype):
        CopyBaseCSS(cidBase, classDef[cidBase][W3Const.w3PropPrototype])
    if classDef[cidBase].has_key(W3Const.w3PropCSS):
        # Copy but do not overwrite existed CSS
        for key in classDef[cidBase][W3Const.w3PropCSS].keys():
            if classDef[cid].has_key(W3Const.w3PropCSS):
                if classDef[cid][W3Const.w3PropCSS].has_key(key):
                    continue
            classDef[cid][W3Const.w3PropCSS][key] = classDef[cidBase][W3Const.w3PropCSS][key]
    # Remove prototype so it would no be copied by next derived CSS
    classDef[cid].pop(W3Const.w3PropPrototype, None)
    
# Handle CSS for each class    
for cid in classDef.keys():
    if classDef[cid].has_key(W3Const.w3PropPrototype):
        CopyBaseCSS(cid, classDef[cid][W3Const.w3PropPrototype])
        
    if not classDef[cid].has_key(W3Const.w3PropCSS):
        continue;
    
    uiCSS.write("." + cid + " {\n")
    for key in classDef[cid][W3Const.w3PropCSS].keys():
        uiCSS.write("    " + key + ":" + classDef[cid][W3Const.w3PropCSS][key] + ";\n")
    uiCSS.write("}\n\n")
    
uiCSS.close()
