<?php
/**
 * @package Hypothesis
 * @version 0.1
 */
/*
Plugin Name: Hypothesis
Plugin URI: http://hypothes.is/
Description: Hypothesis is an open platform for the collaborative evaluation of knowledge. This plugin embeds the necessary scripts in your Wordpress site to enable any user to use Hypothesis without installing any extensions.
Author: Tim Owens
Version: 0.1
Author URI: http://timmmmyboy.com/
*/

// Exit if called directly
defined( 'ABSPATH' ) or die ( 'Cannot access pages directly.' );

/**
 * Add Hypothesis over https
 */
function add_hypothesis() {
	if ( !is_admin() ) 
        wp_enqueue_script( 'hypothesis', 'https://hypothes.is/app/embed.js', '', false, true );
}

add_action( 'init', 'add_hypothesis' );


?>