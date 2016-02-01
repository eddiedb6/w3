<?php

require "php/generated/const.php";
require "php/generated/api.php";
require "php/generated/ui.php";
require "php/generated/language.php";
require "php/W3RequestHandler.php";
require "php/W3PageSelector.php";
require "php/W3LanguageSelector.php";

function W3IsEmptyRequest() {
    return $_SERVER["REQUEST_URI"] == "/";
}

#
# Language Helper
#
function W3GetLanguage() {
    return w3LanEnglish; ## TODO
}


#
# UI helpers
#

function W3GetParamName($index) {
    return "param" . strval($index); 
}

function W3GetAPIParamArray($uid) {
    global $w3UI;

    $paramArray = array ();
    $arraySize = sizeof($w3UI[$uid][w3PropApi]);

    if ($arraySize <= 1) {
        return $paramArray;
    }

    for ($i = 1; $i < $arraySize; $i++) {
        $paramIndex = W3GetParamName($i);
        array_push($paramArray, $w3UI[$uid][w3PropApi][$paramIndex]);
    }
    
    return $paramArray;
}

function W3GetEvent($uid) {
    global $w3UI;

    if (array_key_exists(w3PropEvent, $w3UI[$uid])) {
        $events = "";
        foreach ($w3UI[$uid][w3PropEvent] as $key => $value) {
            $events .= $key . "='" . $value . "' ";
        }
        
        return $events;
    }

    return "";
}

function W3GetClass($uid) {
    global $w3UI;

    if (array_key_exists(w3PropClass, $w3UI[$uid])) {
        return "class='" . $w3UI[$uid][w3PropClass] . "'";
    }

    return "";
}

function W3GetBody($uid) {
    global $w3UI;
    global $w3Lan;

    if (array_key_exists(w3PropBody, $w3UI[$uid]) and $w3UI[$uid][w3PropBody] != "") {
        $language = W3GetLanguage();

        return $w3Lan[$language][$w3UI[$uid][w3PropBody]];
    }

    return "";
}

function W3GetAttr($uid) {
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

#
# UI Creators
#

function W3CreateLink($uid) {
    global $w3UI;

    W3CreateUIBasePro($uid);

    $type = "a";
    $body = "";
    $attr = "href='" . W3CreateAPI($w3UI[$uid][w3PropApi][w3ApiID], W3GetAPIParamArray($uid)) . "'";

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

    $type = "input";
    $body = "";
    $attr = "type='text'";

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
    if (array_key_exists(w3PropBody, $w3UI[$uid]))
    {
        $attr .= " value='" . $w3Lan[W3GetLanguage()][$w3UI[$uid][w3PropBody]] . "'";
        $w3UI[$uid][w3PropBody] = "";
    }
    
    return W3CreateUIBase($uid, $type, $body, $attr);
}

function W3CreateForm($uid) {
    global $w3UI;
    global $w3API;

    W3CreateUIBasePro($uid);

    $type = "form";

    $attr = "name='input' action='" . $w3API[$w3UI[$uid][w3PropApi][w3ApiID]][w3ApiName] .
          "' method='" . $w3UI[$uid][w3PropMethod] . "'";

    # Handle api parameter in form
    $paramSize = sizeof($w3UI[$uid][w3PropApi]);
    if ($paramSize > 1) {
        for ($i = 1; $i <= $paramSize - 1; $i++) {
            $paramIndex = W3GetParamName($i);
            $paramName = "name='" . $w3API[$w3UI[$uid][w3PropApi][w3ApiID]][$paramIndex] . "'";
            W3InsertAttr($w3UI[$uid][w3PropApi][$paramIndex], $paramName);
        }
    }

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
        $body .= "<tr id='" .$uid . "Header'>";
        $header = 0;
        foreach ($w3UI[$uid][w3PropTH] as $value) {
            $body .= "<th id='" . $uid . "Header" . strval($header) . "'>";
            $body .= W3CreateUI($value);
            $body .= "</th>";
            $header += 1;
        }
        $body .= "</tr>";
    }
    if (array_key_exists(w3PropTD, $w3UI[$uid])) {
        $count = sizeof($w3UI[$uid][w3PropTD]);
        for ($i = 0; $i < $count; $i++) {
            $body .= "<tr id='" . $uid . "Row" . strval($i) . "'>";
            $j = 0;
            foreach ($w3UI[$uid][w3PropTD][$i] as $value) {
                $body .= "<td id='" . $uid .
                    "Cell" . strval($i) . strval($j) . "'>";
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
               "id='" .$uid . "' " .
               W3GetEvent($uid) . " " .
               W3GetClass($uid) . " " .
               W3GetAttr($uid) . " " . $attr . ">" .
               trim(W3GetBody($uid) . " " . $body) .
               "</" . $type . ">\n";
}

function W3CreateUIBasePro($uid) {
    global $w3UI;

    # Copy everything from prototype 
    if (array_key_exists(w3PropPrototype, $w3UI[$uid])) {
        foreach ($w3UI[$w3UI[$uid][w3PropPrototype]] as $key => $value) {
            $w3UI[$uid][$key] = $value;
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
        }
    }
    
    return "";
}

 ?>