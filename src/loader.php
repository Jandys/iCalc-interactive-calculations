<?php

if(is_admin()){
    require_once ICALC_PATH . '/configuration/adminBar.php';
    require_once ICALC_PATH . '/configuration/admin_menu.php';
    require_once ICALC_PATH . '/frontend/admin-autoload.php';
}

require_once ICALC_PATH . '/util/utilities.php';
require_once ICALC_PATH . '/util/script-require.php';
require_once ICALC_PATH . '/database/autoload.php';
require_once ICALC_PATH . '/controller/controllers.php';
require_once ICALC_PATH . '/frontend/displayTypes/autoload.php';
