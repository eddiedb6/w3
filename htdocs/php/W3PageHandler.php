<?php

function W3LoadPage() {
    $session = W3GetSession();
    $js = "<script type='text/javascript'> var " . w3Session . " = {" .
        W3MakeString(w3VariableValue, true) . ": " . W3MakeString($session, true) . "," .
        W3MakeString(w3VariableListeners, true) . ": {}" .
        "}; </script>";
             
    echo $js . W3CreateUI(w3UIBody, NULL);
}

function W3SelectPage() {
    global $w3UI;

    if (W3IsEmptyRequest()) {
        $defaultPageID = $w3UI[w3UIBody][w3PropDefaultPage];
        if (array_key_exists($defaultPageID, $w3UI)) {
            return W3CreateUI($defaultPageID, NULL);
        }
    } else {
        if (preg_match(W3CreateAPIReg("aidPage"), $_SERVER["REQUEST_URI"], $matches) and 
            array_key_exists($matches[1], $w3UI)) {
            return W3CreateUI($matches[1], NULL);
        }
    }

    return W3CreateUI($w3UI[w3UIBody][w3PropDefaultErrorPage], NULL);
}

 ?>
