<?php
/**
 * Feature settings.
 *
 * A single autoloaded option, `esab_settings`, holds the on/off state of every
 * optional runtime feature. Each feature reads it through Esab_Settings::get()
 * and nothing else — there is one source of truth.
 *
 * None of these toggles change saved block markup: FAQ schema is emitted
 * server-side, deep-linking and the accessibility extras are applied by the
 * frontend script. Flipping a toggle therefore never invalidates a block.
 *
 * @package Easy_Accordion_Block
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Esab_Settings' ) ) {

    /**
     * Feature settings store.
     */
    class Esab_Settings {

        /**
         * Option name.
         */
        const OPTION = 'esab_settings';

        /**
         * Nonce action shared with the admin dashboard.
         */
        const NONCE = 'esab_admin';

        /**
         * Instance of the class.
         *
         * @var Esab_Settings|null
         */
        private static $instance = null;

        /**
         * Feature keys and their default state.
         *
         * a11y_enhanced - on: keyboard roving + a visible focus ring. The base
         *                 ARIA wiring runs regardless of this flag.
         *
         * @var array<string,bool>
         */
        const DEFAULTS = array(
            'a11y_enhanced' => true,
        );

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'admin_init', array( $this, 'register' ) );
            add_action( 'wp_ajax_esab_save_settings', array( $this, 'ajax_save' ) );
        }

        /**
         * Read one feature flag.
         *
         * @param string    $key     Feature key.
         * @param bool|null  $default Value when the key is unknown.
         * @return bool
         */
        public static function get( $key, $default = null ) {
            $all = self::all();

            if ( array_key_exists( $key, $all ) ) {
                return (bool) $all[ $key ];
            }

            if ( null !== $default ) {
                return (bool) $default;
            }

            return isset( self::DEFAULTS[ $key ] ) ? self::DEFAULTS[ $key ] : false;
        }

        /**
         * The full, normalised settings array.
         *
         * @return array<string,bool>
         */
        public static function all() {
            $stored = get_option( self::OPTION, array() );
            $stored = is_array( $stored ) ? $stored : array();

            return self::sanitize( $stored );
        }

        /**
         * Register the setting so it appears on the options whitelist.
         */
        public function register() {
            register_setting(
                'esab_settings_group',
                self::OPTION,
                array(
                    'type'              => 'object',
                    'sanitize_callback' => array( __CLASS__, 'sanitize' ),
                    'default'           => self::DEFAULTS,
                    'show_in_rest'      => false,
                )
            );
        }

        /**
         * Keep only known keys, cast everything to a real boolean.
         *
         * @param mixed $value Raw option value.
         * @return array<string,bool>
         */
        public static function sanitize( $value ) {
            $value = is_array( $value ) ? $value : array();
            $clean = array();

            foreach ( self::DEFAULTS as $key => $fallback ) {
                if ( array_key_exists( $key, $value ) ) {
                    $raw           = $value[ $key ];
                    $clean[ $key ] = ( '1' === $raw || 1 === $raw || true === $raw || 'true' === $raw );
                } else {
                    $clean[ $key ] = $fallback;
                }
            }

            return $clean;
        }

        /**
         * Persist a single toggle from the dashboard.
         *
         * Mirrors Esab_Admin::esab_complete_step() — same nonce action, same
         * capability gate, same JSON response shape.
         */
        public function ajax_save() {
            check_ajax_referer( self::NONCE, 'nonce' );

            if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( null, 403 );
            }

            $key = isset( $_POST['key'] ) ? sanitize_key( wp_unslash( $_POST['key'] ) ) : '';

            if ( ! array_key_exists( $key, self::DEFAULTS ) ) {
                wp_send_json_error( null, 400 );
            }

            $value = isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';
            $value = ( '1' === $value || 'true' === $value );

            $settings         = self::all();
            $settings[ $key ] = $value;

            update_option( self::OPTION, $settings, true );

            wp_send_json_success( array( 'settings' => $settings ) );
        }

        /**
         * Instance of the class.
         *
         * @return Esab_Settings
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }

    Esab_Settings::instance();
}
