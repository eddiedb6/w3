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
    echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"css/jquery-te-1.4.0.css\"></link>";
    echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"css/calendar.css\"></link>";
}

#
# JS Helper
#

function W3LoadJS() {
    # jquery
    echo "<script src=\"js/jquery-2.2.0.js\"></script>";
    # jquery ui control
    echo "<script src=\"js/jquery-ui.min.js\"></script>";
    # jquery te
    echo "<script type=\"text/javascript\" src=\"js/jquery-te-1.4.0.min.js\" charset=\"utf-8\"></script>";
    # calendar
    echo "<script type=\"text/javascript\" src=\"js/W3Calendar.js\"></script>";
    # pdf
    echo "<script type=\"text/javascript\" src=\"pdf/build/pdf.js\"></script>";
    echo "<script type=\"text/javascript\" src=\"js/W3PDF.js\"></script>";
    # w3
    echo "<script src=\"js/W3.js\"></script>";
    echo "<script src=\"js/W3Util.js\"></script>";

    # user
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
        $apiReg .= $apiDef[w3ApiParams][$i][w3ApiDataValue] . "=([\w\`\~\!\@\#\$%\^\*\(\)\-=\+\[\{\]\}\|\\\\:;\'\"\<,\>\.\/\?]*)";
        if ($i != $paramCount - 1) {
            $apiReg .= "&";
        } else {
            $apiReg .= "$/";
        }
    }
    
    return $apiReg;
}

function W3CreateAPIPostReg($aid) {
    $apiDef = W3GetAPIDef($aid);
    if ($apiDef == NULL) {
        return "";
    }

    $postCount = W3GetAPIPostCount($aid);
    $postReg = "/";
    if ($postCount < 1) {
        return $postReg . "/";
    } 

    for ($i = 0; $i < $postCount; $i++) {
        $postReg .= $apiDef[w3ApiPost][$i][w3ApiDataValue] . "=([\w\`\~\!\@\#\$%\^\*\(\)\-=\+\[\{\]\}\|\\\\:;\'\"\<,\>\.\/\?]*)";
        if ($i != $postCount - 1) {
            $postReg .= "&";
        } else {
            $postReg .= "/";
        }
    }
    
    return $postReg;
}

function W3CreateAPIResult($status, $isFullResult) {
    $result = W3MakeString(w3ApiResultStatus) . ":" . W3MakeString($status);
    if ($isFullResult) {
        $result = "{" . $result . "}";
    }

    return $result;
}

function W3GetAPIArgCount($aid, $argType) {
    $apiDef = W3GetAPIDef($aid);
    if ($apiDef == NULL) {
        return 0;
    }

    if (!array_key_exists($argType, $apiDef)) {
        return 0;
    }

    return sizeof($apiDef[$argType]);
}

function W3GetAPIArgIndex($aid, $argName, $argType) {
    $result = -1;
    
    $apiDef = W3GetAPIDef($aid);
    if ($apiDef == NULL) {
        return $result;
    }

    if (!array_key_exists($argType, $apiDef)) {
        return $result;
    }

    $index = 0;
    foreach ($apiDef[$argType] as $value) {
        $argNameDef = $value[w3ApiDataValue];
        if ($argNameDef == $argName) {
            $result = $index;
            break;
        }
        $index += 1;
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

function W3GetUIEvent($uid, $uiDef) {
    $eventDict = array();
    $apiTriggerArray = array();

    # First check triggers in UI and wrapper API trigger as event function
    $apiTriggers = W3TryGetUIProperty($uiDef, w3PropTriggerApi);
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
    $uiEvents = W3TryGetUIProperty($uiDef, w3PropEvent);
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

function W3GetUIClass($uid, $uiDef) {
    $uiClass = W3TryGetUIProperty($uiDef, w3PropClass);
    if ($uiClass != NULL) {
        return "class=" . W3MakeString($uiClass, true);
    }

    return "";
}

function W3GetUIBody($uid, $uiDef) {
    $uiString = W3TryGetUIProperty($uiDef, w3PropString);
    if ($uiString != NULL and $uiString != "") {
        return W3GetStringValue($uiString);
    }

    return "";
}

function W3GetUIAttr($uid, $uiDef) {
    $attr = "";

    $uiAttr = W3TryGetUIProperty($uiDef, w3PropAttr);
    if ($uiAttr != NULL) {
        foreach ($uiAttr as $key => $value) {
            $attr = $attr . $key . "=" . $value . " ";
        }
    }

    # CSS for static UI will be written to .css file
    # For dynamic UI, css will be insert in UI html
    if (W3IsDynamicUI($uiDef)) {
        $uiCSS = W3TryGetUIProperty($uiDef, w3PropCSS);
        if ($uiCSS != NULL) {
            $css = array();
            foreach ($uiCSS as $key => $value) {
                array_push($css, $key . ":" . $value);
            }

            if (sizeof($css) > 0) {
                $attr = $attr . " style='" . implode(";", $css) . "'";
            }
        }
    }

    return $attr;
}

function W3IsDynamicUI($uiDef) {
    $uiID = W3TryGetUIProperty($uiDef, w3PropID);
    if ($uiID != NULL) {
        return true;
    }

    return false;
}

#
# UI Creator Helper
#

function W3GenerateUIDChild($uidParent, $uiChild) {
    if (!is_array($uiChild)) {
        return $uiChild;
    }

    $uid = W3TryGetUIProperty($uiChild, w3PropID);
    if ($uid != NULL) {
        return $uid;
    }

    return $uidParent . "Child";
}

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
    w3TypeDisplayPanel => "W3CreatePanel",
    w3TypeHeadline => "W3CreateHeadline",
    w3TypeLine => "W3CreateLine",
    w3TypeLineBreak => "W3CreateLineBreak",
    w3TypeParagraph => "W3CreateParagraph",
    w3TypeCanvas => "W3CreateCanvas",
    w3TypePDFCanvas => "W3CreatePDFCanvas",
    w3TypePage => "W3CreatePage",
    w3TypeTextEditor => "W3CreateTextEditor",
    w3TypePlainTextEditor => "W3CreatePlainTextEditor",
    w3TypeCalendar => "W3CreateCalendar",
    w3TypeMap => "W3CreateMap"
);

function W3CreateHeadline($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $level = "1";
    $uiAttr = W3TryGetUIProperty($uiDef, w3PropAttr);
    if ($uiAttr != NULL) {
        if (array_key_exists(w3AttrHeadlineLevel, $uiAttr)) {
            $level = $uiAttr[w3AttrHeadlineLevel];
        }
    }

    $type = "h" . $level;
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}
    
function W3CreateLine($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "hr";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}
    
function W3CreateLineBreak($uid, $uiDef) {
    return "<br>";
}
    
function W3CreateParagraph($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "p";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateCanvas($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "canvas";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreatePDFCanvas($uid, $uiDef) {
    $js = "<script type=\"text/javascript\">\$('#" . $uid . "').W3PDF();</script>";
    return W3CreateCanvas($uid, $uiDef) . $js;
}

function W3CreateLink($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $href = "javascript: void(0);";
    $type = "a";
    $body = "";
    $attr = "href=" . $href;

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
};

function W3CreateLabel($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "label";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
};

function W3CreateCheckbox($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "input";
    $body = "";
    $attr = "type='checkbox'";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateDatePicker($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "input";
    $body = "";
    $attr = "type='text'";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateMonthPicker($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "input";
    $body = "";
    $attr = "type='text'";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateButton($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "button";
    $body = "";
    $attr = "type='button'";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateText($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "input";
    $body = "";
    $attr = "type='text'";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreatePassword($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "input";
    $body = "";
    $attr = "type='password'";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateCombobox($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "select";
    $attr = "";
    $body = "";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateTable($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "table";

    $body = "";
    $isHeadDefined = false;
    $isRowDefined = false;
    $cellsDef = W3TryGetUIProperty($uiDef, w3PropSubUI);
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
            $uiHeader = $value;
            $uiHeaderDef = W3GetUIDef($uiHeader);
            if ($uiHeaderDef == NULL) {
                W3LogError("Table header not find: " . $uiHeader);
                continue;
            }
            if ($uiHeaderDef[w3PropType] == w3TypeTableHeader) {
                # If it's table header, the real UI is in the head as sub UI
                $subUI = W3TryGetUIProperty($uiHeader, w3PropSubUI);
                if ($subUI != NULL and sizeof($subUI) > 0) {
                    $uiHeader = $subUI[0];
                }
            }
            $uidHeader = $uid . "Header" . strval($header);
            $uidHeaderChild = W3GenerateUIDChild($uidHeader, $uiHeader);
            $body .= "<th id=" . W3MakeString($uidHeader, true) . ">";
            $body .= W3CreateUI($uidHeaderChild, $uiHeader);
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
                $uidCell = $uid . "Cell" . strval($i) . strval($j);
                $uidCellChild = W3GenerateUIDChild($uidCell, $value);
                $body .= "<td id=" . W3MakeString($uidCell, true) . ">";
                $body .= W3CreateUI($uidCellChild, $value);
                $body .= "</td>";
                $j += 1;
            }
            $body .= "</tr>";
        }
    }
    
    $attr = "";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
};

function W3CreateTab($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    # Tab only support following CSS and their default value is defined here
    # "border-width": "1px",
    # "border-style": "solid",
    # "background-color": "white"
    $borderStyle = "solid";
    $borderWidth = "1px";
    $bgColor = "white";
    
    $type = "div";

    $attr = "";
    $uiCSS = W3TryGetUIProperty($uiDef, w3PropCSS);
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

    $headerBody = "<ul id=" . W3MakeString($uid . "Header") . ">";
    $contentBody = "<div id=" . W3MakeString($uid . "Content") .
                 " style=" . W3MakeString($tabStyle) . ">";
    $subUI = W3TryGetUIProperty($uiDef, w3PropSubUI);
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

            $uidHeader = $uid . "Header" . strval($i);
            $uidHeaderChild = W3GenerateUIDChild($uidHeader, $value[0]);
            $headerBody .= "<li" .
                        " style=" . W3MakeString($headerStyle, true) .
                        " id=" . W3MakeString($uidHeader) .
                        " onclick=" . W3MakeString("W3OnTabClicked(" .
                            W3MakeString($uid, true) . "," .
                            strval($i) . "," .
                            strval($size) . ")") .
                        ">" . W3CreateUI($uidHeaderChild, $value[0]) . "</li>";

            $contentDisplay = $i == 1 ? "display:block" : "display:none";

            $uidContent = $uid . "Content" . strval($i);
            $uidContentChild = W3GenerateUIDChild($uidContent, $value[1]);
            $contentBody .= "<div id=" . W3MakeString($uidContent) .
                         " style=" . W3MakeString($contentDisplay, true) . ">" .
                         W3CreateUI($uidContentChild, $value[1]) .
                         "</div>";
            ++$i;
        }
    }
    $headerBody .= "</ul>";
    $contentBody .= "</div>";
    $body = $headerBody . $contentBody;
    
    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateCalendar($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "div";
    $body = "";
    $attr = "";
    
    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateMSMap($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $beijing = array("39.918794", "116.398568");
    $shanghai = array("31.230369567871094", "121.47370147705078");
    $initLocation = $shanghai;

    $msKey = "";

    $mapProp = W3TryGetUIProperty($uiDef, w3PropMap);
    if ($mapProp != NULL) {
        if (array_key_exists(w3AttrMapLocation, $mapProp)) {
            $initLocation = $mapProp[w3AttrMapLocation];
        }
        if (array_key_exists(w3AttrMapKey, $mapProp)) {
            $msKey = $mapProp[w3AttrMapKey];
        }
    }

    $initJSStr = "<script type='text/javascript'>" .
                     "function loadMap" . $uid . "() {" .
                     "    var map = new Microsoft.Maps.Map(document.getElementById('" . $uid . "')," .
                     "                                     { center: new Microsoft.Maps.Location(" . $initLocation[0] . "," . $initLocation[1] . ") });" .
                     "    var propMap = W3TryGetUIProperty(\"" . $uid . "\", " . "\"" . w3PropMap . "\");" .
                     "    if ((propMap != null) && propMap.hasOwnProperty(\"" . w3AttrMapHandler . "\")) {" .
                     "        var handler = propMap[\"" . w3AttrMapHandler . "\"];" .
                     "        W3ExecuteFuncFromString(handler, map);" .
                     "    }" .
                     "}" .
                 "</script>" .
                 "<script type='text/javascript' src='https://www.bing.com/api/maps/mapcontrol?key=" . $msKey . "&callback=loadMap" . $uid . "' async defer></script>";

    $type = "div";
    $body = "";
    $attr = "";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr) . $initJSStr;
}

function W3CreatePanel($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "div";

    $body = "";
    $subUI = W3TryGetUIProperty($uiDef, w3PropSubUI);
    if ($subUI != NULL) {
        $index = 0;
        foreach ($subUI as $value) {
            $uidChild = W3GenerateUIDChild($uid . "Child" . strval($index), $value);
            $body .= W3CreateUI($uidChild, $value);
            $index += 1;
        }
    }

    $attr = "";
    
    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateMap($uid, $uiDef) {
    return W3CreateMSMap($uid, $uiDef);
}

function W3CreatePage($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    # No matter what functor defined for page type, it's creator is default to W3SelectPage
    # And here could not use get property functions because it will return a copy
    # Since W3CreateUIBasePro has copy every properties from prototye
    # Here property could be accessed directly
    $pageCreator = "W3SelectPage";
    if (array_key_exists(w3PropFunc, $uiDef) and
        array_key_exists(w3FuncCreator, $uiDef[w3PropFunc])) {
        $uiDef[w3PropFunc][w3FuncCreator] = $pageCreator;
    } else {
        $uiDef[w3PropFunc] = array(
            w3FuncCreator => $pageCreator
        );
    }
    
    return W3CreatePanel($uid, $uiDef);
}

function W3CreatePlainTextEditor($uid, $uiDef) {
    W3CreateUIBasePro($uid, $uiDef);

    $type = "textarea";
    $attr = "";
    $body = "";

    return W3CreateUIBase($uid, $uiDef, $type, $body, $attr);
}

function W3CreateTextEditor($uid, $uiDef) {
    $js = "<script type=\"text/javascript\">\$('#" . $uid . "').jqte();</script>";

    return W3CreatePlainTextEditor($uid, $uiDef) . $js;
}

function W3CreateUIBase($uid, $uiDef, $type, $body, $attr) {
    $uiFunc = W3TryGetUIProperty($uiDef, w3PropFunc);
    if ($uiFunc != NULL and array_key_exists(w3FuncCreator, $uiFunc)) {
        $body .= $uiFunc[w3FuncCreator]();
    } 
    
    return "<" . $type . " " .
               "id=" . W3MakeString($uid, true) . " " .
               W3GetUIEvent($uid, $uiDef) . " " .
               W3GetUIClass($uid, $uiDef) . " " .
               $attr . " " . W3GetUIAttr($uid, $uiDef) . ">" .
               trim(W3GetUIBody($uid, $uiDef) . " " . $body) .
               "</" . $type . ">\n";
}

function W3CreateUIBasePro($uid, $uiDef) {
    global $w3UI;
    
    # Copy everything from prototype 
    if (array_key_exists(w3PropPrototype, $uiDef)) {
        # If there is another propotype also in prototype UI, create it first
        if (array_key_exists($uiDef[w3PropPrototype], $w3UI) and 
            array_key_exists(w3PropPrototype, $w3UI[$uiDef[w3PropPrototype]])) {
            W3CreateUIBasePro($uiDef[w3PropPrototype], $w3UI[$uiDef[w3PropPrototype]]);
        }
        
        foreach ($w3UI[$uiDef[w3PropPrototype]] as $key => $value) {
            # Do not overwrite property already in current UI
            if (!array_key_exists($key, $uiDef)) {
                $uiDef[$key] = $value;
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
