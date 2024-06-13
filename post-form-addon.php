<?php
/**
 * Plugin Name: Elementor Post Form Addon
 * Description: Simple post form widgets for Elementor.
 * Version:     1.0.0
 * Author:      Elementor Developer
 * Author URI:  https://developers.elementor.com/
 * Text Domain: elementor-addon
 *
 * Requires Plugins: elementor
 * Elementor tested up to: 3.21.0
 * Elementor Pro tested up to: 3.21.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Post_Form_Addon {
	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var \Post_Form_Addon The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Styles/Scripts Version
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const STYLES_SCRIPTS_VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the addon.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.21.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the addon.
	 */
	const MINIMUM_PHP_VERSION = '7.4';

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		if ( $this->is_compatible() ) {
			// Init Plugin
			add_action( 'plugins_loaded', [ $this, 'init' ], -11 );
		}
	}

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return \Post_Form_Addon An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}


	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the addon requirement.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_compatible() {
		// Check if Elementor is installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'post-form-addon' ),
			'<strong>' . esc_html__( 'Elementor Test Addon', 'post-form-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'post-form-addon' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'post-form-addon' ),
			'<strong>' . esc_html__( 'Elementor Test Addon', 'post-form-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'post-form-addon' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
		/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'post-form-addon' ),
			'<strong>' . esc_html__( 'Elementor Test Addon', 'post-form-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'post-form-addon' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Initialize
	 *
	 * Load the addons functionality only after Elementor is initialized.
	 *
	 * Fired by `elementor/init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'wp_ajax_propose_post_form', [ $this, 'submit_form_action' ] );

		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'frontend_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'frontend_scripts' ] );

	}

	/**
	 * Register widget styles
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 *  @return void
	 */
	public function frontend_styles() {
		wp_register_style( 'post-from-style', plugins_url( 'assets/build/css/app.min.css', __FILE__ ), [], self::STYLES_SCRIPTS_VERSION  );
	}

	/**
	 * Register widget scripts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 *  @return void
	 */
	public function frontend_scripts() {
		wp_register_script( 'post-form-script', plugins_url( 'assets/build/js/app.min.js', __FILE__ ), [], self::STYLES_SCRIPTS_VERSION , true );
		wp_localize_script( 'post-form-script', 'settings', array(
			'url'	=> admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('ajax-nonce'),
			'msg' => esc_html__('Title and content fields are required!', 'post-form-addon')
		) );
	}

	/**
	 * Register Widgets
	 *
	 * Load widgets files and register new Elementor widgets.
	 *
	 * Fired by `elementor/widgets/register` action hook.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {

		require_once( __DIR__ . '/widgets/post-form-widget.php' );
		$widgets_manager->register( new \Elementor_Post_Form_Widget() );

	}

	/**
	 * Submit form action
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function submit_form_action() {

		require_once( __DIR__ . '/classes/post-form.php' );
		Post_Form::submit_form();

	}

}

\Post_Form_Addon::instance();
