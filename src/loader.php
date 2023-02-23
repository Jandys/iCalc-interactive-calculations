<?php

if(is_admin()){
    require_once ICALC_PATH . '/configuration/adminBar.php';
    require_once ICALC_PATH . '/configuration/admin_menu.php';
    require_once ICALC_PATH . '/frontend/AdminFrontend.php';
}

require_once ICALC_PATH . '/util/utilities.php';
require_once ICALC_PATH . '/configuration/configuration.php';
require_once ICALC_PATH . '/database/autoload.php';
