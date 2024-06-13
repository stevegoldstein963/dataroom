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
define( 'DB_NAME', 'buildit_wp_apqju' );

/** Database username */
define( 'DB_USER', 'buildit_wp_pg74l' );

/** Database password */
define( 'DB_PASSWORD', 'Y8#kt0ZdW3UJZ$Zk' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

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
define('AUTH_KEY', '7c9Grq%BAH17apR#e-9u0/Kf&N&0A(id7i&0Xtme[5|]k7-1drj_@~!7-4gK[8A:');
define('SECURE_AUTH_KEY', 'WOAG169M6F9fmd*/x]%gM917*8X4*vJGP;P[+9VZzG]b2P|0ypy5o5-C28+L457f');
define('LOGGED_IN_KEY', 'w*R61IPMtz6a9Or8/n0c8gb!7b)V(&m62*D)be36xNLy#/1u~K-z+ro1NR%LNhj8');
define('NONCE_KEY', 'lP1*-wDTQxyp*L)70S5~5469;7hc%U9G8+pCK82OfMkUQe2]-77duJo4iHTrUUqC');
define('AUTH_SALT', 'h|3[xq*2p~29#/6P2q:OiJ]29&~lyvqg7nK40Wg9zk9;S#Qw|OvL~;7JM55/#~-H');
define('SECURE_AUTH_SALT', '808sG921-8n28r!#+|d9Q9&0m7~k/249Y1a7Dg(Icts(Fk_C*zOd2zD4-cy9HQJN');
define('LOGGED_IN_SALT', 'GH7%7281C*DN3~4B-d]p_1d*k+57HPGyQ-wc@y95jI4*F]0K7Jl5~xN6cAs3o5J~');
define('NONCE_SALT', '9~Yf%Pb:YO6f_294pKC16Gj%29@7473FZTv~m|&E:Kb9a9w&iO0f@I/v05g5L-D(');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'FAd7W_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', false);
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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
