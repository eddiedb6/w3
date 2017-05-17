<?php

function W3HandleRequest() {
    if (W3IsRequest_page($_SERVER["REQUEST_URI"])) {
        require "W3Main.html";
        return true;
    }

    return W3APIHandleRequest();
}

 ?>
