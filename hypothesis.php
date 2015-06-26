<?php
/**
 * @package Hypothesis
 * @version 0.3.0-dev
 */
/*
Plugin Name: Hypothesis
Plugin URI: http://hypothes.is/
Description: Hypothesis is an open platform for the collaborative evaluation of knowledge. This plugin embeds the necessary scripts in your Wordpress site to enable any user to use Hypothesis without installing any extensions.
Author: The Hypothesis Project and contributors
Version: 0.3.0-dev
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
		?>
		<div class="wrap">
			<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'my_option_group' );
					do_settings_sections( 'hypothesis-setting-admin' );
					submit_button();
				?>
			</form>
		</div>
		<?php
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

		add_settings_section(
			'setting_section_id', // ID
			'Hypothesis Settings', // Title
			array( $this, 'print_section_info' ), // Callback
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
			'post_ids_override', // ID
			'Disallow on posts (list of comma seperated post ids, no spaces)', // Title
			array( $this, 'post_ids_override_callback' ), // Callback
			'hypothesis-setting-admin', // Page
			'setting_section_id' // Section
		);

		add_settings_field(
			'page_ids_override', // ID
			'Disallow on pages (list of comma seperated page ids, no spaces)', // Title
			array( $this, 'page_ids_override_callback' ), // Callback
			'hypothesis-setting-admin', // Page
			'setting_section_id' // Section
		);

		add_settings_field(
			'category_ids_override', // ID
			'Disallow on categories (list of comma category ids, no spaces)', // Title
			array( $this, 'category_ids_override_callback' ), // Callback
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
		if( isset( $input['allow-on-blog-page'] ) )
			$new_input['allow-on-blog-page'] = absint($input['allow-on-blog-page']);

		if( isset( $input['allow-on-front-page'] ) )
			$new_input['allow-on-front-page'] = absint($input['allow-on-front-page']);
		
		if( isset( $input['allow-on-posts'] ) )
			$new_input['allow-on-posts'] = absint($input['allow-on-posts']);

		if( isset( $input['allow-on-pages'] ) )
			$new_input['allow-on-pages'] = absint($input['allow-on-pages']);

		if( isset( $input['post_ids_override'] ) )
			$new_input['post_ids_override'] = explode(',', esc_attr($input['post_ids_override']));

		if( isset( $input['page_ids_override'] ) )
			$new_input['page_ids_override'] = explode(',', esc_attr($input['page_ids_override']));

		if( isset( $input['category_ids_override'] ) )
			$new_input['category_ids_override'] = explode(',', esc_attr($input['category_ids_override']));

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info()
	{
		print 'Customize which pages Hypothesis is loaded on below:';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function allow_on_blog_page_callback()
	{
		printf(
			'<input type="checkbox" id="allow-on-blog-page" name="wp_hypothesis_options[allow-on-blog-page]" value="1" '.checked( isset($this->options["allow-on-blog-page"]) ? $this->options["allow-on-blog-page"]: null, 1, false ).'/>',
			isset( $this->options['allow-on-blog-page'] ) ? esc_attr( $this->options['allow-on-blog-page']) : 0
		);
	}

	/**
	* Get the settings option array and print one of its values
	*/
	public function allow_on_front_page_callback()
	{
		printf(
			'<input type="checkbox" id="allow-on-front-page" name="wp_hypothesis_options[allow-on-front-page]" value="1" 
			'.checked( isset($this->options["allow-on-front-page"]) ? $this->options["allow-on-front-page"]: null, 1, false ).' />',
			isset( $this->options['allow-on-front-page'] ) ? esc_attr( $this->options['allow-on-front-page']) : 0
		);
	}

	/**
	* Get the settings option array and print one of its values
	*/
	public function allow_on_posts_callback()
	{
		printf(
			'<input type="checkbox" id="allow-on-posts" name="wp_hypothesis_options[allow-on-posts]" value="1" 
			'.checked( isset($this->options["allow-on-posts"]) ? $this->options["allow-on-posts"]: null, 1, false ).' />',
			isset( $this->options['allow-on-posts'] ) ? esc_attr( $this->options['allow-on-posts']) : 0
		);
	}

	/**
	* Get the settings option array and print one of its values
	*/
	public function allow_on_pages_callback()
	{
		printf(
			'<input type="checkbox" id="allow-on-pages" name="wp_hypothesis_options[allow-on-pages]" value="1" 
			'.checked( isset($this->options["allow-on-pages"]) ? $this->options["allow-on-pages"]: null, 1, false ).' />',
			isset( $this->options['allow-on-pages'] ) ? esc_attr( $this->options['allow-on-pages']) : 0
		);
	}

	/**
	* Get the settings option array and print one of its values
	*/
	public function post_ids_override_callback()
	{
		printf(
			'<input type="text" id="post_ids_override" name="wp_hypothesis_options[post_ids_override]" value="%s" />',
			isset( $this->options['post_ids_override'] ) ? esc_attr( implode(',', $this->options['post_ids_override'])) : ''
		);
	}

	/**
	* Get the settings option array and print one of its values
	*/
	public function page_ids_override_callback()
	{
		printf(
			'<input type="text" id="page_ids_override" name="wp_hypothesis_options[page_ids_override]" value="%s" />',
			isset( $this->options['page_ids_override'] ) ? esc_attr( implode(',',$this->options['page_ids_override'])) : ''
		);
	}

	/**
	* Get the settings option array and print one of its values
	*/
	public function category_ids_override_callback()
	{
		printf(
			'<input type="text" id="category_ids_override" name="wp_hypothesis_options[category_ids_override]" value="%s" />',
			isset( $this->options['category_ids_override'] ) ? esc_attr( implode(',',$this->options['category_ids_override'])) : ''
		);
	}
}

if( is_admin() )
	$hypothesis_settings_page = new HypothesisSettingsPage();


/**
 * Add Hypothesis over https based on conditions set in the plugin settings.
 */
add_action('wp', 'add_hypothesis');

function add_hypothesis($param) {
	$options = get_option( 'wp_hypothesis_options' );
	if (isset($options['allow-on-blog-page']) && is_home()):
		wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );
	elseif (isset($options['allow-on-front-page']) && is_front_page()):
		wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );
	elseif (isset($options['allow-on-posts']) && is_single()):
		if (isset($options['post_ids_override']) && is_single($options['post_ids_override']));
		elseif (isset($options['category_ids_override']) && in_category($options['category_ids_override']));
		else
			wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );
	elseif (isset($options['allow-on-pages']) && is_page() && !is_front_page() && !is_home()):
	if (isset($options['page_ids_override']) && is_page($options['page_ids_override']));
		else
			wp_enqueue_script( 'hypothesis', '//hypothes.is/embed.js', '', false, true );
	endif;
}

?>
