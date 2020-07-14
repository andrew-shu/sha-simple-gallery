<?php
/*
 * Plugin Name:       Simple CPT Gallery
 * Description:       Simple gallery, based on Custom Post Types
 * Version:           0.1.0
 * Author:            Andrew Sh
 * Text Domain:       sha-sgal
 * Domain Path:       /languages
 */

if ( !defined( 'ABSPATH' ) )  {
  exit;
}

if ( !defined( 'SHA_SGAL_PLUGIN_FILE' ) ) {
	define( 'SHA_SGAL_PLUGIN_FILE', __FILE__ );
}

if ( !class_exists( 'SHA_Simple_Gallery', false ) ) {
	include_once dirname( SHA_SGAL_PLUGIN_FILE ) . '/includes/class-sha_simple_gallery.php';
	$sha_bwcu_class = new SHA_Simple_Gallery();
	$sha_bwcu_class->init();
}
