<?php 
/**
 * Register Blocks
 * 
 * @package Easy_Accordion_Block
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Esab_Register' ) ) {

    class Esab_Register {

        /**
         * Instance of the class
         *
         * @var null
         */
        private static $instance = null;


        /**
         * Constructor
         */
        public function __construct() {
            add_action( 'init', [ $this, 'register_block' ] );

            // var_dump( 'Register Block' );
        }

        /**
         * Register Block 
         * 
         * @return void
         */
        public function register_block() {
            $blocks = [
                'accordion'
            ];

            if ( ! empty( $blocks ) and is_array( $blocks ) ) {
				foreach ( $blocks as $block ) {
					register_block_type( trailingslashit( ESAP_PATH ) . '/build/blocks/' . $block );
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

    Esab_Register::instance(); // Initialize the class

}