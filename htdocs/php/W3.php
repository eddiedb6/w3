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

function W3GetStringValue($str) {
    global $w3Lan;
    
    $language = $w3Lan[W3GetLanguage()];
    $sid = $str;
    if (!array_key_exists($sid, $language)) {
        if (gettype($str) == "string") {
            return $str;
        }
        
        W3LogError("No sid defined: " . $sid);
        return "";
    }
    
    return $language[$sid];
}

function W3GetLanguage() {
    return w3LanEnglish; # [ED]PENDING: Handle language selection
}

#
# API
#

function W3CreateAPI($aid, $paramArray) {
    $apiDef = W3GetAPIDef($aid);
    if ($apiDef == NULL) {
        return "";
    }
    
    $paramCount = sizeof($paramArray);
    $paramDefCount = W3GetAPIParamCount($aid);
    $api = $apiDef[w3ApiName];
    if ($paramDefCount == 0) {
        return $api;
    } else {
        $api .= "?";
    }
    for ($i = 0; $i < $paramDefCount; $i++) {
        $paramValue = $i < $paramCount ? $paramArray[$i] : '';
        $api .= $apiDef[w3ApiParams][$i][w3ApiDataValue] . "=" . $paramValue;
        if ($i != $paramDefCount - 1) {
            $api .= "&";
        }
    }
    
    return $api;
}

function W3CreateSuccessfulResult($isFullResult = true) {
    return W3CreateAPIResult(w3ApiResultSuccessful, $isFullResult);
}

function W3CreateFailedResult($isFullResult = true) {
    return W3CreateAPIResult(w3ApiResultFailed, $isFullResult);
}

function W3CreateAuthenticationResult($isFullResult = true) {
    return W3CreateAPIResult(w3ApiResultAuthentication, $isFullResult);
}

function W3GetAPIDef($aid) {
    global $w3API;

    if (!array_key_exists($aid, $w3API)) {
        W3LogError("No aid defined: " . $aid);
        return NULL;
    }

    return $w3API[$aid];
}

function W3GetAPIParamCount($aid) {
    return W3GetAPIArgCount($aid, w3ApiParams);
}

function W3GetAPIPostCount($aid) {
    return W3GetAPIArgCount($aid, w3ApiPost);
}

function W3GetAPIParamIndex($aid, $paramName) {
    return W3GetAPIArgIndex($aid, $paramName, w3ApiParams);
}

function W3GetAPIPostIndex($aid, $postName) {
    return W3GetAPIArgIndex($aid, $postName, w3ApiPost);
}

function W3GetAPIPostParams($aid, &$postParams) {
    $apiDef = W3GetAPIDef($aid);
    if ($apiDef == NULL) {
        return false;
    }

    $data = file_get_contents("php://input");
    $apiName = $apiDef[w3ApiName];
    $postCheckFunc = "W3IsPost_" . $apiName;
    if ($postCheckFunc($data, $postParams)) {
        return true;
    }
    
    return false;
}

//
// UI
//

function W3GetUIDef($ui) {
    global $w3UI;

    $uid = w3UIDUndefined;
    if (is_array($ui)) {
        return $ui;
    } else {
        $uid = $ui;
    }

    if (!array_key_exists($uid, $w3UI)) {
        W3LogError("No uid defined: " . $uid);
        return NULL;
    }

    return $w3UI[$uid];
}

function W3TryGetUIProperty($ui, $property) {
    $uiDef = W3GetUIDef($ui);

    if ($uiDef == NULL) {
        return NULL;
    }
    
    if (array_key_exists($property, $uiDef)) {
	    return $uiDef[$property];
    }

    if (array_key_exists(w3PropPrototype, $uiDef)) {
        $uidPrototype = $uiDef[w3PropPrototype];
        $uiDefPrototype = W3GetUIDef($uidPrototype);
        if ($uiDefPrototype != NULL) {
            return W3TryGetUIProperty($uiDefPrototype, $property);
        }
    }

    return NULL;
}

function W3CreateDynamicUI($uid, $ui) {
    $uiDefJS = W3PHPVarToJS($ui);
    $js = "<script>w3UI[" . W3MakeString($uid, true) . "]=" . $uiDefJS . ";</script>";
    return $js . W3CreateUI($uid, $ui);
}
 
function W3CreateUI($uid, $ui) {
    global $w3UICreatorMap;

    $uiDef = W3GetUIDef($ui);
    if ($uiDef == NULL) {
        W3LogError("No UI defined for uid: " . $uid);        
        return "";
    }

    if (is_array($uid)) {
        $uid = w3UIDUndefined;
    }

    if (array_key_exists($uiDef[w3PropType], $w3UICreatorMap)) {
        return $w3UICreatorMap[$uiDef[w3PropType]]($uid, $uiDef);
    } else {
        W3LogError("UI Type is not defined and cannot be created: " . $uiDef[w3PropType]);
    }
    
    return "";
}

//
// Session
//

function W3GetSession() {
    if (array_key_exists(w3Session, $_SESSION)) {
        return $_SESSION[w3Session];
    }

    return "";
}

//
// Code
//

function W3Decode($text) {
    // + will lost, why?
    $result = str_replace("+", ":;:;", $text);

    $result = urldecode($result);

    $result = str_replace("::;;", "&", $result);
    $result = str_replace(";;::", "#", $result);

    $result = str_replace(":;:;", "+", $result);

    return $result;
}

function W3Encode($text) {
    // + will lost, why?
    $text = str_replace("+", ":;:;", $text);

    $text = str_replace("&", "::;;", $text);
    $text = str_replace("#", ";;::", $text);

    $result = urlencode($text);

    $result = str_replace(":;:;", "+", $result);

    return $result;
}

//
// Others
//

// Return: gettype() + "dict"
function W3GetPHPVarType($var) {
    if (is_array($var)) {
        $keys = array_keys($var);
        foreach ($keys as $value) {
            if (gettype($value) == "string") {
                return "dict";
            }
        }

        return "array";
    }

    return gettype($var);
}

function W3PHPVarToJS($var) {
    $varType = W3GetPHPVarType($var);
    if ($varType == "dict") {
        return W3PHPVarToJSDict($var);
    } else if ($varType == "array") {
        return W3PHPVarToJSArray($var);
    } else if ($varType == "string") {
        return W3MakeString($var, true);
    }

    return strval($var);
}

function W3PHPVarToJSArray($var) {
    $values = array();
    foreach ($var as $value) {
        array_push($values, W3PHPVarToJS($value));
    }
    $js = implode(",", $values);

    return "[" . $js . "]";
}

function W3PHPVarToJSDict($var) {
    $values = array();
    foreach ($var as $key => $value) {
        array_push($values, W3PHPVarToJS($key) . ":" . W3PHPVarToJS($value));
    }
    $js = implode(",", $values);

    return "{" . $js . "}";
}

 ?>
