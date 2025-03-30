<?php
/**
 * Plugin Name: StayFoxhole Emails for HivePress
 * Description: Email extensions for the HivePress marketpalce.
 * Version: 1.0.0
 * Author: StayFoxhole
 * Author URI: https://stayfoxhole.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: stayfoxhole
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'FOXHOLE_EMAILS_PLUGIN_FILE' ) ) {
    define( 'FOXHOLE_EMAILS_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'FOXHOLE_EMAILS_PLUGIN' ) ) {
    define( 'FOXHOLE_EMAILS_PLUGIN', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'FOXHOLE_EMAILS_PLUGIN_URL' ) ) {
    define( 'FOXHOLE_EMAILS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'FOXHOLE_EMAILS_ASSETS_VERSION' ) ) {
    define( 'FOXHOLE_EMAILS_ASSETS_VERSION', '1.0.0' );
}

require_once 'vendor/autoload.php';

add_filter(
	'hivepress/v1/extensions',
	function( $extensions ) {
		$extensions[] = __DIR__;

		return $extensions;
	}
);

class SfxhEmails {}

$sfxhEmails = new SfxhEmails();