<?php
//require dependencies
require __DIR__ . '/vendor/autoload.php';

if(is_admin()){
    require_once ICALC_PATH . '/configuration/adminBar.php';
    require_once ICALC_PATH . '/configuration/admin_menu.php';
    require_once ICALC_PATH . '/frontend/admin-autoload.php';
}

require_once ICALC_PATH . '/util/utilities.php';
require_once ICALC_PATH . '/database/autoload.php';
require_once ICALC_PATH . '/controller/controllers.php';
require_once ICALC_PATH . '/frontend/displayTypes/autoload.php';
require_once ICALC_PATH . '/controller/icalcJWT.php';
require_once ICALC_PATH . '/integration/IcalcWordpressWidget.php';
require_once ICALC_PATH . '/integration/wordpress-integration.php';
require_once ICALC_PATH . '/integration/elementor-integration.php';

// Hook into the 'plugins_loaded' action to make sure WordPress core functions are available.
add_action('plugins_loaded', 'generate_site_specific_secret_key');
