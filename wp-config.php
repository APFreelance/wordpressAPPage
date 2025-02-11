<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpressaprestaurado' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'hTcT:0Zq&BxgQ<K^R56C&dE;ldqWL$ 5*XU:CO74b!gnSp0uR+/WHU:{qjX7ElDm' );
define( 'SECURE_AUTH_KEY',  ');Yuy@JjQ_*hhJGK@?o>(h2[,+(TW(#5G$])smG>QylVA(W w@>+q*^,Ub?f`iA4' );
define( 'LOGGED_IN_KEY',    'O-S+G#(8#|4t?g7^xT1nEf$hUqW/zDT( FNi,(vnK(hDE{=={%B[J!+#:_,g#Ly9' );
define( 'NONCE_KEY',        'zxMtr(8)6;uCM%:W{L|?5-H|I#?7FqK4]%>cTj^y>B0>ju*|-[(H&uj;CVy5Z|6^' );
define( 'AUTH_SALT',        'v+ICkyX^kj8<+)Zq;8l0uT.%RP&B~H#S|G%ocq3z[)v~D-o7_Do(O-~5BP^Eh1,1' );
define( 'SECURE_AUTH_SALT', '{c}fM-*1yY<i$HhXLF@%+Z!xb4QjRiha@1)1HKlpyS6GK6-(k1o!?19,s-;$rv@C' );
define( 'LOGGED_IN_SALT',   'Q:ci7d8c<fe ulRIz_mX<]dEsmt5Nst(,i/=HPy;07;<JbZNKG|s@nW 9L9z4KLj' );
define( 'NONCE_SALT',       'x ,(Kj#(td/_ 9 HofPv3-rjb8%?BT(Q>M5z}0_]X]bs&Elv)buyL7_Th;=ga3)K' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
