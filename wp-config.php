<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'ro_infinit');

/** MySQL database username */
define('DB_USER', 'ro_infinit');

/** MySQL database password */
define('DB_PASSWORD', 'th1s1sSparta!');

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
define('AUTH_KEY',         'Tms#pI);Ie8=09)sCsy!PfYz78)M+*0MOjpx]l[if35O(.xZV_4I48[K(IYHJ__s');
define('SECURE_AUTH_KEY',  'muKSx].2aVHAU4>Y(y=)N.K;om-hGK[VP=Ea=|TO0zzwtJ)oxZ+2-T4NX+Ti$Y-^');
define('LOGGED_IN_KEY',    '+d#;=Ie|3|H+J7y[vG0uj&^cP;|+m97A7k!KuE4h!LC?$m}Yc0WS/VUrQ.KBm|E~');
define('NONCE_KEY',        '4_)KPQ2w*HYL`K~~mi!_m|~}a@<Lz|]/GZ{)H(P1#}diSmH)3tTx;ZV+m}cNBM6Z');
define('AUTH_SALT',        '8f|[=`8Aw!-JbWI^)[i4[^9].XDy%cIKb>@I*AUY!~N )de+ST0R8^-C3_l--h:[');
define('SECURE_AUTH_SALT', '!cOc}7qx<%`5cVpO+jtfYF7{m>G7-mt+~O}{(/A|eVc!M.uJD=}BBV]~3%6IZi;e');
define('LOGGED_IN_SALT',   '@/Z~Dd.+-j@y7-u1^RDQXK|om):c ?wG-l1$tN_Wdt`C!! vsa|(qsEXJxo9;-8l');
define('NONCE_SALT',       '66~U$1HR |+Es? puc<yy[6!s$uGDJ|Y/BN6t@oV-u]N|)n=Db!,!;r7MZqhr?}&');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'nftv_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
$base = '/';
define( 'DOMAIN_CURRENT_SITE', 'infinitive.local' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
