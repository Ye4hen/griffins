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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress3' );

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
define( 'AUTH_KEY',         '#IU>YaW^s<Cb8bK9*AG-a!Go1,1E*|Z,sO<^V[x.]$9bi[Jbsued-zAkNx79Vm_6' );
define( 'SECURE_AUTH_KEY',  '?zrN-TSima5#CadSj8<lD;2hXf@?Ej,~pc6WZpd|9@9#?Gy*1h8XA=G{5X_g<GL}' );
define( 'LOGGED_IN_KEY',    '?&wl@Kc4!NwN=}s$02FVhl2+2u/stD%<$@D5>(mtOf2Oa-Xz}pAX,Tq%Q0ht7=Ax' );
define( 'NONCE_KEY',        'Lb39yIUb/KTL|n>@fs5zT51uI@x4T}kbb97@]FQck*tG;OuZ ,jRkq}/6)bQ+4>[' );
define( 'AUTH_SALT',        ',:)(y2#yakOrQIN{R#b!UzT=AH]xZ]GK+Ncj$T6lr@6#j2[sFR+DR[M;j!bC~SnH' );
define( 'SECURE_AUTH_SALT', 'Hi0].ungg+Nl&0zE-1[J?% VW<p {eyvt0d>PF[_|.xgG## ijol?sM7EqN6^vE#' );
define( 'LOGGED_IN_SALT',   's&ZsVK{qN|I*MYgO;|hwn~ENWog)qd9)*1kdn,0:9~4gg( pWycW3_T:=$T9S6Rh' );
define( 'NONCE_SALT',       'KD=UFO#sjm+/emXRGZrpf~ <Jy*M4a_8`0^)E:U=dr5|H1QO1]fZWJsV~+EAo2-k' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
