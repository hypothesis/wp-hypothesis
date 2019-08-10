<?php
/*
 * Plugin Name: Hypothesis
 * Plugin URI: http://hypothes.is/
 * Description: Hypothesis is an open platform for the collaborative evaluation of knowledge. This plugin embeds the necessary scripts in your Wordpress site to enable any user to use Hypothesis without installing any extensions.
 * Author: The Hypothesis Project and contributors
 * Version: 0.6.0
 * Author URI: http://hypothes.is/
 * Text Domain:     hypothesis
 * Domain Path:     /languages
 */

// Exit if called directly.
defined( 'ABSPATH' ) || die( 'Cannot access pages directly.' );

// Load textdomain
function hypothesis_load_plugin_textdomain() {
	load_plugin_textdomain( 'hypothesis', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'hypothesis_load_plugin_textdomain' );

define( 'HYPOTHESIS_PLUGIN_VERSION', '0.6.0' );

require_once __DIR__ . '/class-hypothesissettingspage.php';

if ( is_admin() ) {
	$hypothesis_settings_page = new HypothesisSettingsPage();
}

/**
 * Add Hypothesis based on conditions set in the plugin settings.
 */
add_action( 'wp', 'add_hypothesis' );

/**
 * Wrapper for the primary Hypothesis wp_enqueue call.
 */
function enqueue_hypothesis() {
	wp_enqueue_script( 'hypothesis', 'https://hypothes.is/embed.js', array(), HYPOTHESIS_PLUGIN_VERSION, true );
}

/**
 * Add Hypothesis script(s) to front end.
 */
function add_hypothesis() {
	$options   = get_option( 'wp_hypothesis_options' );
	$posttypes = HypothesisSettingsPage::get_posttypes();

		// Set defaults if we $options is not set yet.
	if ( empty( $options ) ) :
		$defaults = array(
			'highlights-on-by-default' => 1,
		);
		add_option( 'wp_hypothesis_options', $defaults );
	endif;

	// Otherwise highlighting is on by default.
	wp_enqueue_script( 'nohighlights', plugins_url( 'js/nohighlights.js', __FILE__ ), array(), HYPOTHESIS_PLUGIN_VERSION, true );

	// Embed options.
	if ( isset( $options['highlights-on-by-default'] ) ) :
		wp_enqueue_script( 'showhighlights', plugins_url( 'js/showhighlights.js', __FILE__ ), array(), HYPOTHESIS_PLUGIN_VERSION, true );
	endif;

	if ( isset( $options['sidebar-open-by-default'] ) ) :
		wp_enqueue_script( 'sidebaropen', plugins_url( 'js/sidebaropen.js', __FILE__ ), array(), HYPOTHESIS_PLUGIN_VERSION, true );
	endif;

	if ( isset( $options['serve-pdfs-with-via'] ) ) :
		wp_enqueue_script( 'pdfs-with-via', plugins_url( 'js/via-pdf.js', __FILE__ ), array(), HYPOTHESIS_PLUGIN_VERSION, true );

		$uploads = wp_upload_dir();
		wp_localize_script(
			'pdfs-with-via',
			'HypothesisPDF',
			array(
				'uploadsBase' => trailingslashit( $uploads['baseurl'] ),
			)
		);
	endif;

	// Content settings.
	$enqueue = false;

	if ( is_front_page() && isset( $options['allow-on-front-page'] ) ) {
		enqueue_hypothesis();
	} elseif ( is_home() && isset( $options['allow-on-blog-page'] ) ) {
		enqueue_hypothesis();
	}

	foreach ( $posttypes as $slug => $name ) {
		if ( 'page' !== $slug ) {
			$posttype = $slug;
			if ( 'post' === $slug ) {
				$slug = 'posts'; // Backwards compatibility.
			}
			if ( isset( $options[ "allow-on-$slug" ] ) && is_singular( $posttype ) ) { // Check if Hypothesis is allowed on this post type.
				if ( isset( $options[ $posttype . '_ids_override' ] ) && ! is_single( $options[ $posttype . '_ids_override' ] ) ) { // Make sure this post isn't in the override list if it exists.
					enqueue_hypothesis();
				} elseif ( ! isset( $options[ $posttype . '_ids_override' ] ) ) {
					enqueue_hypothesis();
				}
			} elseif ( ! isset( $options[ "allow-on-$slug" ] ) && isset( $options[ $posttype . '_ids_show_h' ] ) && is_single( $options[ $posttype . '_ids_show_h' ] ) ) { // Check if Hypothesis is allowed on this specific post.
				enqueue_hypothesis();
			}
		} elseif ( 'page' === $slug ) {
			if ( isset( $options['allow-on-pages'] ) && is_page() && ! is_front_page() && ! is_home() ) { // Check if Hypothesis is allowed on pages (and that we aren't on a special page).
				if ( isset( $options['page_ids_override'] ) && ! is_page( $options['page_ids_override'] ) ) { // Make sure this page isn't in the override list if it exists.
					enqueue_hypothesis();
				} elseif ( ! isset( $options['page_ids_override'] ) ) {
					enqueue_hypothesis();
				}
			} elseif ( ! isset( $options['allow-on-pages'] ) && isset( $options['page_ids_show_h'] ) && is_page( $options['page_ids_show_h'] ) ) { // Check if Hypothesis is allowed on this specific page.
				enqueue_hypothesis();
			}
		}
	}
}
