<?php
/**
 * WordPress session manager by Eric Mann - thank you man
 *
 * Taken from:  http://jumping-duck.com/wordpress/plugins
 * Author:      Eric Mann, http://eamann.com
 */

// Safely include the needed files..

// let users change the session cookie name
if( ! defined( 'WP_SESSION_COOKIE' ) ) {
	define( 'WP_SESSION_COOKIE', '_wp_session' );
}

if ( ! class_exists( 'Recursive_ArrayAccess' ) ) {
	include ASL_CLASSES_PATH . 'session/includes/class-recursive-arrayaccess.php';
}

// Include utilities class
if ( ! class_exists( 'WP_Session_Utils' ) ) {
	include ASL_CLASSES_PATH . 'session/includes/class-wp-session-utils.php';
}

// Include WP_CLI routines early
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	include ASL_CLASSES_PATH . 'session/includes/wp-cli.php';
}

// Only include the functionality if it's not pre-defined.
if ( ! class_exists( 'WP_Session' ) ) {
	include ASL_CLASSES_PATH . 'session/includes/class-wp-session.php';
	include ASL_CLASSES_PATH . 'session/includes/wp-session.php';
}
