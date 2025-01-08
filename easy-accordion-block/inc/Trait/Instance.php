<?php 
namespace ESAB\Trait;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Instance {

    /**
     * Instance of the class
     *
     * @var null
     */
    private static $instance = null;

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

