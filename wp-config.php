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
define('DB_NAME', 'fsi');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         '|Qt;w}h;S8DqS=Lycms6+S//[6Z7.gMS|#`gGEP}-0Ro~,ewOKR5^n}?.?VM->`V');
define('SECURE_AUTH_KEY',  '{0WF/.;rGAK$x8Qf~c~M~{B36+NOkn`q*+Pn[sG*%gaK=eg(nE75|T.7pwAM29lM');
define('LOGGED_IN_KEY',    '-];cc06K_y 2wWq7HmaGm(.OtH|u-|{sFQo*w~7fIJEL&Jb4-<I!bs1s>B(zL~pp');
define('NONCE_KEY',        'z.(^-(15iCBTT*@3:M!A:QZ,WGSGphse)VA1Te_]a<*0Ah#s#I|xGoU;|Hh(OAg2');
define('AUTH_SALT',        'KXdM_?N-Yon+hQ0Tq9fbtc/1.xe]@N1-{gnzfGcdSRLF=w^H^_+}U)x8~,pOs A+');
define('SECURE_AUTH_SALT', 'OEE|CB ?J4}Pw/:]u2tR[ eq2-{Rm34$/lR)RA_$|<.)Nz|+#ld,45pq^mNq)8`2');
define('LOGGED_IN_SALT',   '5CD(H2<9L~rNd#*4;xg>SU2[{r5qh#ed;9NI55lJ^A!|=d$~t+ZIn8Y!j|m@LD^`');
define('NONCE_SALT',       '7FdeGWz89_gHgHJC%#`yq9^Ns$-3(1|9#9uk|:x+l682/|-o^B&NfPY+ ka=McF}');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
