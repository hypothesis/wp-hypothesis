<?php

namespace Hypothesis;

/**
 * Create settings page (see https://codex.wordpress.org/Creating_Options_Pages)
 */
class HypothesisSettingsPage {
	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Holds the posttypes to be used in the fields callbacks
	 *
	 * @var array
	 */
	private $posttypes;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		add_options_page(
			__( 'Hypothesis Settings', 'hypothesis' ),
			__( 'Hypothesis', 'hypothesis' ),
			'manage_options',
			'hypothesis-setting-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Return an array of post type slugs and corresponding plural display names for options page.
	 *
	 * @returns array
	 */
	public static function get_posttypes() {
		return apply_filters(
			'hypothesis_supported_posttypes',
			array(
				'post' => _x( 'posts', 'plural post type', 'hypothesis' ),
				'page' => _x( 'pages', 'plural post type', 'hypothesis' ),
			)
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property.
		$this->options = get_option( 'wp_hypothesis_options' ); ?>
		<div class="wrap">
		<form method="post" action="options.php">
		<?php
				settings_fields( 'hypothesis_option_group' );
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
	public function page_init() {
		$posttypes = $this->get_posttypes();

		register_setting(
			'hypothesis_option_group', // Option group.
			'wp_hypothesis_options', // Option name.
			array( $this, 'sanitize' ) // Sanitize callback.
		);

		/**
		 * Hypothesis Settings
		 */
		add_settings_section(
			'hypothesis_settings_section', // ID.
			__( 'Hypothesis Settings', 'hypothesis' ), // Title.
			array( $this, 'settings_section_info' ), // Callback.
			'hypothesis-setting-admin' // Page.
		);

		add_settings_field(
			'highlights-on-by-default',
			__( 'Highlights on by default', 'hypothesis' ),
			array( $this, 'highlights_on_by_default_callback' ),
			'hypothesis-setting-admin',
			'hypothesis_settings_section'
		);

		add_settings_field(
			'sidebar-open-by-default',
			__( 'Sidebar open by default', 'hypothesis' ),
			array( $this, 'sidebar_open_by_default_callback' ),
			'hypothesis-setting-admin',
			'hypothesis_settings_section'
		);

		add_settings_field(
			'serve-pdfs-with-via',
			__( 'Enable annotation for PDFs in Media Library', 'hypothesis' ),
			array( $this, 'serve_pdfs_with_via_default_callback' ),
			'hypothesis-setting-admin',
			'hypothesis_settings_section'
		);

		/**
		 * Content Settings
		 * Control which pages / posts / custom post types Hypothesis is loaded on.
		 */
		add_settings_section(
			'hypothesis_content_section', // ID.
			__( 'Content Settings', 'hypothesis' ), // Title.
			array( $this, 'content_section_info' ), // Callback.
			'hypothesis-setting-admin' // Page.
		);

		add_settings_field(
			'allow-on-front-page',
			__( 'Allow on front page', 'hypothesis' ),
			array( $this, 'allow_on_front_page_callback' ),
			'hypothesis-setting-admin',
			'hypothesis_content_section'
		);

		add_settings_field(
			'allow-on-blog-page',
			__( 'Allow on blog page', 'hypothesis' ),
			array( $this, 'allow_on_blog_page_callback' ),
			'hypothesis-setting-admin',
			'hypothesis_content_section'
		);

		foreach ( $posttypes as $slug => $name ) {
			if ( 'post' === $slug ) {
				$slug = 'posts';
			} elseif ( 'page' === $slug ) {
				$slug = 'pages';
			}

			add_settings_field(
				"allow-on-$slug",
				/* Translators: name of post type */
				sprintf( __( 'Allow on %s', 'hypothesis' ), $name ),
				array( $this, 'allow_on_posttype_callback' ),
				'hypothesis-setting-admin',
				'hypothesis_content_section',
				array(
					$slug,
					$name,
				)
			);
		}

		foreach ( $posttypes as $slug => $name ) {
			add_settings_field(
				$slug . '_ids_show_h', // ID.
				sprintf(
					/* Translators: plural name of post type */
					__( 'Allow on specific %1$s (list of comma-separated %1$s IDs, no spaces)', 'hypothesis' ),
					$name,
					$slug
				), // Title.
				array( $this, 'posttype_ids_show_h_callback' ), // Callback.
				'hypothesis-setting-admin', // Page.
				'hypothesis_content_section', // Section.
				array(
					$slug,
					$name,
				)
			);
		}

		foreach ( $posttypes as $slug => $name ) {
			add_settings_field(
				$slug . '_ids_override', // ID.
				sprintf(
					/* Translators: plural name of post type */
					__( 'Disallow on specific %1$s (list of comma-separated %1$s IDs, no spaces)', 'hypothesis' ),
					$name,
					$slug
				), // Title.
				array( $this, 'posttype_ids_override_callback' ), // Callback.
				'hypothesis-setting-admin', // Page.
				'hypothesis_content_section', // Section.
				array(
					$slug,
					$name,
				)
			);
		}
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys.
	 */
	public function sanitize( $input ) {
		$posttypes = $this->get_posttypes();
		$new_input = array();

		if ( isset( $input['highlights-on-by-default'] ) ) {
			$new_input['highlights-on-by-default'] = absint( $input['highlights-on-by-default'] );
		}

		if ( isset( $input['sidebar-open-by-default'] ) ) {
			$new_input['sidebar-open-by-default'] = absint( $input['sidebar-open-by-default'] );
		}

		if ( isset( $input['serve-pdfs-with-via'] ) ) {
			$new_input['serve-pdfs-with-via'] = absint( $input['serve-pdfs-with-via'] );
		}

		if ( isset( $input['allow-on-blog-page'] ) ) {
			$new_input['allow-on-blog-page'] = absint( $input['allow-on-blog-page'] );
		}

		if ( isset( $input['allow-on-front-page'] ) ) {
			$new_input['allow-on-front-page'] = absint( $input['allow-on-front-page'] );
		}

		foreach ( $posttypes as $slug => $name ) {
			if ( 'post' === $slug ) { // Adjust for backwards compatibility.
				$slug = 'posts';
			} elseif ( 'page' === $slug ) {
				$slug = 'pages';
			}

			if ( isset( $input[ "allow-on-$slug" ] ) ) {
				$new_input[ "allow-on-$slug" ] = absint( $input[ "allow-on-$slug" ] );
			}

			if ( 'posts' === $slug ) { // Adjust for backwards compatibility.
				$slug = 'post';
			} elseif ( 'pages' === $slug ) {
				$slug = 'page';
			}

			if ( isset( $input[ $slug . '_ids_show_h' ] ) && '' != $input[ $slug . '_ids_show_h' ] ) {
				$new_input[ $slug . '_ids_show_h' ] = explode( ',', esc_attr( $input[ $slug . '_ids_show_h' ] ) );
			}

			if ( isset( $input[ $slug . '_ids_override' ] ) && '' != $input[ $slug . '_ids_override' ] ) {
				$new_input[ $slug . '_ids_override' ] = explode( ',', esc_attr( $input[ $slug . '_ids_override' ] ) );
			}
		}

		return $new_input;
	}

	/**
	 * Print the Hypothesis Settings section text
	 */
	public function settings_section_info() {
		?>
		<p><?php esc_attr_e( 'Customize Hypothesis defaults and behavior.', 'hypothesis' ); ?></p>
		<?php
	}

	/**
	 * Print the Content Settings section text
	 */
	public function content_section_info() {
		?>
		<p><?php esc_attr_e( 'Control where Hypothesis is loaded.', 'hypothesis' ); ?></p>
		<?php
	}

	/**
	 * Callback for 'highlights-on-by-default'.
	 */
	public function highlights_on_by_default_callback() {
		$val = isset( $this->options['highlights-on-by-default'] ) ? esc_attr( $this->options['highlights-on-by-default'] ) : 0;

		printf(
			'<input type="checkbox" id="highlights-on-by-default" name="wp_hypothesis_options[highlights-on-by-default]" value="1" %s/>',
			checked( $val, 1, false )
		);
	}

	/**
	 * Callback for 'sidebar-open-by-default'.
	 */
	public function sidebar_open_by_default_callback() {
		$val = isset( $this->options['sidebar-open-by-default'] ) ? esc_attr( $this->options['sidebar-open-by-default'] ) : 0;
		printf(
			'<input type="checkbox" id="sidebar-open-by-default" name="wp_hypothesis_options[sidebar-open-by-default]" value="1" %s/>',
			checked( $val, 1, false )
		);
	}

	/**
	 * Callback for 'serve-pdfs-with-via'.
	 */
	public function serve_pdfs_with_via_default_callback() {
		$val = isset( $this->options['serve-pdfs-with-via'] ) ? esc_attr( $this->options['serve-pdfs-with-via'] ) : 0;
		printf(
			'<input type="checkbox" id="serve-pdfs-with-via" name="wp_hypothesis_options[serve-pdfs-with-via]" value="1" %s/>',
			checked( $val, 1, false )
		);
	}

	/**
	 * Callback for 'allow_on_blog_page'.
	 */
	public function allow_on_blog_page_callback() {
		$val = isset( $this->options['allow-on-blog-page'] ) ? esc_attr( $this->options['allow-on-blog-page'] ) : 0;
		printf(
			'<input type="checkbox" id="allow-on-blog-page" name="wp_hypothesis_options[allow-on-blog-page]" value="1" %s/>',
			checked( $val, 1, false )
		);
	}

	/**
	 * Callback for 'allow-on-front-page'.
	 */
	public function allow_on_front_page_callback() {
		$val = isset( $this->options['allow-on-front-page'] ) ? esc_attr( $this->options['allow-on-front-page'] ) : 0;
		printf(
			'<input type="checkbox" id="allow-on-front-page" name="wp_hypothesis_options[allow-on-front-page]" value="1" %s/>',
			checked( $val, 1, false )
		);
	}

	/**
	 * Callback for 'allow-on-<posttype>'.
	 */
	public function allow_on_posttype_callback( $args ) {
		$slug = $args[0];
		$val = isset( $this->options[ "allow-on-$slug" ] ) ? esc_attr( $this->options[ "allow-on-$slug" ] ) : 0;

		printf(
			'<input type="checkbox" id="allow-on-%s" name="wp_hypothesis_options[allow-on-%s]" value="1" %s/>',
			esc_attr( $slug ),
			esc_attr( $slug ),
			checked( $val, 1, false )
		);
	}

	/**
	 * Callback for '<posttype>_ids_show_h'.
	 *
	 * @param array $args An arry containing the post type slug and the post type name (plural).
	 */
	public function posttype_ids_show_h_callback( $args ) {
		$slug = $args[0];
		$val = isset( $this->options[ $slug . '_ids_show_h' ] ) ? esc_attr( implode( ',', $this->options[ $slug . '_ids_show_h' ] ) ) : '';

		printf(
			'<input type="text" id="%s_ids_show_h" name="wp_hypothesis_options[%s_ids_show_h]" value="%s" />',
			esc_attr( $slug ),
			esc_attr( $slug ),
			esc_attr( $val )
		);
	}

	/**
	 * Callback for '<posttype>_ids_override'.
	 *
	 * @param array $args An arry containing the post type slug and the post type name (plural).
	 */
	public function posttype_ids_override_callback( $args ) {
		$slug = $args[0];
		$val = isset( $this->options[ $slug . '_ids_override' ] ) ? esc_attr( implode( ',', $this->options[ $slug . '_ids_override' ] ) ) : '';

		printf(
			'<input type="text" id="%s_ids_override" name="wp_hypothesis_options[%s_ids_override]" value="%s" />',
			esc_attr( $slug ),
			esc_attr( $slug ),
			esc_attr( $val )
		);
	}
}
