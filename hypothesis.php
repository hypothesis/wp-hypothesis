<?php
/**
 * Plugin Name: Hypothesis
 * Plugin URI: https://hypothes.is/
 * Description: Hypothesis is an open platform for the collaborative evaluation of knowledge. This plugin embeds the necessary scripts in your WordPress site to enable any user to use Hypothesis without installing any extensions.
 * Version: 0.7.3
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: The Hypothesis Project and contributors
 * Author URI: https://hypothes.is/
 * License: BSD-3-Clause
 * License URI: https://opensource.org/licenses/BSD-3-Clause
 * Text Domain: hypothesis
 * Domain Path: /languages
 **/

namespace Hypothesis;

use function add_action;
use function define;
use function defined;
use function get_option;
use function is_admin;
use function load_plugin_textdomain as wp_load_plugin_textdomain;
use function wp_enqueue_script;

// Exit if called directly.
defined( 'ABSPATH' ) || die( 'Cannot access pages directly.' );

// Load textdomain
function load_plugin_textdomain() {
	wp_load_plugin_textdomain( 'hypothesis', false, basename( __DIR__ ) . '/languages/' );
}
add_action( 'plugins_loaded', 'Hypothesis\load_plugin_textdomain' );

define( 'HYPOTHESIS_PLUGIN_VERSION', '0.7.3' );

require_once __DIR__ . '/class-hypothesissettingspage.php';

if ( is_admin() ) {
	$hypothesis_settings_page = new HypothesisSettingsPage();
}

/**
 * Add Hypothesis based on conditions set in the plugin settings.
 */
add_action( 'wp', 'Hypothesis\add_scripts' );

/**
 * Wrapper for the primary Hypothesis wp_enqueue call.
 */
function enqueue_hypothesis() {
	wp_enqueue_script( 'hypothesis', 'https://hypothes.is/embed.js', array(), HYPOTHESIS_PLUGIN_VERSION, true );
}

/**
 * Add Hypothesis script(s) to front end.
 */
function add_scripts() {
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
