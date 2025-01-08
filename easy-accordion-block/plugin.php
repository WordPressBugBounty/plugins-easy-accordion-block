<?php
/**
 * Plugin Name:       Easy Accordion Gutenberg Block
 * Description:       A custom Gutenberg Block developed with Gutenberg Native Components.
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Version:           1.3.0
 * Author:            Zakaria Binsaifullah
 * Author URI:        https://makegutenblock.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       easy-accordion-block
 * Domain Path:       /languages
 */

// Stop Direct Access 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// require autoload file
require_once plugin_dir_path( __FILE__ ) . 'autoload-config-psr4.php';
use ESAB\Admin\Admin;
use ESAB\Plugin\Accordion;

if( ! class_exists( 'Esab_Accordion_Block' ) ) {

	final class Esab_Accordion_Block {

		/**
		 * Plugin Version
		 */
		const VERSION = '1.3.0';

		// instance
		protected static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			
			// define constants
			$this->define_constants();

			// init
			$this->init();

			// enable redirect
			register_activation_hook( __FILE__, [ $this, 'redirect_to_admin' ] );

			// handle redirect
			add_action( 'admin_init', [ $this, 'handle_redirection' ] );
		}

		/**
		 * Define Constants
		 */
		public function define_constants() {
			$constants = [
				'ESAB_VERSION' => self::VERSION,
				'ESAB_URL'     => plugin_dir_url( __FILE__ ),
				'ESAP_PATH'    => plugin_dir_path( __FILE__ ),
				'ESAB_LIB_URL' => plugin_dir_url( __FILE__ ) . 'includes/',
			];

			foreach ( $constants as $key => $value ) {
				if ( ! defined( $key ) ) {
					define( $key, $value );
				}
			}
		}

		/**
		 * Initialize the plugin
		 */
		public function init() {

			// admin
			if( class_exists( 'ESAB\Admin\Admin' ) ) {
				Admin::instance();
			}

			// accordion
			if(  class_exists( 'ESAB\Plugin\Accordion' ) ) {
				Accordion::instance();
			}
		}

		/**
		 * Redirect to admin page after activation
		 */
		public function redirect_to_admin() {
			set_transient( '_esab_redirect', true, 30 );
		}

		/**
		 * Handle Redirection
		 */
		public function handle_redirection() {
			if ( get_transient( '_esab_redirect' ) ) {
				delete_transient( '_esab_redirect' );
				if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) && ! ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
					wp_safe_redirect( admin_url( 'options-general.php?page=esab-accordion' ) );
					exit;
				}
			}
		}

		/**
		 * Instance of the class
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

	}

	/**
	 * Initialize the plugin
	 */
	function esab_accordion_block() {
		return Esab_Accordion_Block::instance();
	}

	// kick-off
	esab_accordion_block();
}