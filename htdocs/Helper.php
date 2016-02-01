<?php

require "php/generated/const.php";
require "php/generated/api.php";
require "php/generated/ui.php";
require "php/generated/language.php";
require "php/RequestHandler.php";
require "php/PageSelector.php";
require "php/LanguageSelector.php";

function IsEmptyRequest() {
    return $_SERVER["REQUEST_URI"] == "/";
}

#
# Language Helper
#
function GetLanguage() {
    return lanEnglish; ## TODO
}


#
# UI helpers
#

function GetParamName($index) {
    return "param" . strval($index); 
}

function GetAPIParamArray($uid) {
    global $ui;

    $paramArray = array ();
    $arraySize = sizeof($ui[$uid][propApi]);

    if ($arraySize <= 1) {
        return $paramArray;
    }

    for ($i = 1; $i < $arraySize; $i++) {
        $paramIndex = GetParamName($i);
        array_push($paramArray, $ui[$uid][propApi][$paramIndex]);
    }
    
    return $paramArray;
}

function GetEvent($uid) {
    global $ui;

    if (array_key_exists(propEvent, $ui[$uid])) {
        $events = "";
        foreach ($ui[$uid][propEvent] as $key => $value) {
            $events .= $key . "='" . $value . "' ";
        }
        
        return $events;
    }

    return "";
}

function GetClass($uid) {
    global $ui;

    if (array_key_exists(propClass, $ui[$uid])) {
        return "class='" . $ui[$uid][propClass] . "'";
    }

    return "";
}

function GetBody($uid) {
    global $ui;
    global $lan;

    if (array_key_exists(propBody, $ui[$uid]) and $ui[$uid][propBody] != "") {
        $language = GetLanguage();

        return $lan[$language][$ui[$uid][propBody]];
    }

    return "";
}

function GetAttr($uid) {
    global $ui;

    if (array_key_exists(propAttr, $ui[$uid])) {
        return $ui[$uid][propAttr];
    }

    return "";
}

function InsertAttr($uid, $attr) {
    global $ui;

    if (array_key_exists(propAttr, $ui[$uid])) {
        $ui[$uid][propAttr] = $ui[$uid][propAttr] . " " . $attr;
    } else {
        $ui[$uid][propAttr] = $attr;
    }
}

#
# UI Creators
#

function CreateLink($uid) {
    global $ui;

    CreateUIBasePro($uid);

    $type = "a";
    $body = "";
    $attr = "href='" . CreateAPI($ui[$uid][propApi][apiID], GetAPIParamArray($uid)) . "'";

    return CreateUIBase($uid, $type, $body, $attr);
};

function CreateLabel($uid) {
    global $ui;

    CreateUIBasePro($uid);

    $type = "label";
    $body = "";
    $attr = "";

    return CreateUIBase($uid, $type, $body, $attr);
};

function CreateCheckbox($uid) {
    global $ui;

    CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='checkbox'";

    return CreateUIBase($uid, $type, $body, $attr);
}

function CreateDatePicker($uid) {
    global $ui;

    CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='date'";

    return CreateUIBase($uid, $type, $body, $attr);
}

function CreateButton($uid) {
    global $ui;

    CreateUIBasePro($uid);

    $type = "button";
    $body = "";
    $attr = "type='button'";

    return CreateUIBase($uid, $type, $body, $attr);
}

function CreateText($uid) {
    global $ui;

    CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='text'";

    return CreateUIBase($uid, $type, $body, $attr);
}

function CreateCombobox($uid) {
    global $ui;

    CreateUIBasePro($uid);

    $type = "input";
    $body = "";
    $attr = "type='text'";

    return CreateUIBase($uid, $type, $body, $attr);
}

function CreateSubmit($uid) {
    global $ui;
    global $lan;

    CreateUIBasePro($uid);
    
    $type = "input";
    $body = "";
    $attr = "type='submit'";

    # put body string as value attr for submit
    if (array_key_exists(propBody, $ui[$uid]))
    {
        $attr .= " value='" . $lan[GetLanguage()][$ui[$uid][propBody]] . "'";
        $ui[$uid][propBody] = "";
    }
    
    return CreateUIBase($uid, $type, $body, $attr);
}

function CreateForm($uid) {
    global $ui;
    global $api;

    CreateUIBasePro($uid);

    $type = "form";

    $attr = "name='input' action='" . $api[$ui[$uid][propApi][apiID]][apiName] .
          "' method='" . $ui[$uid][propMethod] . "'";

    # Handle api parameter in form
    $paramSize = sizeof($ui[$uid][propApi]);
    if ($paramSize > 1) {
        for ($i = 1; $i <= $paramSize - 1; $i++) {
            $paramIndex = GetParamName($i);
            $paramName = "name='" . $api[$ui[$uid][propApi][apiID]][$paramIndex] . "'";
            InsertAttr($ui[$uid][propApi][$paramIndex], $paramName);
        }
    }

    $body = "";
    if (array_key_exists(propList, $ui[$uid])) {
        foreach ($ui[$uid][propList] as $value) {
            $body .= CreateUI($value);
        }
    }
    
    return CreateUIBase($uid, $type, $body, $attr);
}

function CreateTable($uid) {
    global $ui;

    CreateUIBasePro($uid);

    $type = "table";

    $body = "";
    if (array_key_exists(propTH, $ui[$uid])) {
        $body .= "<tr id='" .$uid . "Header'>";
        $header = 0;
        foreach ($ui[$uid][propTH] as $value) {
            $body .= "<th id='" . $uid . "Header" . strval($header) . "'>";
            $body .= CreateUI($value);
            $body .= "</th>";
            $header += 1;
        }
        $body .= "</tr>";
    }
    if (array_key_exists(propTD, $ui[$uid])) {
        $count = sizeof($ui[$uid][propTD]);
        for ($i = 0; $i < $count; $i++) {
            $body .= "<tr id='" . $uid . "Row" . strval($i) . "'>";
            $j = 0;
            foreach ($ui[$uid][propTD][$i] as $value) {
                $body .= "<td id='" . $uid .
                    "Cell" . strval($i) . strval($j) . "'>";
                $body .= CreateUI($value);
                $body .= "</td>";
                $j += 1;
            }
            $body .= "</tr>";
        }
    }
    
    $attr = "";

    return CreateUIBase($uid, $type, $body, $attr);
};

function CreateUIBase($uid, $type, $body, $attr) {
    return "<" . $type . " " .
               "id='" .$uid . "' " .
               GetEvent($uid) . " " .
               GetClass($uid) . " " .
               GetAttr($uid) . " " . $attr . ">" .
               trim(GetBody($uid) . " " . $body) .
               "</" . $type . ">\n";
}

function CreateUIBasePro($uid) {
    global $ui;

    # Copy everything from prototype 
    if (array_key_exists(propPrototype, $ui[$uid])) {
        foreach ($ui[$ui[$uid][propPrototype]] as $key => $value) {
            $ui[$uid][$key] = $value;
        }
    }
}

function CreateUI($uid) {
    global $ui;

    if (array_key_exists(propType, $ui[$uid])) {
        if ($ui[$uid][propType] == typeTable) {
            return CreateTable($uid);
        } else if ($ui[$uid][propType] == typeLink) {
            return CreateLink($uid);
        } else if ($ui[$uid][propType] == typeLabel) {
            return CreateLabel($uid);
        } else if ($ui[$uid][propType] == typeCheckbox) {
            return CreateCheckbox($uid);
        } else if ($ui[$uid][propType] == typeDatePicker) {
            return CreateDatePicker($uid);
        } else if ($ui[$uid][propType] == typeButton) {
            return CreateButton($uid);
        } else if ($ui[$uid][propType] == typeForm) {
            return CreateForm($uid);
        } else if ($ui[$uid][propType] == typeSubmit) {
            return CreateSubmit($uid);
        } else if ($ui[$uid][propType] == typeText) {
            return CreateText($uid);
        } else if ($ui[$uid][propType] == typeCombobox) {
            return CreateCombobox($uid);
        }
    }
    
    return "";
}

 ?>