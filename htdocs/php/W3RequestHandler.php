<?php

function W3HandleRequest() {
    $request = $_SERVER["REQUEST_URI"];

    if (W3IsRequest_page($request)) {
        return W3OnRequestPage();
    }
    
    return W3APIHandleRequest();
}

 ?>
