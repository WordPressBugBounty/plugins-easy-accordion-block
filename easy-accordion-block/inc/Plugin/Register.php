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
            // check block theme 
			if( ! wp_is_block_theme() ) {
				add_filter( 'should_load_separate_core_block_assets', '__return_true' );
			}
        }

        /**
         * Register Block 
         * 
         * @return void
         */
        public function register_block() {
            $blocks = [
                [
                    'name' => 'accordion',
                    'is_pro' => false
                ],
                [
                    'name' => 'unfold',
                    'is_pro' => true
                ],
                [
                    'name' => 'faqs',
                    'is_pro' => true
                ]
            ];

            if ( ! empty( $blocks ) && is_array( $blocks ) ) {
                foreach ( $blocks as $block ) {
                    $block_name = $block['name'];
            
                    // Determine the correct path based on pro status
                    if ( $block['is_pro'] ) {
                        if ( ! class_exists( 'Esabp_Accordion_Block_Pro' ) || ! defined( 'ESABP_PATH' ) ) {
                            continue; // Skip if pro class or constant doesn't exist
                        }
                        $block_path = trailingslashit( ESABP_PATH ) . 'build/blocks/' . $block_name;
                    } else {
                        if ( ! defined( 'ESAB_PATH' ) ) {
                            continue; // Skip if constant doesn't exist
                        }
                        $block_path = trailingslashit( ESAB_PATH ) . 'build/blocks/' . $block_name;
                    }

                    // Check if block.json exists before registering
                    if ( file_exists( $block_path . '/block.json' ) ) {
                        register_block_type( $block_path );
                    }
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