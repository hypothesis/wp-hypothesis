<?php
/**
 * @package Hypothesis
 * @version 0.1.2
 */
/*
Plugin Name: Hypothesis
Plugin URI: http://hypothes.is/
Description: Hypothesis is an open platform for the collaborative evaluation of knowledge. This plugin embeds the necessary scripts in your Wordpress site to enable any user to use Hypothesis without installing any extensions.
Author: The Hypothesis Project and contributors
Version: 0.1.2
Author URI: http://hypothes.is/
*/

// Exit if called directly
defined( 'ABSPATH' ) or die ( 'Cannot access pages directly.' );

/**
 * Add Hypothesis over https
 */
function add_hypothesis() {
	if ( !is_admin() ) 
        wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );
}

add_action( 'init', 'add_hypothesis' );


?>
