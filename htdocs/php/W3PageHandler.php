<?php

function W3LoadPage() {
    echo W3CreateUI(w3UIBody);
}

function W3SelectPage() {
    global $w3UI;

    if (W3IsEmptyRequest()) {
        $defaultPageID = $w3UI[w3UIBody][w3PropDefaultPage];
        if (array_key_exists($defaultPageID, $w3UI)) {
            return W3CreateUI($defaultPageID);
        }
    } else {
        if (preg_match(W3CreateAPIReg("aidPage"), $_SERVER["REQUEST_URI"], $matches) and 
            array_key_exists($matches[1], $w3UI)) {
            return W3CreateUI($matches[1]);
        }
    }

    return W3CreateUI($w3UI[w3UIBody][w3PropDefaultErrorPage]);
}

 ?>