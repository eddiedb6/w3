<?php

function W3HandleRequest() {
    #############################################
    # User function logic should be added below #
    #############################################

    # e.g. #
    if (W3IsRequest_page($_SERVER["REQUEST_URI"])) {
        require "W3Main.html";
        return true;
    }

    #############################################
    # User function logic should be added above #
    #############################################

    return false;
}

 ?>
