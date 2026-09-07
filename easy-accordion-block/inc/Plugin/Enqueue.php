<?php
/**
 * Enqueue all necessary scripts and styles
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Esab_Enqueue' ) ) {

    class Esab_Enqueue {

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
            add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets'], 2);
            add_action('wp_enqueue_scripts', [$this, 'frontend_runtime_config'], 20);
        }

        /**
         * Hand the frontend script its feature flags.
         *
         * The view script is registered at init by register_block_type() and
         * only enqueued on pages that actually use the block, so attaching an
         * inline script here is a no-op everywhere else.
         */
        public function frontend_runtime_config() {
            if ( ! class_exists( 'Esab_Settings' ) ) {
                return;
            }

            $config = wp_json_encode( array(
                'a11yEnhanced' => Esab_Settings::get( 'a11y_enhanced' ),
            ) );

            foreach ( array( 'esab-accordion-view-script', 'esabp-faqs-view-script' ) as $handle ) {
                if ( wp_scripts()->query( $handle, 'registered' ) ) {
                    wp_add_inline_script( $handle, 'window.esabView=Object.assign(window.esabView||{},' . $config . ');', 'before' );
                }
            }
        }

        /**
         * Editor assets enqueue
         */
        public function enqueue_editor_assets(){

            $dep = ESAB_PATH . 'build/modules/index.asset.php';
            if( file_exists( $dep ) ) {
                $asset = require_once $dep;

                wp_enqueue_script(
                    'esab-modules',
                    ESAB_URL . 'build/modules/index.js',
                    $asset['dependencies'],
                    $asset['version'],
                    false
                );

                wp_enqueue_style(
                    'esab-modules',
                    ESAB_URL . 'build/modules/style-index.css',
                    [],
                    ESAB_VERSION
                );

                wp_localize_script( 'esab-modules', 'esabData', [
                    'hasPro'   => class_exists( 'Esabp_Accordion_Block_Pro' ) ? true : false,
                    'features' => class_exists( 'Esab_Settings' ) ? Esab_Settings::all() : [],
                ] );

            }

            $gdep = ESAB_PATH . 'build/global/index.asset.php';
            if( file_exists( $gdep ) ) {
                $gasset = require_once $gdep;

                wp_enqueue_script(
                    'esab-editor-global',
                    ESAB_URL . 'build/global/index.js',
                    $gasset['dependencies'],
                    $gasset['version'],
                    false
                );

                wp_enqueue_style(
                    'esab-editor-global',
                    ESAB_URL . 'build/global/style-index.css',
                    [],
                    ESAB_VERSION
                );
            }

        }
    }

    new Esab_Enqueue();
}