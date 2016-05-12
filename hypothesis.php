<?php
/**
 * @package Hypothesis
 * @version 0.4.5
 */
/*
Plugin Name: Hypothesis
Plugin URI: http://hypothes.is/
Description: Hypothesis is an open platform for the collaborative evaluation of knowledge. This plugin embeds the necessary scripts in your Wordpress site to enable any user to use Hypothesis without installing any extensions.
Author: The Hypothesis Project and contributors
Version: 0.4.5
Author URI: http://hypothes.is/
*/

// Exit if called directly
defined( 'ABSPATH' ) or die ( 'Cannot access pages directly.' );


// From https://codex.wordpress.org/Creating_Options_Pages
class HypothesisSettingsPage
{
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page()
	{
		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			'Hypothesis',
			'manage_options',
			'hypothesis-setting-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page()
	{
		// Set class property
		$this->options = get_option( 'wp_hypothesis_options' );
		include ( 'formgen.php' );
	}

	/**
	 * Register and add settings
	 */
	public function page_init()
	{		
		register_setting(
			'my_option_group', // Option group
			'wp_hypothesis_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		/**
		 * Hypothesis Settings
		 */
		add_settings_section(
			'setting_section_id2', // ID
			'Hypothesis Settings', // Title
			array( $this, 'print_section_info' ), // Callback
			'hypothesis-setting-admin' // Page
		);

		add_settings_field(
			'highlights-on-by-default',
			'Highlights on by default',
			array( $this, 'highlights_on_by_default_callback' ),
			'hypothesis-setting-admin',
			'setting_section_id2'
		);

		add_settings_field(
			'sidebar-open-by-default',
			'Sidebar open by default',
			array( $this, 'sidebar_open_by_default_callback' ),
			'hypothesis-setting-admin',
			'setting_section_id2'
		);

		/**
		 * Content Settings
		 * Control which pages / posts Hypothesis is loaded on.
		 */
		add_settings_section(
			'setting_section_id', // ID
			'Content Settings', // Title
			array( $this, 'print_section_info2' ), // Callback
			'hypothesis-setting-admin' // Page
		);
		
		add_settings_field(
			'allow-on-front-page',
			'Allow on front page',
			array( $this, 'allow_on_front_page_callback' ),
			'hypothesis-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'allow-on-blog-page',
			'Allow on blog page',
			array( $this, 'allow_on_blog_page_callback' ),
			'hypothesis-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'allow-on-posts',
			'Allow on posts',
			array( $this, 'allow_on_posts_callback' ),
			'hypothesis-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'allow-on-pages',
			'Allow on pages',
			array( $this, 'allow_on_pages_callback' ),
			'hypothesis-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'post_ids_show_h', // ID
			'Allow on posts (list of comma-separated post ids, no spaces)', // Title
			array( $this, 'post_ids_show_h_callback' ), // Callback
			'hypothesis-setting-admin', // Page
			'setting_section_id' // Section
		);

		add_settings_field(
			'page_ids_show_h', // ID
			'Allow on pages (list of comma-separated page ids, no spaces)', // Title
			array( $this, 'page_ids_show_h_callback' ), // Callback
			'hypothesis-setting-admin', // Page
			'setting_section_id' // Section
		);

		add_settings_field(
			'post_ids_override', // ID
			'Disallow on posts (list of comma-separated post ids, no spaces)', // Title
			array( $this, 'post_ids_override_callback' ), // Callback
			'hypothesis-setting-admin', // Page
			'setting_section_id' // Section
		);

		add_settings_field(
			'page_ids_override', // ID
			'Disallow on pages (list of comma-separated page ids, no spaces)', // Title
			array( $this, 'page_ids_override_callback' ), // Callback
			'hypothesis-setting-admin', // Page
			'setting_section_id' // Section
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input )
	{
		$new_input = array();

		if( isset( $input['highlights-on-by-default'] ) )
			$new_input['highlights-on-by-default'] = absint($input['highlights-on-by-default']);
		// else $new_input['highlights-on-by-default'] = 1;

		if( isset( $input['sidebar-open-by-default'] ) )
			$new_input['sidebar-open-by-default'] = absint($input['sidebar-open-by-default']);

		if( isset( $input['allow-on-blog-page'] ) )
			$new_input['allow-on-blog-page'] = absint($input['allow-on-blog-page']);

		if( isset( $input['allow-on-front-page'] ) )
			$new_input['allow-on-front-page'] = absint($input['allow-on-front-page']);
		
		if( isset( $input['allow-on-posts'] ) )
			$new_input['allow-on-posts'] = absint($input['allow-on-posts']);

		if( isset( $input['allow-on-pages'] ) )
			$new_input['allow-on-pages'] = absint($input['allow-on-pages']);

		if( isset( $input['post_ids_show_h'] ) )
			$new_input['post_ids_show_h'] = explode(',', esc_attr($input['post_ids_show_h']));

		if( isset( $input['page_ids_show_h'] ) )
			$new_input['page_ids_show_h'] = explode(',', esc_attr($input['page_ids_show_h']));

		if( isset( $input['post_ids_override'] ) )
			$new_input['post_ids_override'] = explode(',', esc_attr($input['post_ids_override']));

		if( isset( $input['page_ids_override'] ) )
			$new_input['page_ids_override'] = explode(',', esc_attr($input['page_ids_override']));

		return $new_input;
	}

	/**
	 * Print the Hypothesis Settings section text
	 */
	public function print_section_info()
	{
		print 'Customize Hypothesis defaults and behavior below:';
	}

	/**
	 * Print the Content Settings section text
	 */
	public function print_section_info2()
	{
		print 'Control which pages Hypothesis is loaded on below:';
	}

	/**
	 * HYPOTHESIS SETTINGS Callbacks
	 * These get the settings option array for a setting and print one of its values.
	 * They are used to set various defaults for the Hypothesis application.
	 */
	public function highlights_on_by_default_callback()
	{
		printf(
			'<input type="checkbox" id="highlights-on-by-default" name="wp_hypothesis_options[highlights-on-by-default]" value="1" '.checked( isset($this->options["highlights-on-by-default"]) ? $this->options["highlights-on-by-default"]: null, 1, false ).'/>',
			isset( $this->options['highlights-on-by-default'] ) ? esc_attr( $this->options['highlights-on-by-default']) : 0
		);
	}

	public function sidebar_open_by_default_callback()
	{
		printf(
			'<input type="checkbox" id="sidebar-open-by-default" name="wp_hypothesis_options[sidebar-open-by-default]" value="1" '.checked( isset($this->options["sidebar-open-by-default"]) ? $this->options["sidebar-open-by-default"]: null, 1, false ).'/>',
			isset( $this->options['sidebar-open-by-default'] ) ? esc_attr( $this->options['sidebar-open-by-default']) : 0
		);
	}


	/**
	 * CONTENT SETTINGS Callbacks
	 * These get the settings option array for a setting and print one of its values.
	 * They are used to determine what pages Hypothesis is loaded on.
	 */
	public function allow_on_blog_page_callback()
	{
		printf(
			'<input type="checkbox" id="allow-on-blog-page" name="wp_hypothesis_options[allow-on-blog-page]" value="1" '.checked( isset($this->options["allow-on-blog-page"]) ? $this->options["allow-on-blog-page"]: null, 1, false ).'/>',
			isset( $this->options['allow-on-blog-page'] ) ? esc_attr( $this->options['allow-on-blog-page']) : 0
		);
	}

	public function allow_on_front_page_callback()
	{
		printf(
			'<input type="checkbox" id="allow-on-front-page" name="wp_hypothesis_options[allow-on-front-page]" value="1" 
			'.checked( isset($this->options["allow-on-front-page"]) ? $this->options["allow-on-front-page"]: null, 1, false ).' />',
			isset( $this->options['allow-on-front-page'] ) ? esc_attr( $this->options['allow-on-front-page']) : 0
		);
	}

	public function allow_on_posts_callback()
	{
		printf(
			'<input type="checkbox" id="allow-on-posts" name="wp_hypothesis_options[allow-on-posts]" value="1" 
			'.checked( isset($this->options["allow-on-posts"]) ? $this->options["allow-on-posts"]: null, 1, false ).' />',
			isset( $this->options['allow-on-posts'] ) ? esc_attr( $this->options['allow-on-posts']) : 0
		);
	}

	public function allow_on_pages_callback()
	{
		printf(
			'<input type="checkbox" id="allow-on-pages" name="wp_hypothesis_options[allow-on-pages]" value="1" 
			'.checked( isset($this->options["allow-on-pages"]) ? $this->options["allow-on-pages"]: null, 1, false ).' />',
			isset( $this->options['allow-on-pages'] ) ? esc_attr( $this->options['allow-on-pages']) : 0
		);
	}

	public function page_ids_show_h_callback()
	{
		printf(
			'<input type="text" id="page_ids_show_h" name="wp_hypothesis_options[page_ids_show_h]" value="%s" />',
			isset( $this->options['page_ids_show_h'] ) ? esc_attr( implode(',', $this->options['page_ids_show_h'])) : ''
		);
	}

	public function post_ids_show_h_callback()
	{
		printf(
			'<input type="text" id="post_ids_show_h" name="wp_hypothesis_options[post_ids_show_h]" value="%s" />',
			isset( $this->options['post_ids_show_h'] ) ? esc_attr( implode(',', $this->options['post_ids_show_h'])) : ''
		);
	}

	public function post_ids_override_callback()
	{
		printf(
			'<input type="text" id="post_ids_override" name="wp_hypothesis_options[post_ids_override]" value="%s" />',
			isset( $this->options['post_ids_override'] ) ? esc_attr( implode(',', $this->options['post_ids_override'])) : ''
		);
	}

	public function page_ids_override_callback()
	{
		printf(
			'<input type="text" id="page_ids_override" name="wp_hypothesis_options[page_ids_override]" value="%s" />',
			isset( $this->options['page_ids_override'] ) ? esc_attr( implode(',',$this->options['page_ids_override'])) : ''
		);
	}
}

if( is_admin() )
	$hypothesis_settings_page = new HypothesisSettingsPage();


/**
 * Add Hypothesis based on conditions set in the plugin settings.
 */
add_action('wp', 'add_hypothesis');

function add_hypothesis($param) {
	$options = get_option( 'wp_hypothesis_options' );

	// Set defaults if we $options is not set yet.
	if (empty($options)):
		$defaults = array(
		 'highlights-on-by-default' => 1,
		);
		add_option( 'wp_hypothesis_options', $defaults );
	endif;

	// Embed options
	if (isset($options['highlights-on-by-default'])):
		wp_enqueue_script( 'showhighlights', '/wp-content/plugins/hypothesis/js/showhighlights.js', '', false, true );
	endif;

	if (isset($options['sidebar-open-by-default'])):
		wp_enqueue_script( 'sidebaropen', '/wp-content/plugins/hypothesis/js/sidebaropen.js', '', false, true );
	endif;

	// Content settings
	if (isset($options['allow-on-blog-page']) && is_home()):
		wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );

	elseif (isset($options['allow-on-front-page']) && is_front_page()):
		wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );

	elseif (isset($options['post_ids_show_h']) && is_single($options['post_ids_show_h'])):
		wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );

	elseif (isset($options['page_ids_show_h']) && is_page($options['page_ids_show_h'])):
		wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );

	elseif (isset($options['allow-on-posts']) && is_single()):
		if (isset($options['post_ids_override']) && is_single($options['post_ids_override']));
		else
			wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );

	elseif (isset($options['allow-on-pages']) && is_page() && !is_front_page() && !is_home()):
		if (isset($options['page_ids_override']) && is_page($options['page_ids_override']));
		else
			wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );
	endif;
}

?>
