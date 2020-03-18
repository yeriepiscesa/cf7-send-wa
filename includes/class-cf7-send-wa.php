<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://solusipress.com
 * @since      0.3.0
 *
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.3.0
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/includes
 * @author     Yerie Piscesa <yerie@solusipress.com>
 */
class Cf7_Send_Wa {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.3.0
	 * @access   protected
	 * @var      Cf7_Send_Wa_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.3.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.3.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.3.0
	 */
	public function __construct() {
		if ( defined( 'CF7_SEND_WA_VERSION' ) ) {
			$this->version = CF7_SEND_WA_VERSION;
		} else {
			$this->version = '0.3.0';
		}
		$this->plugin_name = 'cf7-send-wa';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cf7_Send_Wa_Loader. Orchestrates the hooks of the plugin.
	 * - Cf7_Send_Wa_i18n. Defines internationalization functionality.
	 * - Cf7_Send_Wa_Admin. Defines all hooks for the admin area.
	 * - Cf7_Send_Wa_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.3.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-send-wa-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-send-wa-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cf7-send-wa-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cf7-send-wa-public.php';

		$this->loader = new Cf7_Send_Wa_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cf7_Send_Wa_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.3.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cf7_Send_Wa_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.3.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cf7_Send_Wa_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'create_menu' );
        $this->loader->add_filter( 'wpcf7_contact_form_shortcode', $plugin_admin, 'cf7_extended_shortcode', 10, 3 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.3.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cf7_Send_Wa_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        
        $this->loader->add_filter( 'wpcf7_skip_mail', $plugin_public, 'check_skip_mail', 10, 2 );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'render_script_footer', 99 );
        
        $this->loader->add_action( 'wp_ajax_send_twilio', $plugin_public, 'send_twilio' );
        $this->loader->add_action( 'wp_ajax_nopriv_send_twilio', $plugin_public, 'send_twilio' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.3.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.3.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.3.0
	 * @return    Cf7_Send_Wa_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.3.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
