<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'hccworld');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'HutsSP5Om<4#{j,D@kOwx:6*lYU7G|?!oY:m5(+1(%!m1%-I9r7Y ~c,F/3c5Iid');
define('SECURE_AUTH_KEY',  ']s,@Jz4hPbj7p96!q|%1f~N5VLTpHw5rr4t=,&LzYR67(q`Z 3!1VdySE2)oFE35');
define('LOGGED_IN_KEY',    'IvI:Oz]~E&q5>|AqpFIq%RVLyKbzvH6l/3,}lEl=V1v1UH6fnl.9%2Dc>G)mH;*~');
define('NONCE_KEY',        't|_5L0.r$U.dl>GgB B2f@< oc,;{]@lY;/<=~y~9kt/t#AJkEHgX~[)O h`4B`3');
define('AUTH_SALT',        'qQc`z5p^w28b>q0(*{ MEM2&J!ET9$^oFWU^0Ja_9)wvx!*l;&n~kxUc6Bo26T6&');
define('SECURE_AUTH_SALT', 'b:r1#[c`?fH5ffd{)xhc o{mDZpEA(N90>ST43U!8MX;wM^!AX[-NRn@VuNr%&7;');
define('LOGGED_IN_SALT',   '-Un)(;YRP0vQ)_,4Z[GYPBk^QGO8#?XsmnAPZXQVeZ]bj3Qzbj?)Fb^PBuxZT#Oa');
define('NONCE_SALT',       '^>Nq$RCY0+C }t)vf6$n7df6@eZZv,&.Z0D.14ag?o/^hyNM8_%&jJ9.&~{OJ8BR');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
