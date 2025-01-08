<?php 
namespace ESAB\Plugin;
use ESAB\Trait\Instance;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Category' ) ) {

    class Category {

        use Instance;

        public function __construct() {
            add_filter( 'block_categories_all', [ $this, 'register_category' ], 10, 2 );
        }

        /**
         * Register Block 
         * 
         * @return void
         */
        public function register_category( $categories ) {
            return array_merge(
                [
                    [
                        'slug'  => 'esab-blocks',
                        'title' => __( 'Accordion', 'easy-accordion-block' ),
                        'icon'  => null,
                    ],
                ],
                $categories
            );
        }

    }

}
