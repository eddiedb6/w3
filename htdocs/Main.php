<?php

#
# This is the main entry for all request
# So any global variable, function or any include could be used here
#

require "Helper.php";

if (IsEmptyRequest()) {
    require "Main.html";
} else {
    if (!HandleRequest()) {
        echo "NOT handled request: " . $_SERVER["REQUEST_URI"];
    }
}

 ?>