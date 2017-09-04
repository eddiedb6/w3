<?php

# The path or dir or file name is defined by W3Const
# However, W3Const could not be used before it's loaded
# So here hard code is used directly and only here
# Becareful to change W3Const and it will be affect here
require "php/generated/const.php";
require "php/generated/api.php";
require "php/generated/ui.php";
require "php/generated/language.php";
require "php/generated/user.php";
require "php/W3RequestHandler.php";
require "php/W3PageHandler.php";
require "php/W3Util.php";

#
# Logger
#

function W3LogDebug($msg) {
    if (w3LogLevel <= w3LogDebug) {
        error_log("[W3 Debug]" . $msg);
    }
}

function W3LogInfo($msg) {
    if (w3LogLevel <= w3LogInfo) {
        error_log("[W3 Info]" . $msg);
    }
}

function W3LogWarning($msg) {
    if (w3LogLevel <= w3LogWarning) {
        error_log("[W3 Warning]" . $msg);
    }
}

function W3LogError($msg) {
    if (w3LogLevel <= w3LogError) {
        error_log("[W3 Error]" . $msg);
    }
}

function W3LogFatal($msg) {
    if (w3LogLevel <= w3LogFatal) {
        error_log("[W3 Fatal]" . $msg);
    }
}

#
# Request
#

function W3IsEmptyRequest() {
    return $_SERVER["REQUEST_URI"] == "/";
}

#
# CSS
#
function W3LoadCSS() {
    $uiPath = w3DirCSS . "/" . w3DirGenerated . "/" . w3FileUICSS;
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $uiPath . "\"></link>";

    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/jquery-ui.min.css\"></link>";
}
    
#
# JS
#

function W3LoadJS() {
    echo "<script src=\"js/jquery-2.2.0.js\"></script>";
    echo "<script src=\"js/jquery-ui.min.js\"></script>";
    echo "<script src=\"js/W3.js\"></script>";
    echo "<script src=\"js/W3Util.js\"></script>";

    $generatedPath = w3DirJS . "/" . w3DirGenerated;
    $apiPath = $generatedPath . "/" . w3FileAPIJS;
    $uiPath = $generatedPath . "/" . w3FileUIJS;
    $constPath = $generatedPath . "/" . w3FileConstJS;
    $languagePath = $generatedPath . "/" . w3FileStringJS;
    echo "<script src=\"" . $apiPath . "\"></script>";
    echo "<script src=\"" . $uiPath . "\"></script>";
    echo "<script src=\"" . $constPath . "\"></script>";
    echo "<script src=\"" . $languagePath . "\"></script>";

    W3LoadUserJS();
}

#
# String
#

function W3MakeString($val, $isSingleQuot = false) {
    $quot = $isSingleQuot ? "'" : "\"";
    return $quot . $val . $quot;
}

function W3MakeDateString($year, $month, $day, $isSingleQuot = false) {
    $dateFormat = w3DateFormat;
    $dateFormat = str_replace("YYYY", strval($year), $dateFormat);
    $dateFormat = str_replace("MM", strval($month), $dateFormat);
    $dateFormat = str_replace("DD", strval($day), $dateFormat);

    return W3MakeString($dateFormat, $isSingleQuot);
}

#
# Language
#

function W3GetStringValue($sid) {
    global $w3Lan;
    
    $language = $w3Lan[W3GetLanguage()];
    if (!array_key_exists($sid, $language)) {
        W3LogError("No sid defined: " . $sid);
        return "";
    }
    
    return $language[$sid];
}

#
# API
#

function W3CreateSuccessfulResult($isFullResult = true) {
    return W3CreateAPIResult(w3ApiResultSuccessful, $isFullResult);
}

function W3CreateFailedResult($isFullResult = true) {
    return W3CreateAPIResult(w3ApiResultFailed, $isFullResult);
}

function W3CreateAPIReg($aid) {
    $apiDef = W3GetAPIDef($aid);
    if ($apiDef == NULL) {
        return "";
    }
    
    $paramCount = W3GetAPIParamCount($aid);
    $apiReg = "/^\/" . $apiDef[w3ApiName];
    if ($paramCount < 1) {
        return $apiReg . "$/";
    } else {
        $apiReg .= "\?";
    }
    for ($i = 0; $i < $paramCount; $i++) {
        $apiReg .= $apiDef[w3ApiParams][$i][w3ApiDataValue] . "=([\w\-\.\,]*)";
        if ($i != $paramCount - 1) {
            $apiReg .= "&";
        } else {
            $apiReg .= "$/";
        }
    }
    return $apiReg;
}

#
# UI Creators
#

function W3CreateUI($uid) {
    global $w3UI;
    global $w3UICreatorMap;

    if (array_key_exists($uid, $w3UI)) {
        if (array_key_exists($w3UI[$uid][w3PropType], $w3UICreatorMap)) {
            return $w3UICreatorMap[$w3UI[$uid][w3PropType]]($uid);
        } else {
            W3LogError("UI Type is not defined and cannot be created: " . $w3UI[$uid][w3PropType]);
        }
    } else {
        W3LogError("No UI type defined so cannot be created for uid: " . $uid);
    }
    
    return "";
}

 ?>