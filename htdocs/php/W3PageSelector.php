<?php

function W3SelectPage() {
    #############################################
    # User function logic should be added below #
    #############################################

    # e.g. #
    global $w3UI;

    if (W3IsEmptyRequest()) {
        require $w3UI["uidPageDebug"][w3PropFile];
    } else {
        $errorPage = $w3UI["uidPageError"][w3PropFile];

        if (!preg_match(W3CreateAPIReg("aidPage"), $_SERVER["REQUEST_URI"], $matches)) {
            require $errorPage;
            return;
        }
        if (!array_key_exists($matches[1], $w3UI)) {
            require $errorPage;
            return;
        }
        if (!array_key_exists(w3PropFile, $w3UI[$matches[1]])) {
            require $errorPage;
            return;
        }

        require $w3UI[$matches[1]][w3PropFile];
    }

    #############################################
    # User function logic should be added above #
    #############################################
}

 ?>