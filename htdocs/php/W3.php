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
    
    $quot = $isSingleQuot ? "'" : "\"";
    return $quot . $dateFormat . $quot;
}

#
# Language
#

function W3GetLanguage() {
    return w3LanEnglish; # [ED]PENDING: Handle language selection
}

#
# API
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

function W3CreateAPI($aid, $paramArray) {
    global $w3API;
    $paramCount = sizeof($paramArray);
    $paramDefCount = W3GetAPIParamCount($aid);
    $apiString = $w3API[$aid][w3ApiName];
    if ($paramDefCount == 0) {
        return $apiString;
    } else {
        $apiString .= "?";
    }
    for ($i = 0; $i < $paramDefCount; $i++) {
        $paramValue = $i < $paramCount ? $paramArray[$i] : '';
        $apiString .= $w3API[$aid][w3ApiParams][$i][w3ApiDataValue] . "=" . $paramValue;
        if ($i != $paramDefCount - 1) {
            $apiString .= "&";
        }
    }
    return $apiString;
}

function W3CreateAPIReg($aid) {
    global $w3API;
    $paramCount = W3GetAPIParamCount($aid);
    $apiReg = "/^\/" . $w3API[$aid][w3ApiName];
    if ($paramCount < 1) {
        return $apiReg . "$/";
    } else {
        $apiReg .= "\?";
    }
    for ($i = 0; $i < $paramCount; $i++) {
        $apiReg .= $w3API[$aid][w3ApiParams][$i][w3ApiDataValue] . "=([\w\-\.\,]*)";
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

function W3CreateHeadline($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $level = "1";
    if (array_key_exists(w3PropAttr, $w3UI[$uid])) {
        if (array_key_exists(w3AttrHeadlineLevel, $w3UI[$uid][w3PropAttr])) {
            $level = $w3UI[$uid][w3PropAttr][w3AttrHeadlineLevel];
        }
    }

    $type = "h" . $level;
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
}
    
function W3CreateLine($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "hr";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
}
    
function W3CreateLineBreak($uid) {
    return "<br>";
}
    
function W3CreateParagraph($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "p";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateCanvas($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "canvas";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateLink($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "a";
    $body = "";
    $attr = "href=" . W3MakeString(W3CreateAPI($w3UI[$uid][w3PropApi][w3ApiID], W3GetAPIParamArrayFromUI($uid)), true);

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
    $attr = "type='text'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateMonthPicker($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='text'";

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
    if (array_key_exists(w3PropString, $w3UI[$uid])) {
        $attr .= " value=" . W3MakeString($w3Lan[W3GetLanguage()][$w3UI[$uid][w3PropString]], true);
        $w3UI[$uid][w3PropString] = "";
    }
    
    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateForm($uid) {
    global $w3UI;
    global $w3API;

    W3CreateUIBasePro($uid);

    $type = "form";

    $attr = "name='input' action=" . W3MakeString($w3API[$w3UI[$uid][w3PropApi][w3ApiID]][w3ApiName], true) . " method=" . W3MakeString($w3UI[$uid][w3PropMethod], true);

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
    $isHeadDefined = false;
    $isRowDefined = false;
    if (array_key_exists(w3PropSubUI, $w3UI[$uid])) {
        if (sizeof($w3UI[$uid][w3PropSubUI]) >= 1 and sizeof($w3UI[$uid][w3PropSubUI][0]) >= 1) {
            $isHeadDefined = true;
        }
        if (sizeof($w3UI[$uid][w3PropSubUI]) >= 2) {
            $isRowDefined = true;
        }
    }
    if ($isHeadDefined) {
        $body .= "<tr id=" . W3MakeString($uid . "Header", true) . ">";
        $header = 0;
        foreach ($w3UI[$uid][w3PropSubUI][0] as $value) {
            if ($w3UI[$value][w3PropType] != w3TypeTableHeader) {
                W3LogError("UI is not table header: " + $value);
                continue;
            }
            $body .= "<th id=" . W3MakeString($uid . "Header" . strval($header), true) . ">";
            $body .= W3CreateUI($w3UI[$value][w3PropSubUI][0]);
            $body .= "</th>";
            $header += 1;
        }
        $body .= "</tr>";
    }
    if ($isRowDefined) {
        $count = sizeof($w3UI[$uid][w3PropSubUI]) - 1;
        for ($i = 1; $i <= $count; $i++) {
            $body .= "<tr id=" . W3MakeString($uid . "Row" . strval($i), true) . ">";
            $j = 0;
            foreach ($w3UI[$uid][w3PropSubUI][$i] as $value) {
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

function W3CreateTab($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    # Tab only support following CSS and their default value is defined here
    # "border-width": "1px",
    # "border-style": "solid",
    # "background-color": "white"
    $borderStyle = "solid";
    $borderWidth = "1px";
    $bgColor = "white";
    
    $type = "div";

    $attr = "";
    if (array_key_exists(w3PropCSS, $w3UI[$uid])) {
        $attr .= "style='border-style:none'";
        if (array_key_exists("border-style", $w3UI[$uid][w3PropCSS])) {
            $borderStyle = $w3UI[$uid][w3PropCSS]["border-style"];
        }
        if (array_key_exists("border-width", $w3UI[$uid][w3PropCSS])) {
            $borderWidth = $w3UI[$uid][w3PropCSS]["border-width"];
        }
        if (array_key_exists("background-color", $w3UI[$uid][w3PropCSS])) {
            $bgColor = $w3UI[$uid][w3PropCSS]["background-color"];
        }
    }

    $tabStyle = "clear:both;background-color:" . $bgColor .
              ";border-top:" . $borderWidth . " " . $borderStyle;

    $headerBorder = $borderWidth . " " . $borderStyle;

    $headerBody = "<ul id=" . W3MakeString($uid . "header") . ">";
    $contentBody = "<div id=" . W3MakeString($uid . "content") .
                 " style=" . W3MakeString($tabStyle) . ">";
    if (array_key_exists(w3PropSubUI, $w3UI[$uid])) {
        $i = 1;
        $size = sizeof($w3UI[$uid][w3PropSubUI]);
        foreach ($w3UI[$uid][w3PropSubUI] as $value) {
            $headerStyle = "float:left;list-style:none;margin-bottom:-" . $borderWidth . ";" .
                         "background-color:" . $bgColor . ";";

            if ($i == 1) {
                $headerStyle .= "border-bottom:" . $headerBorder . " " . $bgColor . ";" .
                             "border-top:" . $headerBorder . ";" .
                             "border-left:" . $headerBorder . ";" .
                             "border-right:" . $headerBorder . ";";

            } 

            $headerBody .= "<li" .
                        " style=" . W3MakeString($headerStyle, true) .
                        " id=" . W3MakeString($uid . "header" . strval($i)) .
                        " onclick=" . W3MakeString("W3SetTab(" .
                            W3MakeString($uid, true) . "," .
                            strval($i) . "," .
                            strval($size) . ")") .
                        ">" . W3CreateUI($value[0]) . "</li>";

            $contentDisplay = $i == 1 ? "display:block" : "display:none";
            
            $contentBody .= "<div id=" . W3MakeString($uid . "content" . strval($i)) .
                         " style=" . W3MakeString($contentDisplay, true) . ">" .
                         W3CreateUI($value[1]) .
                         "</div>";
            ++$i;
        }
    }
    $headerBody .= "</ul>";
    $contentBody .= "</div>";
    $body = $headerBody . $contentBody;
    
    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreatePanel($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "div";

    $body = "";
    if (array_key_exists(w3PropSubUI, $w3UI[$uid])) {
        foreach ($w3UI[$uid][w3PropSubUI] as $value) {
            $body .= W3CreateUI($value);
        }
    }

    $attr = "";
    
    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreatePage($uid) {
    global $w3UI;

    # No matter what functor defined for page type, it's creator is default to W3SelectPage
    $pageCreator = "W3SelectPage";
    if (array_key_exists(w3PropFunc, $w3UI[$uid])) {        
        $w3UI[$uid][w3PropFunc][w3FuncCreator] = $pageCreator;
    } else {
        $w3UI[$uid][w3PropFunc] = array(
            w3FuncCreator => $pageCreator
        );
    }
    
    return W3CreatePanel($uid);
}

function W3CreateUIBase($uid, $type, $body, $attr) {
    global $w3UI;
    
    if (array_key_exists(w3PropFunc, $w3UI[$uid]) and
        array_key_exists(w3FuncCreator, $w3UI[$uid][w3PropFunc])) {
        $body .= $w3UI[$uid][w3PropFunc][w3FuncCreator]();
    }

    return "<" . $type . " " .
               "id=" . W3MakeString($uid, true) . " " .
               W3GetUIEvent($uid) . " " .
               W3GetUIClass($uid) . " " .
               $attr . " " . W3GetUIAttr($uid) . ">" .
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

$w3UICreatorMap = array (
    w3TypeTable => "W3CreateTable",
    w3TypeLink => "W3CreateLink",
    w3TypeLabel => "W3CreateLabel",
    w3TypeCheckbox => "W3CreateCheckbox",
    w3TypeDatePicker => "W3CreateDatePicker",
    w3TypeMonthPicker => "W3CreateMonthPicker",
    w3TypeButton => "W3CreateButton",
    w3TypeForm => "W3CreateForm",
    w3TypeSubmit => "W3CreateSubmit",
    w3TypeText => "W3CreateText",
    w3TypeCombobox => "W3CreateCombobox",
    w3TypeTab => "W3CreateTab",
    w3TypePanel => "W3CreatePanel",
    w3TypeHeadline => "W3CreateHeadline",
    w3TypeLine => "W3CreateLine",
    w3TypeLineBreak => "W3CreateLineBreak",
    w3TypeParagraph => "W3CreateParagraph",
    w3TypeCanvas => "W3CreateCanvas",
    w3TypePage => "W3CreatePage"
);

function W3CreateUI($uid) {
    global $w3UI;
    global $w3UICreatorMap;

    if (array_key_exists(w3PropType, $w3UI[$uid])) {
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