<?php

require "php/generated/const.php";
require "php/generated/api.php";
require "php/generated/ui.php";
require "php/generated/language.php";
require "php/W3RequestHandler.php";
require "php/W3PageSelector.php";
require "php/W3LanguageSelector.php";
require "php/W3UserLib.php";

#
# Logger
#

function W3LogDebug($msg) {
    if (w3LogLevel >= w3LogDebug) {
        error_log("[W3 Debug]" . $msg);
    }
}

function W3LogInfo($msg) {
    if (w3LogLevel >= w3LogInfo) {
        error_log("[W3 Info]" . $msg);
    }
}

function W3LogWarning($msg) {
    if (w3LogLevel >= w3LogWarning) {
        error_log("[W3 Warning]" . $msg);
    }
}

function W3LogError($msg) {
    if (w3LogLevel >= w3LogError) {
        error_log("[W3 Error]" . $msg);
    }
}

function W3LogFatal($msg) {
    if (w3LogLevel >= w3LogFatal) {
        error_log("[W3 Fatal]" . $msg);
    }
}

#
# Request helper
#

function W3IsEmptyRequest() {
    return $_SERVER["REQUEST_URI"] == "/";
}

#
# CSS helper
#
function W3LoadCSS() {
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/generated/ui.css\"></link>";
}
    
#
# JS helper
#

function W3LoadJS() {
    echo "<script src=\"jquery-2.2.0.js\"></script>";
    echo "<script src=\"W3Helper.js\"></script>";
    echo "<script src=\"js/W3Event.js\"></script>";
    echo "<script src=\"js/generated/api.js\"></script>";
    echo "<script src=\"js/generated/ui.js\"></script>";
    echo "<script src=\"js/generated/const.js\"></script>";
}

#
# String helper
#

function W3MakeString($val, $isSingleQuot = false) {
    $quot = $isSingleQuot ? "'" : "\"";
    return $quot . $val . $quot;
}

#
# Language Helper
#

function W3GetLanguage() {
    return w3LanEnglish; ## TODO
}


#
# API Helper
#

function W3CreateSuccessfulResult($isFullResult = true) {
    if ($isFullResult) {
        return "{" . W3MakeString(w3ApiResultStatus) . ":" . W3MakeString(w3ApiResultSuccessful) . "}";
    } else {
        return W3MakeString(w3ApiResultStatus) . ":" . W3MakeString(w3ApiResultSuccessful);
    }
}

function W3CreateFailedResult($isFullResult = true) {
    if ($isFullResult) {
        return "{" . W3MakeString(w3ApiResultStatus) . ":" . W3MakeString(w3ApiResultFailed) . "}";
    } else {
        return W3MakeString(w3ApiResultStatus) . ":" . W3MakeString(w3ApiResultFailed);
    }
}

function W3GetAPIParamCount($aid) {
    global $w3API;

    $count = sizeof($w3API[$aid]);

    if (array_key_exists(w3ApiName, $w3API[$aid])) {
        $count -= 1;
    }
    if (array_key_exists(w3ApiResult, $w3API[$aid])) {
        $count -= 1;
    }

    return $count;
}

function W3GetAPIParamArrayFromUI($uid) {
    global $w3UI;

    $paramArray = array ();
    $arraySize = sizeof($w3UI[$uid][w3PropApi]);

    if ($arraySize <= 1) {
        return $paramArray;
    }

    for ($i = 1; $i < $arraySize; $i++) {
        $paramIndex = W3GetParamNameFromIndex($i);
        array_push($paramArray, $w3UI[$uid][w3PropApi][$paramIndex]);
    }
    
    return $paramArray;
}

function W3GetParamNameFromIndex($index) {
    return "param" . strval($index); 
}

#
# UI helpers
#

function W3GetUIEvent($uid) {
    global $w3UI;

    if (array_key_exists(w3PropEvent, $w3UI[$uid])) {
        $events = "";
        foreach ($w3UI[$uid][w3PropEvent] as $key => $value) {
            $events .= $key . "=" . W3MakeString($value) . " ";
        }
        
        return $events;
    }

    return "";
}

function W3GetUIClass($uid) {
    global $w3UI;

    if (array_key_exists(w3PropClass, $w3UI[$uid])) {
        return "class=" . W3MakeString($w3UI[$uid][w3PropClass], true);
    }

    return "";
}

function W3GetUIBody($uid) {
    global $w3UI;
    global $w3Lan;

    if (array_key_exists(w3PropBody, $w3UI[$uid]) and $w3UI[$uid][w3PropBody] != "") {
        $language = W3GetLanguage();

        return $w3Lan[$language][$w3UI[$uid][w3PropBody]];
    }

    return "";
}

function W3GetUIAttr($uid) {
    global $w3UI;

    if (array_key_exists(w3PropAttr, $w3UI[$uid])) {
        return $w3UI[$uid][w3PropAttr];
    }

    return "";
}

function W3InsertAttr($uid, $attr) {
    global $w3UI;

    if (array_key_exists(w3PropAttr, $w3UI[$uid])) {
        $w3UI[$uid][w3PropAttr] = $w3UI[$uid][w3PropAttr] . " " . $attr;
    } else {
        $w3UI[$uid][w3PropAttr] = $attr;
    }
}

function W3InsertAPIParamAttr(&$api) {
    # Handle api parameter in form or button or other UI
    $paramSize = sizeof($api);
    if ($paramSize > 1) {
        for ($i = 1; $i <= $paramSize - 1; $i++) {
            $paramIndex = W3GetParamNameFromIndex($i);
            $paramName = "name=" . W3MakeString($w3API[$api[w3ApiID]][$paramIndex], true);
            W3InsertAttr($api[$paramIndex], $paramName);
        }
    }
}

#
# UI Creators
#

function W3CreateLink($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "a";
    $body = "";
    $attr = "href=" .
          W3MakeString(W3CreateAPI($w3UI[$uid][w3PropApi][w3ApiID], W3GetAPIParamArrayFromUI($uid)), true);

    return W3CreateUIBase($uid, $type, $body, $attr);
};

function W3CreateLabel($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "label";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
};

function W3CreateCheckbox($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='checkbox'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateDatePicker($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='date'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateButton($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "button";
    $body = "";
    $attr = "type='button'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateText($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='text'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateCombobox($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "select";
    $attr = "";

    $body = "";
    if (array_key_exists(w3PropFunc, $w3UI[$uid])) {
        $body .= $w3UI[$uid][w3PropFunc][w3FuncComboCreator]();
    }

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateSubmit($uid) {
    global $w3UI;
    global $w3Lan;

    W3CreateUIBasePro($uid);
    
    $type = "input";
    $body = "";
    $attr = "type='submit'";

    # put body string as value attr for submit
    if (array_key_exists(w3PropBody, $w3UI[$uid])) {
        $attr .= " value=" . W3MakeString($w3Lan[W3GetLanguage()][$w3UI[$uid][w3PropBody]], true);
        $w3UI[$uid][w3PropBody] = "";
    }
    
    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateForm($uid) {
    global $w3UI;
    global $w3API;

    W3CreateUIBasePro($uid);

    $type = "form";

    $attr = "name='input' action=" . W3MakeString($w3API[$w3UI[$uid][w3PropApi][w3ApiID]][w3ApiName], true) .
          " method=" . W3MakeString($w3UI[$uid][w3PropMethod], true);

    W3InsertAPIParamAttr($w3UI[$uid][w3PropApi]);
    
    $body = "";
    if (array_key_exists(w3PropList, $w3UI[$uid])) {
        foreach ($w3UI[$uid][w3PropList] as $value) {
            $body .= W3CreateUI($value);
        }
    }
    
    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateTable($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "table";

    $body = "";
    if (array_key_exists(w3PropTH, $w3UI[$uid])) {
        $body .= "<tr id=" . W3MakeString($uid . "Header", true) . ">";
        $header = 0;
        foreach ($w3UI[$uid][w3PropTH] as $value) {
            $body .= "<th id=" . W3MakeString($uid . "Header" . strval($header), true) . ">";
            $body .= W3CreateUI($value);
            $body .= "</th>";
            $header += 1;
        }
        $body .= "</tr>";
    }
    if (array_key_exists(w3PropTD, $w3UI[$uid])) {
        $count = sizeof($w3UI[$uid][w3PropTD]);
        for ($i = 0; $i < $count; $i++) {
            $body .= "<tr id=" . W3MakeString($uid . "Row" . strval($i), true) . ">";
            $j = 0;
            foreach ($w3UI[$uid][w3PropTD][$i] as $value) {
                $body .= "<td id=" .
                    W3MakeString($uid . "Cell" . strval($i) . strval($j), true) .
                    ">";
                $body .= W3CreateUI($value);
                $body .= "</td>";
                $j += 1;
            }
            $body .= "</tr>";
        }
    }
    
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
};

function W3CreateUIBase($uid, $type, $body, $attr) {
    return "<" . $type . " " .
               "id=" . W3MakeString($uid, true) . " " .
               W3GetUIEvent($uid) . " " .
               W3GetUIClass($uid) . " " .
               W3GetUIAttr($uid) . " " . $attr . ">" .
               trim(W3GetUIBody($uid) . " " . $body) .
               "</" . $type . ">\n";
}

function W3CreateUIBasePro($uid) {
    global $w3UI;

    # Copy everything from prototype 
    if (array_key_exists(w3PropPrototype, $w3UI[$uid])) {
        # If there is another propotype also in prototype UI, create it first
        if (array_key_exists(w3PropPrototype, $w3UI[$w3UI[$uid][w3PropPrototype]])) {
            W3CreateUIBasePro($w3UI[$uid][w3PropPrototype]);
        }
        
        foreach ($w3UI[$w3UI[$uid][w3PropPrototype]] as $key => $value) {
            # Do not overwrite property already in current UI
            if (!array_key_exists($key, $w3UI[$uid])) {
                    $w3UI[$uid][$key] = $value;
            }
        }
    }
}

function W3CreateUI($uid) {
    global $w3UI;

    if (array_key_exists(w3PropType, $w3UI[$uid])) {
        if ($w3UI[$uid][w3PropType] == w3TypeTable) {
            return W3CreateTable($uid);
        } else if ($w3UI[$uid][w3PropType] == w3TypeLink) {
            return W3CreateLink($uid);
        } else if ($w3UI[$uid][w3PropType] == w3TypeLabel) {
            return W3CreateLabel($uid);
        } else if ($w3UI[$uid][w3PropType] == w3TypeCheckbox) {
            return W3CreateCheckbox($uid);
        } else if ($w3UI[$uid][w3PropType] == w3TypeDatePicker) {
            return W3CreateDatePicker($uid);
        } else if ($w3UI[$uid][w3PropType] == w3TypeButton) {
            return W3CreateButton($uid);
        } else if ($w3UI[$uid][w3PropType] == w3TypeForm) {
            return W3CreateForm($uid);
        } else if ($w3UI[$uid][w3PropType] == w3TypeSubmit) {
            return W3CreateSubmit($uid);
        } else if ($w3UI[$uid][w3PropType] == w3TypeText) {
            return W3CreateText($uid);
        } else if ($w3UI[$uid][w3PropType] == w3TypeCombobox) {
            return W3CreateCombobox($uid);
        } else {
            W3LogError("UI Type is not defined and cannot be created: " . $w3UI[$uid][w3PropType]);
        }
    }
    
    return "";
}

 ?>