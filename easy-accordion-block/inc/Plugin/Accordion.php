<?php
/**
 * Initialize all necessary classes and functions
 */
namespace ESAB\Plugin;
use ESAB\Trait\Instance;
use ESAB\Plugin\Category;
use ESAB\Plugin\Register;
use ESAB\Plugin\Style;
use ESAB\Plugin\Fonts;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Accordion' ) ) {

    class Accordion {

        use Instance;

        public function __construct() {
            // Category
            Category::instance();
            // Register
            Register::instance(); 
            // Style
            Style::instance();
            // Fonts
            Fonts::instance();
        }
        
    }
    
}