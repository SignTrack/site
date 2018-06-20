<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'signtrack');

/** MySQL database username */
define('DB_USER', 'webteam');

/** MySQL database password */
define('DB_PASSWORD', 'tDm4E5GtKCPrzquF');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'q^C9NBLPx2)wZ:WkO&{?jws!sQ!aEhCFC: E0jX^UTgPP$3EAu&35uw3`uF_f$T3');
define('SECURE_AUTH_KEY',  'NF36|o16hhO)MBZ94d1l)fp{f#{hhlZE(kT4RHojVd^lfwt{aPl8oG;YoYSD{rJ_');
define('LOGGED_IN_KEY',    '|-4%?H]}Y)6e${.) E=dbU&mL)h4LYrvh(----#V1[e$`fz;-HbeDU)b867~-`hd');
define('NONCE_KEY',        '=,{w#pxrAx+|qR@1UBW0%&|B%DQ6JuY <(->~JbaFG/!-S-#Q.)EKjl.ao%G|!;j');
define('AUTH_SALT',        'j:bLZR8UPNVEzM?P8)oLHG@(d?j#*9zkG&)T$Qe#^{[]Z^w_kz|&dySi?PcVe+BB');
define('SECURE_AUTH_SALT', 'f(jJb,UE9@#N2%4QM8~YiX8/-V#E+=?#9QC8dGuPMq.aRYS:N%*3(F-@iOik+AlQ');
define('LOGGED_IN_SALT',   '%D@p~[8P2}:N*,K+iKcUIXn3??ye5/s.}}BQxT}$^B~[sA+bZ5(65n-Z!C]NZjEs');
define('NONCE_SALT',       ']~e ?x+nWwq#EH5=PrXuD_w]hAuMr`rIXCrY,]9E[%v!8#0 b}Y}-JaxBM&TcS/`');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
