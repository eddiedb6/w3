<?php

#
# This is the main entry for all request
# So any global variable, function or any include could be used here
#

require "php/W3.php";

if (W3IsEmptyRequest()) {
    W3OnRequestPage();
} else {
    if (!W3HandleRequest()) {
        $msg = "NOT handled request: " . $_SERVER["REQUEST_URI"];
        W3LogError($msg);
        echo $msg;
    }
}

 ?>