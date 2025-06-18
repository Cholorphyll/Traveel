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
define('DB_NAME', 'wordpress_4');

/** MySQL database username */
define('DB_USER',       'wordpress_5');

/** MySQL database password */
define('DB_PASSWORD',       'm!11DpYmU6');

/** MySQL hostname */
define('DB_HOST', 'localhost:3306');

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
define('AUTH_KEY',       '4c#7TU41@v#yikt&AdVg)EM#6Bg3w#HfP%s1SmQwN(ao*7faOu%FRNHF^XMb6kop');
define('SECURE_AUTH_KEY',       'LG1wK*jBfYF*JlKnAyUzERU3Frmld&NReQ0yHtP%&@7!UC(nm@^IYo8PF#Jp3pIR');
define('LOGGED_IN_KEY',       's6ZXloo6KuDt0W#%CAE@h1WJz(Rr%OmUF15V&QWQ#KaZP*0In2@WFo0n7OEpaF%s');
define('NONCE_KEY',       '9aFioM!Q6nbXyeAjnc(%gHpYgA8EkoM9v(yFaN64V)cO#PDCPRX4y2I7SSFGLWK%');
define('AUTH_SALT',       '&^6ZbQD4p4holzCBs*L0JGaGDRUVgMD*x)l3!DmS2R0N7JubevaYI(2&@H#NmECZ');
define('SECURE_AUTH_SALT',       'E)5seA)8UYyhTvC@mHr001htDK&dz)8bjnM*ehN9D!OfEWaZqIPxM%JX8a8IEkTx');
define('LOGGED_IN_SALT',       'dRUB)Q*K7LN9fY#lRjNIph^IUAKx!GhFi6ulpnntMYhxXyYruwmbTmEPGLTraIS#');
define('NONCE_SALT',       'nNE1(lx6W(JOt)Sea8osNMALLVv6yFdSB%n^OKLirWEE4VXd9b5x7raVEg!PkKZe');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'hc9k8aqv_';

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

define( 'WP_ALLOW_MULTISITE', true );

define ('FS_METHOD', 'direct');
