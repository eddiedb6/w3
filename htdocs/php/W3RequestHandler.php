<?php

function W3HandleRequest() {
    $request = $_SERVER["REQUEST_URI"];

    if (W3IsRequest_page($request)) {
        return W3OnRequestPage();
    }
    
    if (W3IsRequest_token($request)) {
        return W3OnRequestToken();
    }

    // These are the only API do not need encryption above
    // Other APIs need decryption first in following handler
    
    return W3APIHandleRequest();
}

 ?>
