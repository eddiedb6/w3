<?php

#
# This is the main entry for all request
# So any global variable, function or any include could be used here
#

require "W3Helper.php";

if (W3IsEmptyRequest()) {
    require "W3Main.html";
} else {
    if (!W3HandleRequest()) {
        echo "NOT handled request: " . $_SERVER["REQUEST_URI"];
    }
}

 ?>