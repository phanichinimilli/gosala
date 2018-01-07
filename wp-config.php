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
define('DB_NAME', 'gsdb');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'eC_v/^U<i(LLWWn_[0b|GBP$@lXZAp=U*;<<9.Eo%N0 pH|>6,/Qv{$3t&i./{|C');
define('SECURE_AUTH_KEY',  'iX=`K2*!jG;6Qa`#,~EVG|^/Ry6[Pjix!3qn!/6y~y:C%;Uk-3Y~}rTQk?HJxJ%}');
define('LOGGED_IN_KEY',    'AtvS!4t]s>FA;1!xwmMFoYVUL~%]OW+XB0l&J7R7FWwk(JUfr66^Bi3,RN*QmY`x');
define('NONCE_KEY',        'KNm!gJ|$HiKq@gvU8[RdQ=nbQKwV2 wjDh1VD;(_%D9Iwjrf.)`qFdlR%nxoRLUr');
define('AUTH_SALT',        'fkF05tD4%K#KU-#L8z:`IC*]m/J%@bLzO5P|_U2W>8kYHQng~8A?jBQkq4CK,AY#');
define('SECURE_AUTH_SALT', 'h/v@L;Sz=9[tXs>LV^)c6Yf^4.F0X9yV#b[2UF9yn1&NOF4 w`L*:RVenPB ,y:Z');
define('LOGGED_IN_SALT',   '4T9C,i6io6l_8HEM6--}b#2Q[^&zS?1L<P?X*Nss*hVzJuh18}EY@TBt$GX5AjNC');
define('NONCE_SALT',       'z$OGhXoS[JdHVj8091%c-A{*?p?%6:t^1Wju&z<N/KNkysQ-7u%&b*Mh&]zKH<6A');

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
