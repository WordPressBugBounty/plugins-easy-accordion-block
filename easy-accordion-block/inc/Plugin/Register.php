<?php 
namespace ESAB\Plugin;
use ESAB\Trait\Instance;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Register' ) ) {

    class Register {

        use Instance;

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

    }

}