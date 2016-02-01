<?php

function HandleRequest() {
    #############################################
    # User function logic should be added below #
    #############################################

    if (IsRequest_page($_SERVER["REQUEST_URI"])) {
        require "Main.html";
        return true;
    }

    #############################################
    # User function logic should be added above #
    #############################################

    return false;
}

 ?>
