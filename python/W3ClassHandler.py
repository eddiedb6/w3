import os

import W3Helper
import W3Const

from metadata import W3UI
from metadata import W3Class
from metadata import W3Def

# Generate CSS for UI
uiCSSPath = os.path.join(W3Def.w3DirBase,
                         W3Const.w3DirServer,
                         W3Const.w3DirCSS,
                         W3Const.w3DirGenerated,
                         W3Const.w3FileUICSS)
uiCSS = open(uiCSSPath, "w")

# Handle CSS for each UI 
for uid in W3UI.w3UI.keys():
    if not W3UI.w3UI[uid].has_key(W3Const.w3PropCSS):
        continue;
    uiCSS.write("#" + uid + " {\n")
    for key in W3UI.w3UI[uid][W3Const.w3PropCSS].keys():
        uiCSS.write("    " + key + ":" + W3UI.w3UI[uid][W3Const.w3PropCSS][key] + ";\n")
    uiCSS.write("}\n\n")

def CopyBaseCSS(cid, cidBase):
    if cid == cidBase:
        return
    if W3Class.w3Class[cidBase].has_key(W3Const.w3PropPrototype):
        CopyBaseCSS(cidBase, W3Class.w3Class[cidBase][W3Const.w3PropPrototype])
    if W3Class.w3Class[cidBase].has_key(W3Const.w3PropCSS):
        # Copy but do not overwrite existed CSS
        for key in W3Class.w3Class[cidBase][W3Const.w3PropCSS].keys():
            if W3Class.w3Class[cid].has_key(W3Const.w3PropCSS):
                if W3Class.w3Class[cid][W3Const.w3PropCSS].has_key(key):
                    continue
            W3Class.w3Class[cid][W3Const.w3PropCSS][key] = W3Class.w3Class[cidBase][W3Const.w3PropCSS][key]
    # Remove prototype so it would no be copied by next derived CSS
    W3Class.w3Class[cid].pop(W3Const.w3PropPrototype, None)
    
# Handle CSS for each class    
for cid in W3Class.w3Class.keys():
    if W3Class.w3Class[cid].has_key(W3Const.w3PropPrototype):
        CopyBaseCSS(cid, W3Class.w3Class[cid][W3Const.w3PropPrototype])
        
    if not W3Class.w3Class[cid].has_key(W3Const.w3PropCSS):
        continue;
    
    uiCSS.write("." + cid + " {\n")
    for key in W3Class.w3Class[cid][W3Const.w3PropCSS].keys():
        uiCSS.write("    " + key + ":" + W3Class.w3Class[cid][W3Const.w3PropCSS][key] + ";\n")
    uiCSS.write("}\n\n")
    
uiCSS.close()
