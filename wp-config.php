<?php
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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ukijashtis' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '8c1e7c17adbcefe626a2eddd62551b84fb552928a2505a1f' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1:3306' );

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
define( 'AUTH_KEY',          'z3Su1uZyR*K[t5EKzI+2WWPbhOx3jHA+vfX;+.Ol_B3LR_35yd6yj2z@U9UIpj]/' );
define( 'SECURE_AUTH_KEY',   '(8 XNiN^Xb3Bl/(Yt]Rz/dhF{D.z9!0oo`3xO IV8jczda.Kh9Uib]|YFn;CB^?x' );
define( 'LOGGED_IN_KEY',     'WG]LKIoIs6r]RxxNZ z4J??Ykfd@|2Lqx)Y^fk8|/Ve.73#kd.u?!KESyITZ[O41' );
define( 'NONCE_KEY',         '^OikS`_waA|YU/kK!^FoS,1;m#reA=F*TA_c!7r#h@_<G;HXACB[*nCTle/&,Sjl' );
define( 'AUTH_SALT',         'ZOt?OfG=$/wrpIYpK ,SM,[s*w`^F4X`t4YWqN_g>-L?KWp#|:u@#]C|k^m=Rx2/' );
define( 'SECURE_AUTH_SALT',  'r?UA6%{^^tY6:mXP!g<2NbIlco^!B%0C.@0V<KI[r$Vg4!_cPEuuyoNeVdI|5:Kh' );
define( 'LOGGED_IN_SALT',    ';&DiWBkNAsVN?e!Gxc3%r9E?H(kO)h@!A}@;P*|GAnPxwEgd$ZFEc?aTe/qGLBK0' );
define( 'NONCE_SALT',        'X|;S`E>W[/)[br9bnP~EwE@O:]X_MDsty&ZV5C<E5],`)@rsIH|rP~(=,x>>J--$' );
define( 'WP_CACHE_KEY_SALT', '|}qiA1:J**5UHN<c )g0.?.(180a]pE;qT,d&;1+YDQ-aL$=={d/:95#XYvKz.Hy' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );


/* Add any custom values between this line and the "stop editing" line. */



define( 'FS_METHOD', 'direct' );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
define( 'WP_DEBUG_LOG', true );
define( 'CONCATENATE_SCRIPTS', false );
define( 'AUTOSAVE_INTERVAL', 600 );
define( 'WP_POST_REVISIONS', 5 );
define( 'EMPTY_TRASH_DAYS', 21 );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
