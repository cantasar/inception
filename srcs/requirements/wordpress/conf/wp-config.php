<?php
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
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'ctasar' );

/** Database password */
define( 'DB_PASSWORD', '12345' );

/** Database hostname */
define( 'DB_HOST', 'mariadb' );

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
define('AUTH_KEY',         '^j6=.Iz]bCe$g_q#4CHCHn eH.mICAH6d8jb+Ti8 <Yhuhne5`(D.^Wwx%zF> |U');
define('SECURE_AUTH_KEY',  'mxvu@6~Iqty!+veNt+<6aBA#baFf_j4qP@0s_#vrvWy,t9Y_TrKTpej)=NPb*r!_');
define('LOGGED_IN_KEY',    'Mj-~gnW5|WhY] *mXYLn9DLKWI[~*?xV{/+c(wZ&BiisO8^{I(>>_q[3#UPVp~jl');
define('NONCE_KEY',        'GvTVW(*}72nl+uFaa),8|YL~a{+<93)FZo+xm/dqM+RY[lim<x!Vh$Hs++Y2[GK-');
define('AUTH_SALT',        'G5G[k=#h]v.|SXJGf!P]V~,@LKaZ(](sq2yjVMvnBkAbze(Wl)e+($BK]860-`wn');
define('SECURE_AUTH_SALT', ' }(ERlq+srd&[Xn(+YG?#ue|7:S:4;!*Kn@w=8z:Y|YC:(==kQgXVhem}HGB =l-');
define('LOGGED_IN_SALT',   '854yB.<{$|>D/f@.]6]6M+dX5U9dIC+h!zIIh8(-mCLWF8DhFTr~`wF[^*nFl+x<');
define('NONCE_SALT',       '.?&sAx3~17%0k>q]S5Iq-<0~+~x?mJ`gUT $Ew-N=RHP}u;5IM!L,XvtQy>nJ%G_');

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