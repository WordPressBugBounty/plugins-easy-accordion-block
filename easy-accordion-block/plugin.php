<?php
/**
 * Plugin Name:       Easy Accordion Gutenberg Block
 * Description:       A custom Gutenberg Block developed with Gutenberg Native Components.
 * Requires at least: 6.0
 * Requires PHP:      7.0
 * Version:           1.2.4
 * Author:            Zakaria Binsaifullah
 * Author URI:        https://makegutenblock.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       easy-accordion-block
 */

 /**
  * @package Zero Configuration with @wordpress/create-block
  *  [esab] && [ESAB] ===> Prefix
  */

// Stop Direct Access 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// require admin page
require_once plugin_dir_path( __FILE__ ) . 'admin/class-esab-blocks.php';

/**
 * Blocks Final Class
 */

final class ESAB_BLOCKS_CLASS {
	public function __construct() {

		// define constants
		$this->esab_define_constants();

		// block initialization
		add_action( 'init', [ $this, 'esab_blocks_init' ] );

		// blocks category
		if( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
			add_filter( 'block_categories', [ $this, 'esab_register_block_category' ], 10, 2 );
		} else {
			add_filter( 'block_categories_all', [ $this, 'esab_register_block_category' ], 10, 2 );
		}

		// enqueue block assets
		add_action( 'enqueue_block_assets', [ $this, 'esab_external_libraries' ] );

		// redirect to admin page after activation
		add_action( 'activated_plugin', [ $this, 'esab_redirect_to_admin_page' ] );
	}

	/**
	 * Redirect to admin page after activation
	 */
	public function esab_redirect_to_admin_page( $plugin ) {
		if( $plugin == plugin_basename( __FILE__ ) ) {
			exit( wp_redirect( admin_url( 'options-general.php?page=esab-accordion' ) ) );
		}
	}

	/**
	 * Initialize the plugin
	 */

	public static function init(){
		static $instance = false; 
		if( ! $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * Define the plugin constants
	 */
	private function esab_define_constants() {
		define( 'ESAB_VERSION', '1.2.4' );
		define( 'ESAB_URL', plugin_dir_url( __FILE__ ) );
		define( 'ESAB_LIB_URL', ESAB_URL . 'includes/' );		
	}

	/**
	 * Blocks Registration 
	 */

	public function esab_register_block( $name, $options = array() ) {
		register_block_type( __DIR__ . '/build/blocks/' . $name, $options );
	 }

	 /**
	  * Render Inline CSS
	  */
	public function esab_render_inline_css( $handle, $css ) {
		// register inline style
		wp_register_style( $handle, false );
		// enqueue inline style
		wp_enqueue_style( $handle );

		wp_add_inline_style( $handle, $css );
	}

	/**
	 * Blocks Initialization
	*/
	public function esab_blocks_init() {
		// register single block
		$this->esab_register_block( 'accordion', array(
			'render_callback' => [ $this, 'esab_render_accordion_block' ],
		));
	}

	/**
	 * Render Callback function
	 */
	public function esab_render_accordion_block( $attributes, $content ) {
		$handle = $attributes['uniqueId'];
		$custom_css = '';
		if(!empty($attributes['zindex'])){
			$custom_css .= '.'.$attributes['uniqueId'].' { z-index: '.$attributes['zindex'].'; }';
		}
		// single accordion
		if(!empty($attributes['accordionBorderRadius'])){
			$custom_css .= '.'.$attributes['uniqueId'].' .wp-block-esab-accordion-child { border-radius: '.$attributes['accordionBorderRadius'].'px; }';
		}

		if(!empty($attributes['accordionActiveBorderColor'])){
			$custom_css .= '.'.$attributes['uniqueId'].' .wp-block-esab-accordion-child.esab__active_accordion {
				border-color: '.$attributes['accordionActiveBorderColor'].' !important;
			}';
		}

		// HEADER
		if(!empty($attributes['headerActiveBg'])){
			$custom_css .= '.'.$attributes['uniqueId'].' .wp-block-esab-accordion-child.esab__active_accordion .esab__head {
				background: '.$attributes['headerActiveBg'].' !important;
			}';
		}
		
		if(!empty($attributes['headingActiveColor'])){
			$custom_css .= '.'.$attributes['uniqueId'].' .wp-block-esab-accordion-child.esab__active_accordion .esab__heading_tag{
				color: '.$attributes['headingActiveColor'].' !important;
			}';
		}

		// Body
		if(!empty($attributes['activeSeparatorColor'])){
			$custom_css .= '.'.$attributes['uniqueId'].' .wp-block-esab-accordion-child.esab__active_accordion .esab__body{
				border-color: '.$attributes['activeSeparatorColor'].' !important;
			}';
		}
		if(!empty($attributes['bodyActiveBg'])){
			$custom_css .= '.'.$attributes['uniqueId'].' .wp-block-esab-accordion-child.esab__active_accordion .esab__body{
				background-color: '.$attributes['bodyActiveBg'].' !important;
			}';
		}

		//icon
		$inactiveIconColor = !empty($attributes['inactiveIconColor']) ? $attributes['inactiveIconColor'] : 'inherit';
		$activeIconColor = !empty($attributes['activeIconColor']) ? $attributes['activeIconColor'] : 'inherit';

		$custom_css .= '.'.$attributes['uniqueId'].' .esab__collapse svg { width: '.$attributes['iconSize'].'px; fill: '.$inactiveIconColor.'; }';
		$custom_css .= '.'.$attributes['uniqueId'].' .esab__expand svg { width: '.$attributes['iconSize'].'px; fill: '.$activeIconColor.'; }';

		$this->esab_render_inline_css( $handle, $custom_css );
		return $content;
	}

	/**
	 * Register Block Category
	 */

	public function esab_register_block_category( $categories, $post ) {
		return array_merge(
			array(
				array(
					'slug'  => 'esab-blocks',
					'title' => __( 'Easy Accordion', 'easy-accordion-block' ),
				),
			),
			$categories
		);
	}
	
	/**
	 * Enqueue Block Assets
	 */
	public function esab_external_libraries() {

		// Editor CSS
		if( is_admin() ) {
			wp_enqueue_style( 'esab-editor-css', ESAB_LIB_URL . 'css/editor.css', array(), ESAB_VERSION );
		}

		// enqueue JS
		if( has_block('esab/accordion') ) {
			wp_enqueue_script( 'esab-accordion-js', ESAB_LIB_URL . 'js/accordion.js', array( 'jquery' ), ESAB_VERSION, true );
		}
	}

}

/**
 * Kickoff
*/
ESAB_BLOCKS_CLASS::init();

/**
* SDK Integration
*/

if ( ! function_exists( 'esab_plugin_easy_accordion_block' ) ) {
	function esab_plugin_easy_accordion_block() {

		// Include DCI SDK.
		require_once dirname( __FILE__ ) . '/admin/dci/start.php';
		wp_register_style('dci-sdk-easy_accordion_block', plugins_url('admin/dci/assets/css/dci.css', __FILE__), array(), '1.2.1', 'all');
        wp_enqueue_style('dci-sdk-easy_accordion_block');


		dci_dynamic_init( array(
			'sdk_version'          => '1.2.1',
			'product_id'           => 6,
			'plugin_name'          => 'Easy Accordion Block',                                          // make simple, must not empty
			'plugin_title'         => 'Easy Accordion Block',                                          // You can describe your plugin title here
			'api_endpoint'         => 'https://dashboard.codedivo.com/wp-json/dci/v1/data-insights',
			'slug'                 => 'easy-accordion-block',                                          // write 'no-need' if you don't want to use
			'core_file'            => false,
			'plugin_deactivate_id' => false,
			'menu'                 => array(
				'slug' => 'esab-accordion',
			),
			'public_key'          => 'pk_2zPuK3VzDOtZ2HEZuT9zBFUu8d2iQw3z',
			'is_premium'          => false,
			'popup_notice'        => false,
			'deactivate_feedback' => false,
			'delay_time'          => [ 
				'time' => 3 * DAY_IN_SECONDS,
			],
			'text_domain' => 'easy-accordion-block',
			'plugin_msg'  => '
				<p>Thank you for using <strong>Easy Accordion Block</strong>! We hope you enjoy using it. If you love <strong>Easy Accordion Block</strong>, please consider leaving us a 5-star review on <a href="https://wordpress.org/support/plugin/easy-accordion-block/reviews/#new-post" target="_blank">WordPress</a> to help us spread the word. </p>
                <We>Also, if you need any help, feel free to contact us via our <a href="https://gutenbergkits.com/contact/" target="_blank">support forum</a>. We are always here to help you. We collect some data to improve our product.</p>
			',
		) );

	}
	add_action( 'admin_init', 'esab_plugin_easy_accordion_block' );
}
