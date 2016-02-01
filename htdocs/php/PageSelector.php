<?php

function SelectPage() {
    #############################################
    # User function logic should be added below #
    #############################################

    # e.g. #
    global $ui;
    if (IsEmptyRequest()) {
        require $ui["uidPageDebug"][propFile];
    } else {
        $errorPage = $ui["uidPageError"][propFile];
        if (!preg_match(CreateAPIReg("aidPage"), $_SERVER["REQUEST_URI"], $matches)) {
            require $errorPage;
            return;
        }
        if (!array_key_exists($matches[1], $ui)) {
            require $errorPage;
            return;
        }
        if (!array_key_exists(propFile, $ui[$matches[1]])) {
            require $errorPage;
            return;
        }
        require $ui[$matches[1]][propFile];
    }

    #############################################
    # User function logic should be added above #
    #############################################
}

 ?>