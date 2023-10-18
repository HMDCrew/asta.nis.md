<?php


add_filter( 'xmlrpc_enabled', '__return_false' );

// Helpers WordPress
require_once __DIR__ . '/inc/helpers.php';

// Assets WordPress
require_once __DIR__ . '/inc/assets.php';

// Filters WordPress
require_once __DIR__ . '/inc/asta-filters.php';

// Actions WordPress
require_once __DIR__ . '/inc/asta-actions.php';

// Widgets WordPress
require_once __DIR__ . '/inc/widgets.php';

// User roles WordPress
require_once __DIR__ . '/inc/user-roles.php';
