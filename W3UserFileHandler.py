import os
import sys
import shutil

import W3Helper
import W3Const

from metadata import W3Config

w3HandlerDirBase = os.path.split(os.path.realpath(__file__))[0]

def loopUserDir(src, des, ext, fileFunc):
    if os.path.exists(src) is False:
        return
    for subdir, dirs, files in os.walk(src):
        relPath = os.path.relpath(subdir, src)
        for file in files:
            base, extention = os.path.splitext(file)
            if extention.lower() == ext:
                # Copy only file with "ext"
                srcFilePath = os.path.join(subdir, file)
                desFilePath = os.path.join(des, relPath, file)
                shutil.copyfile(srcFilePath, desFilePath)
                # Add user php files to header
                fileFunc(file)
        for dir in dirs:
            desDir = os.path.join(des, relPath, dir)
            os.mkdir(desDir)

# Deploy user JS file and generate include header function
userJSFileDir = os.path.join(w3HandlerDirBase,
                             W3Const.w3DirServer,
                             W3Const.w3DirJS,
                             W3Const.w3DirUserFile)
loadUserJSFunc =  "function W3LoadUserJS() {\n"
def echoJSFileInclude(jsFile):
    global loadUserJSFunc
    includePath = os.path.join(W3Const.w3DirJS, W3Const.w3DirUserFile, jsFile)
    loadUserJSFunc += "    echo \"<script src=\\\"" + includePath + "\\\"></script>\";\n"
loopUserDir(W3Config.w3UserJSRoot, userJSFileDir, ".js", echoJSFileInclude)
loadUserJSFunc += "}\n"

# Generate user include file for PHP and deploy user PHP file
userPHPFileDir = os.path.join(w3HandlerDirBase,
                              W3Const.w3DirServer,
                              W3Const.w3DirPHP,
                              W3Const.w3DirUserFile)
userPHPFilePath = os.path.join(w3HandlerDirBase,
                               W3Const.w3DirServer,
                               W3Const.w3DirPHP,
                               W3Const.w3DirGenerated,
                               W3Const.w3FileUserPHP)
userFilePhp = open(userPHPFilePath, "w")
userFilePhp.write("<?php\n\n")
def writePHPFileInclude(phpFile):
    global userFilePhp
    includePath = os.path.join(W3Const.w3DirPHP, W3Const.w3DirUserFile, phpFile)
    userFilePhp.write("require \"" + includePath + "\";\n")
loopUserDir(W3Config.w3UserPHPRoot, userPHPFileDir, ".php", writePHPFileInclude)
userFilePhp.write("\n")
userFilePhp.write(loadUserJSFunc)
userFilePhp.write("\n")
userFilePhp.write(" ?>")
userFilePhp.close()
