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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'practice' );

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
define( 'AUTH_KEY',         '`hcR+nGlw58(?tyGR-<gJW++iFcT#a%:Ni70>9iqM6Z47)&;rf$UNDD2|uxSODX/' );
define( 'SECURE_AUTH_KEY',  'AxntVjFtbWBuPS2d(I2u5y{G%f<e3Ny~Azr[k=2;;hb&8_[2:W=CZ|Gu%?U5kXkp' );
define( 'LOGGED_IN_KEY',    'TE;Laa3*GHD^emPH0S06.:oHkIs%RC!2Uey9(WD4.kt7oH``bw2YIKBACa}Qsg4w' );
define( 'NONCE_KEY',        'D/X?S)FZ+z)wqPv? dth1Wi]Q pNDw2LA(U1Y%d+RQvi~`0Qvc`</TU@c}2=K3MJ' );
define( 'AUTH_SALT',        'kyfws*uM<M0N}&.m&J0.U)An-BLeT$ESl3~RbMnOvjJFu4T{6Ur65%Y!3yzN_R}+' );
define( 'SECURE_AUTH_SALT', '0Bo)MP-P%Rp[`?=@=v}-FF)o;8O7mTWP fGPL|WAjH`]Zg$0y[)WV9j bX1A8?qv' );
define( 'LOGGED_IN_SALT',   'YOdGF$7_%O3[iHyEtcyk5F$QAr.Fc)d|kk^{6HmnK)ef|a<IgH41u=S~A~$8eI^.' );
define( 'NONCE_SALT',       '!z=xkf+#+h72.ianKl7NaY~GUWk%s]MNn3&)IrJzBljB-piA_b1i;<[8]kd7GOq4' );

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
