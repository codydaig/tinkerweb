<?php

class Framework {

    // __construct
    // Loads the Core Framework
    function __construct() {
        
        // Require config.php (Secure website config info)
        require_once '../globals/config/config.php';

        // Require vars.php (Nonsecure Website Variables)
        require_once '../globals/config/vars.php';

        // Connect to the MySql Database
        mysql_connect($SQLHOST, $SQLUSER, $SQLPASS);
        mysql_select_db($SQLDB);
        
        
    }

}

?>
