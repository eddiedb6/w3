<?php

function W3GetAPIParamCount($aid) {
    global $w3API;

    if (!array_key_exists(w3ApiParams, $w3API[$aid])) {
        return 0;
    }

    return sizeof($w3API[$aid][w3ApiParams]);
}

function W3GetAPIParamArrayFromUI($uid) {
    global $w3UI;

    $paramArray = array ();
    if (!array_key_exists(w3PropApi, $w3UI[$uid]))
    {
        W3LogWarning("There is no API property defined for specifed uid: " + $uid);
        return $paramArray;
    }
    if (!array_key_exists(w3ApiParams, $w3UI[$uid][w3PropApi]))
    {
        W3LogWarning("There is no API param property defined for specifed uid: " + $uid);
        return $paramArray;
    }
    
    $arraySize = sizeof($w3UI[$uid][w3PropApi][w3ApiParams]);

    if ($arraySize < 1) {
        return $paramArray;
    }

    for ($i = 0; $i < $arraySize; $i++) {
        array_push($paramArray, $w3UI[$uid][w3PropApi][w3ApiParams][$i][w3ApiDataValue]);
    }
    
    return $paramArray;
}

function W3MakeUIEventString($key, $value) {
    $event = "";
    
    if ($key == "onclick") {
        $arraySize = sizeof($value);
        if ($arraySize < 1) {
            return "";
        } else if ($arraySize == 1) {
            $event = $value[0];
        } else {
            $event = "(function() {";
            for ($i = 0; $i < $arraySize; $i++) {
                $event .= $value[$i] . ";";
            }
            $event .= "})()";
        }
    }
    
    return $key . "=" . W3MakeString($event);
}

function W3GetUIEvent($uid) {
    global $w3UI;

    if (array_key_exists(w3PropEvent, $w3UI[$uid])) {
        $events = "";
        foreach ($w3UI[$uid][w3PropEvent] as $key => $value) {
            $events .=  W3MakeUIEventString($key, $value) . " ";
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

    if (array_key_exists(w3PropString, $w3UI[$uid]) and $w3UI[$uid][w3PropString] != "") {
        $language = W3GetLanguage();

        return $w3Lan[$language][$w3UI[$uid][w3PropString]];
    }

    return "";
}

function W3GetUIAttr($uid) {
    global $w3UI;

    if (array_key_exists(w3PropAttr, $w3UI[$uid])) {
        $attr = "";
        foreach ($w3UI[$uid][w3PropAttr] as $key => $value) {
            $attr = $attr . $key . "=" . $value . " ";
        }
        
        return $attr;
    }

    return "";
}

function W3InsertAttr($uid, $attrDict) {
    global $w3UI;

    if (array_key_exists(w3PropAttr, $w3UI[$uid])) {
        foreach ($attrDict as $key => $value) {
            if (!array_key_exists($key, $w3UI[$uid][w3PropAttr])) {
                $w3UI[$uid][w3PropAttr][$key] = $value;
            }
        }
    } else {
        $w3UI[$uid][w3PropAttr] = $attrDict;
    }
}

function W3InsertAPIParamAttr(&$api) {
    # Handle api parameter in form or button or other UI
    $paramSize = sizeof($api);
    if ($paramSize > 1) {
        for ($i = 0; $i < $paramSize; $i++) {
            $paramName = "name=" . W3MakeString($w3API[$api[w3ApiID]][w3ApiParams][$i][w3ApiDataValue], true);
            W3InsertAttr($api[w3ApiParams][$i][w3ApiDataValue], $paramName);
        }
    }
}

?>