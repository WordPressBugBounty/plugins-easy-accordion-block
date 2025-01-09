<?php
/**
 * Admin Page Handler
 * 
 * @package Easy_Accordion_Block
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin Page Handler
 */
class Esab_Admin {

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
        add_action( 'admin_menu', [ $this, 'esab_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'esab_admin_assets' ] );
        add_action( 'admin_init', [$this, 'esab_accordion_dci_plugin'] );
    }

    /**
     * Enqueue admin scripts
     */
    public function esab_admin_assets($screen) {
        if( $screen === 'settings_page_esab-accordion' ){
            wp_enqueue_style( 'esab-admin-style', ESAB_URL . 'inc/Admin/admin.css', [], ESAB_VERSION );
            wp_enqueue_script( 'esab-admin-script', ESAB_URL . 'inc/Admin/admin.js', [ 'jquery' ], ESAB_VERSION, true );
        }
    }

    /**
     * Add admin menu
     */
    public function esab_admin_menu() {
        add_submenu_page(
            'options-general.php',
            __( 'Easy Accordion', 'easy-accordion-block' ),
            __( 'Easy Accordion', 'easy-accordion-block' ),
            'manage_options',
            'esab-accordion',
            [ $this, 'esab_admin_page' ]
        );
    }

    /**
     * Admin page
     */
    public function esab_admin_page() {
        ?>
        <div class="esab__wrap">
            <div class="plugin_max_container">
                <div class="plugin__head_container">
                    <div class="plugin_head">
                        <h1 class="plugin_title">
                            <?php _e( 'Easy Accordion Block', 'easy-accordion-block' ); ?>
                        </h1>
                        <p class="plugin_description">
                            <?php _e( 'Easy Accordion Block is a Gutenberg block plugin that allows you to create accordion blocks with ease in Gutenberg Editor without any coding knowledge', 'easy-accordion-block' ); ?>
                        </p>
                        <button class="credit-btn">
                            <a href="https://accordion.gutenbergkits.com" target="_blank">
                                <?php _e( 'See Demos', 'easy-accordion-block' ); ?>
                            </a>
                        </button>
                    </div>
                </div>
                <div class="plugin__body_container">
                    <div class="plugin_body">
                        <div class="tabs__panel_container">
                            <div class="tabs__titles">
                                <p class="tab__title active" data-tab="tab1">
                                    <?php _e( 'Help and Support', 'easy-accordion-block' ); ?>
                                </p>
                            </div>
                            <div class="tabs__container">
                                <div class="tabs__panels">
                                    <div class="tab__panel active" id="tab1">
                                        <div class="tab__panel_flex">
                                            <div class="tab__panel_left">
                                                <h3 class="video__title">
                                                    <?php _e( 'Video Tutorial', 'easy-accordion-block' ); ?>
                                                </h3>
                                                <p class="video__description">
                                                    <?php _e( 'Watch the video tutorial to learn how to use the plugin. It will help you start your own design quickly.', 'easy-accordion-block' ); ?>
                                                </p>
                                                <div class="video__container">
                                                    <iframe width="560" height="315" src="https://www.youtube.com/embed/Hh3LNLpwzX4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                </div>
                                            </div>
                                            <div class="tab__panel_right">
                                                <div class="single__support_panel">
                                                    <h3 class="support__title">
                                                        <?php _e( 'Get Support', 'easy-accordion-block' ); ?>
                                                    </h3>
                                                    <p class="support__description">
                                                        <?php _e( 'If you find any issue or have any suggestion, please let me know.', 'easy-accordion-block' ); ?>
                                                    </p>
                                                    <a href="https://wordpress.org/support/plugin/easy-accordion-block/" class="support__link" target="_blank">
                                                        <?php _e( 'Support', 'easy-accordion-block' ); ?>
                                                    </a>
                                                </div>
                                                <div class="single__support_panel">
                                                    <h3 class="support__title">
                                                        <?php _e( 'Spread Your Love', 'easy-accordion-block' ); ?>
                                                    </h3>
                                                    <p class="support__description">
                                                        <?php _e( 'If you like this plugin, please share your opinion', 'easy-accordion-block' ); ?>
                                                    </p>
                                                    <a href="https://wordpress.org/support/plugin/easy-accordion-block/reviews/" class="support__link" target="_blank">
                                                        <?php _e( 'Rate the Plugin', 'easy-accordion-block' ); ?>
                                                    </a>
                                                </div>
                                                <div class="single__support_panel">
                                                    <h3 class="support__title">
                                                        <?php _e( 'Similar Blocks', 'easy-accordion-block' ); ?>
                                                    </h3>
                                                    <p class="support__description">
                                                        <?php _e( 'Want to get more similar blocks, please visit my website', 'easy-accordion-block' ); ?>
                                                    </p>
                                                    <a href="https://gutenbergkits.com" class="support__link" target="_blank">
                                                        <?php _e( 'Visit Our Website', 'easy-accordion-block' ); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="custom__block_request">
                                            <h3 class="custom__block_request_title">
                                                <?php _e( 'Need to Hire Us?', 'easy-accordion-block' ); ?>
                                            </h3>
                                            <p class="custom__block_request_description">
                                                <?php _e( 'Available for any freelance projects. Please feel free to share your project detail with us.', 'easy-accordion-block' ); ?>
                                            </p>
                                            <div class="available__links">
                                                <a href="mailto:info@gutenbergkits.com" class="available__link mail" target="_blank">
                                                    <?php _e( 'Send Email', 'easy-accordion-block' ); ?>
                                                </a>
                                                <a href="https://gutenbergkits.com/contact" class="available__link web" target="_blank">
                                                    <?php _e( 'Send Message', 'easy-accordion-block' ); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * SDK Integration
     */
    function esab_accordion_dci_plugin() {
        // Include DCI SDK.
        require_once ESAP_PATH . 'inc/Admin/dci/start.php';
        wp_register_style('dci-sdk-esab-accordion', plugins_url('dci/assets/css/dci.css', __FILE__), array(), '1.2.1', 'all');
        wp_enqueue_style('dci-sdk-esab-accordion');
        dci_dynamic_init( array(
            'sdk_version'          => '1.2.1',
            'product_id'           => 6,
            'plugin_name'          => 'Easy Accordion Block',                                                               // make simple, must not empty
            'plugin_title'         => 'Love using Easy Accordion Block? Congrats 🎉  ( Never miss an Important Update )',   // You can describe your plugin title here
            'plugin_icon'          => '',                                      // delete the line if you don't need
            'api_endpoint'         => 'https://dashboard.codedivo.com/wp-json/dci/v1/data-insights',
            'slug'                 => 'easy-accordion-block',                                                                  // folder-name or write 'no-need' if you don't want to use
            'core_file'            => false,
            'plugin_deactivate_id' => false,
            'menu'                 => array(
                'slug' => 'esab-accordion',
            ),
            'public_key' => 'pk_2zPuK3VzDOtZ2HEZuT9zBFUu8d2iQw3z',
            'is_premium' => false,
            //'custom_data' => array(
            //'test' => 'value',
            //),
            'popup_notice'        => false,
            'deactivate_feedback' => false,
            'text_domain'         => 'easy-accordion-block',
            'plugin_msg'          => '<p>Be Top-contributor by sharing non-sensitive plugin data and create an impact to the global WordPress community today! You can receive valuable emails periodically.</p>',
        ) );
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

Esab_Admin::instance(); // Initialize the class