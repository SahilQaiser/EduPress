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
define( 'DB_NAME', 'edupress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'mysql' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '&>AvZj<GsOPxUw0bdw]q:. z=!N.gr7I6U-,hm8j<J,WS:N}n<.!I9}SY|R>H,%,' );
define( 'SECURE_AUTH_KEY',  '!8HL]DY=i[%c*O_kFujaCZ3CCgN|eb*]Q}6n7mixM%0V=Nsgk9._jd4LKm:sLysJ' );
define( 'LOGGED_IN_KEY',    'H(#c>#FH4{7ntGU8<[$GI~2M*<r@hxBsE%M#mnt(jB)d@WJC9G^Q.KjNq#<mx/74' );
define( 'NONCE_KEY',        '2.^NG-uobAL]DSpg$nvO/,/dYDdhl_+}Y8;jU6y(1+4H|.&?D8cKjmx1AI!_G-1Y' );
define( 'AUTH_SALT',        'Nqo03+|duEayGwB_r[0VJv`/qf[FGVJ=:<pvC3w-W#WxUiuP{v#Vnr/$ *ww4W.J' );
define( 'SECURE_AUTH_SALT', ';?OBEx+tU6.r[88oQBR-b88FI<]UP+Z9.~`0>`WHvP8rK#-@MpS^qVH>I;$7Yi$z' );
define( 'LOGGED_IN_SALT',   'prt.j+${#dQ/7Wi;rAjBmO/HR=IB^29E{[S-B(jc@wKaz4P LY&sU^SaR5~^^Qhw' );
define( 'NONCE_SALT',       'pwbnMtS.iKK7jWqj]{VWp|5h*gs2}lO%Dhnp2P`s8d]9a}1eWmp YsWf@>%RD8/S' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'edu_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
