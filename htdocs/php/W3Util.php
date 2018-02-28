<?php

#
# Request Helper
#

function W3IsEmptyRequest() {
    return $_SERVER["REQUEST_URI"] == "/";
}

#
# CSS Helper
#
function W3LoadCSS() {
    $uiPath = w3DirCSS . "/" . w3DirGenerated . "/" . w3FileUICSS;
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $uiPath . "\"></link>";

    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/jquery-ui.min.css\"></link>";
}
    
#
# JS Helper
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
# API Helper
#

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

function W3CreateAPIResult($status, $isFullResult) {
    $result = W3MakeString(w3ApiResultStatus) . ":" . W3MakeString($status);
    if ($isFullResult) {
        $result = "{" . $result . "}";
    }

    return $result;
}

#
# API Handler Helper
#

function W3OnRequestPage() {
    require "W3Main.html";
    return true;
}

#
# UI Helper
#

function W3GetUIEvent($uid) {
    $eventDict = array();
    $apiTriggerArray = array();

    # First check triggers in UI and wrapper API trigger as event function
    $apiTriggers = W3TryGetUIProperty($uid, w3PropTriggerApi);
    $triggerArraySize = 0;
    if ($apiTriggers != NULL) {
        $triggerArraySize = sizeof($apiTriggers);
        for ($i = 0; $i < $triggerArraySize; $i++) {
            $func =  "W3TriggerAPIFromUI(" . W3MakeString($uid, true) . ", " . strval($i) . ")";   
            array_push($apiTriggerArray, $func);
        }
    }

    # Get event from property
    $events = "";
    $uiEvents = W3TryGetUIProperty($uid, w3PropEvent);
    if ($uiEvents != NULL) {
        foreach ($uiEvents as $key => $value) {
            if (!array_key_exists($key, $eventDict)) {
                $eventDict[$key] = array();
            }

            if (!is_array($value)) {
                W3LogError("Event " . $key . " is not array type for UI: " . $uid);
                return $events;
            }

            foreach ($value as $func) {
                # Replace place holder as API trigger
                if (strncmp($func, w3PlaceHolder_1, 14) == 0) {
                    $posPlaceHolder = intval(substr($func, 14));
                    if ($posPlaceHolder > $triggerArraySize) {
                        W3LogError("Trigger event place holder is more than trigger");
                        return "";
                    }

                    $func = $apiTriggerArray[$posPlaceHolder - 1];
                }
                
                array_push($eventDict[$key], $func);
            }
        }
    }

    foreach ($eventDict as $event => $funcs) {
        $events .=  W3CreateUIEventString($event, $funcs) . " ";
    }

    return $events;
}

function W3GetUIClass($uid) {
    $uiClass = W3TryGetUIProperty($uid, w3PropClass);
    if ($uiClass != NULL) {
        return "class=" . W3MakeString($uiClass, true);
    }

    return "";
}

function W3GetUIBody($uid) {
    $uiString = W3TryGetUIProperty($uid, w3PropString);
    if ($uiString != NULL and $uiString != "") {
        return W3GetStringValue($uiString);
    }

    return "";
}

function W3GetUIAttr($uid) {
    $uiAttr = W3TryGetUIProperty($uid, w3PropAttr);
    if ($uiAttr != NULL) {
        $attr = "";
        foreach ($uiAttr as $key => $value) {
            $attr = $attr . $key . "=" . $value . " ";
        }
        
        return $attr;
    }

    return "";
}

#
# UI Creator Helper
#

$w3UICreatorMap = array (
    w3TypeTable => "W3CreateTable",
    w3TypeLink => "W3CreateLink",
    w3TypeLabel => "W3CreateLabel",
    w3TypeCheckbox => "W3CreateCheckbox",
    w3TypeDatePicker => "W3CreateDatePicker",
    w3TypeMonthPicker => "W3CreateMonthPicker",
    w3TypeButton => "W3CreateButton",
    w3TypeText => "W3CreateText",
    w3TypePassword => "W3CreatePassword",
    w3TypeCombobox => "W3CreateCombobox",
    w3TypeTab => "W3CreateTab",
    w3TypePanel => "W3CreatePanel",
    w3TypeCanvasPanel => "W3CreatePanel",
    w3TypeHeadline => "W3CreateHeadline",
    w3TypeLine => "W3CreateLine",
    w3TypeLineBreak => "W3CreateLineBreak",
    w3TypeParagraph => "W3CreateParagraph",
    w3TypeCanvas => "W3CreateCanvas",
    w3TypePage => "W3CreatePage"
);

function W3CreateHeadline($uid) {
    W3CreateUIBasePro($uid);

    $level = "1";
    $uiAttr = W3TryGetUIProperty($uid, w3PropAttr);
    if ($uiAttr != NULL) {
        if (array_key_exists(w3AttrHeadlineLevel, $uiAttr)) {
            $level = $uiAttr[w3AttrHeadlineLevel];
        }
    }

    $type = "h" . $level;
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
}
    
function W3CreateLine($uid) {
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
    W3CreateUIBasePro($uid);

    $type = "p";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateCanvas($uid) {
    W3CreateUIBasePro($uid);

    $type = "canvas";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateLink($uid) {
    W3CreateUIBasePro($uid);

    $href = "javascript: void(0);";
    $type = "a";
    $body = "";
    $attr = "href=" . $href;

    return W3CreateUIBase($uid, $type, $body, $attr);
};

function W3CreateLabel($uid) {
    W3CreateUIBasePro($uid);

    $type = "label";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
};

function W3CreateCheckbox($uid) {
    W3CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='checkbox'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateDatePicker($uid) {
    W3CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='text'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateMonthPicker($uid) {
    W3CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='text'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateButton($uid) {
    W3CreateUIBasePro($uid);

    $type = "button";
    $body = "";
    $attr = "type='button'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateText($uid) {
    W3CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='text'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreatePassword($uid) {
    W3CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='password'";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateCombobox($uid) {
    W3CreateUIBasePro($uid);

    $type = "select";
    $attr = "";
    $body = "";

    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateTable($uid) {
    W3CreateUIBasePro($uid);

    $type = "table";

    $body = "";
    $isHeadDefined = false;
    $isRowDefined = false;
    $cellsDef = W3TryGetUIProperty($uid, w3PropSubUI);
    if ($cellsDef != NULL) {
        if (sizeof($cellsDef) >= 1 and sizeof($cellsDef[0]) >= 1) {
            $isHeadDefined = true;
        }
        if (sizeof($cellsDef) >= 2) {
            $isRowDefined = true;
        }
    }
    if ($isHeadDefined) {
        $body .= "<tr id=" . W3MakeString($uid . "Header", true) . ">";
        $header = 0;
        foreach ($cellsDef[0] as $value) {
            $uidHeader = $value;
            $uiHeaderDef = W3GetUIDef($uidHeader);
            if ($uiHeaderDef == NULL) {
                W3LogError("Table header not find: " . $uidHeader);
                continue;
            }
            if ($uiHeaderDef[w3PropType] == w3TypeTableHeader) {
                # If it's table header, the real UI is in the head as sub UI
                $subUI = W3TryGetUIProperty($uidHeader, w3PropSubUI);
                if ($subUI != NULL and sizeof($subUI) > 0) {
                    $uidHeader = $subUI[0];
                }
            }
            $body .= "<th id=" . W3MakeString($uid . "Header" . strval($header), true) . ">";
            $body .= W3CreateUI($uidHeader);
            $body .= "</th>";
            $header += 1;
        }
        $body .= "</tr>";
    }
    if ($isRowDefined) {
        $count = sizeof($cellsDef) - 1;
        for ($i = 1; $i <= $count; $i++) {
            $body .= "<tr id=" . W3MakeString($uid . "Row" . strval($i), true) . ">";
            $j = 0;
            foreach ($cellsDef[$i] as $value) {
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
    $uiCSS = W3TryGetUIProperty($uid, w3PropCSS);
    if ($uiCSS != NULL) {
        $attr .= "style='border-style:none'";
        if (array_key_exists("border-style", $uiCSS)) {
            $borderStyle = $uiCSS["border-style"];
        }
        if (array_key_exists("border-width", $uiCSS)) {
            $borderWidth = $uiCSS["border-width"];
        }
        if (array_key_exists("background-color", $uiCSS)) {
            $bgColor = $uiCSS["background-color"];
        }
    }

    $tabStyle = "clear:both;background-color:" . $bgColor .
              ";border-top:" . $borderWidth . " " . $borderStyle;

    $headerBorder = $borderWidth . " " . $borderStyle;

    $headerBody = "<ul id=" . W3MakeString($uid . "header") . ">";
    $contentBody = "<div id=" . W3MakeString($uid . "content") .
                 " style=" . W3MakeString($tabStyle) . ">";
    $subUI = W3TryGetUIProperty($uid, w3PropSubUI);
    if ($subUI != NULL) {
        $i = 1;
        $size = sizeof($subUI);
        foreach ($subUI as $value) {
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
                        " onclick=" . W3MakeString("W3OnTabClicked(" .
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
    W3CreateUIBasePro($uid);

    $type = "div";

    $body = "";
    $subUI = W3TryGetUIProperty($uid, w3PropSubUI);
    if ($subUI != NULL) {
        foreach ($subUI as $value) {
            $body .= W3CreateUI($value);
        }
    }

    $attr = "";
    
    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreatePage($uid) {
    global $w3UI;
    
    W3CreateUIBasePro($uid);

    # No matter what functor defined for page type, it's creator is default to W3SelectPage
    # And here could not use get property functions because it will return a copy
    # Since W3CreateUIBasePro has copy every properties from prototye
    # Here property could be accessed directly
    $pageCreator = "W3SelectPage";
    if (array_key_exists($uid, $w3UI) and
        array_key_exists(w3PropFunc, $w3UI[$uid]) and
        array_key_exists(w3FuncCreator, $w3UI[$uid][w3PropFunc])) {
        $w3UI[$uid][w3PropFunc][w3FuncCreator] = $pageCreator;
    } else {
        $w3UI[$uid][w3PropFunc] = array(
            w3FuncCreator => $pageCreator
        );
    }
    
    return W3CreatePanel($uid);
}

function W3CreateUIBase($uid, $type, $body, $attr) {
    $uiFunc = W3TryGetUIProperty($uid, w3PropFunc);
    if ($uiFunc != NULL and array_key_exists(w3FuncCreator, $uiFunc)) {
        $body .= $uiFunc[w3FuncCreator]();
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
    if (array_key_exists($uid, $w3UI) and 
        array_key_exists(w3PropPrototype, $w3UI[$uid])) {
        # If there is another propotype also in prototype UI, create it first
        if (array_key_exists($w3UI[$uid][w3PropPrototype], $w3UI) and 
            array_key_exists(w3PropPrototype, $w3UI[$w3UI[$uid][w3PropPrototype]])) {
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

function W3CreateUIEventString($event, $funcs) {
    $result = "";
    
    $arraySize = sizeof($funcs);
    if ($arraySize < 1) {
        return "";
    } else if ($arraySize == 1) {
        $result = $funcs[0];
    } else {
        $result = "(function() {";
        for ($i = 0; $i < $arraySize; $i++) {
            $result .= $funcs[$i] . ";";
        }
        $result .= "})()";
    }
    
    return $event . "=" . W3MakeString($result);
}

?>