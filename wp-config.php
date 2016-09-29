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
define('DB_NAME', 'wordpress_prod');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'u8IJolv6FElxNrEo');

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
define('AUTH_KEY',         'QM1bu(hWDG03q_Q3*TD)5On<cso7I-H2n&R/GDSY2Ux||$5xB7wS*B^#JZaXK11F');
define('SECURE_AUTH_KEY',  '?E1t{Se88;Z:/X~/U/lt%~bqfLq1_PkI*AH]*,8w=f vWYlAp-]Br90^+.^LC|Do');
define('LOGGED_IN_KEY',    '}igt|a$J>R0EbmL_t[o8:/#b;#[#6TTj]F?{`-B(S-dBQq3aEyx)Sd~mVY]2uUIH');
define('NONCE_KEY',        'bFj*e`mNRM;%}&C#3~!j.u :.OXe8x|JC*rIble-FDb.f8w@nNDi[g-YK24X&>hR');
define('AUTH_SALT',        'utT<:.z.l=2h)sPYR]b^k&]eGjsx-l&q.9Lz+kn9q6D8A/rpNS=<j/8OjI:PfpZm');
define('SECURE_AUTH_SALT', 'B[ ZUW![!ts9qZsY@@y-UwR1Npoa/VK`*|~L?JxVOURp8l%-}ynQcb^OB-p> .Za');
define('LOGGED_IN_SALT',   '>uNjUxG{HkKq@*$~1q(qOZ3P%]!+,@mN|P{p8QUf/2=~sB}P &c,)eD)*I6uB+I ');
define('NONCE_SALT',       '{@(O6#XErUPVK,p[:3tA*GJqBfT%m8AR/b=~TC2V+bl2[T6*LE<6[uo6`FS$b*-6');

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

define('WP_ALLOW_MULTISITE', true);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'malivi.ru');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
