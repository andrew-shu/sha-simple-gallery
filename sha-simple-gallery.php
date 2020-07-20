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

if ( !class_exists( 'SHA_Simple_Gallery', false ) ) {

  $settings = [
    'plugin_name' => basename( dirname( __FILE__ , 1 ) ),
    'plugin_file' => __FILE__,
    'plugin_slug' => 'sgal',
  ];

	include_once dirname( __FILE__ ) . '/includes/class-sha_simple_gallery.php';
	$sha_bwcu_class = new SHA_Simple_Gallery();
	$sha_bwcu_class->init( $settings );
}
