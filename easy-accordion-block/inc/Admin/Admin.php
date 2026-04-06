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
                            <?php 
                                if( class_exists( 'Esabp_Accordion_Block_Pro' ) ){
                                    esc_html_e( 'Easy Accordion Block Pro', 'easy-accordion-block' );
                                } else {
                                    esc_html_e( 'Easy Accordion Block', 'easy-accordion-block' );
                                }
                            ?>
                        </h1>
                        <p class="plugin_description">
                            <?php esc_html_e( 'Easy Accordion Block is a Gutenberg block plugin that allows you to create accordion blocks with ease in Gutenberg Editor without any coding knowledge', 'easy-accordion-block' ); ?>
                        </p>
                    </div>
                </div>
                <div class="plugin__body_container">
                    <div class="plugin_body">
                        <div class="tabs__panel_container">
                            <div class="tabs__titles">
                                <p class="tab__title active" data-tab="tab1">
                                    <?php esc_html_e( 'Help and Support', 'easy-accordion-block' ); ?>
                                </p>
                                <?php 
                                        if( class_exists( 'Esabp_Accordion_Block_Pro' ) ){
                                    ?>
                                        <p class="tab__title" data-tab="tab2">
                                            <?php esc_html_e( 'License', 'easy-accordion-block' ); ?>
                                        </p>
                                    <?php
                                        }
                                ?>
                            </div>
                            <div class="tabs__container">
                                <div class="tabs__panels">
                                    <div class="tab__panel active" id="tab1">
                                        <div class="tab__panel_flex">
                                            <div class="tab__panel_left">
                                                <h3 class="video__title">
                                                    <?php esc_html_e( 'Video Tutorial', 'easy-accordion-block' ); ?>
                                                </h3>
                                                <p class="video__description">
                                                    <?php esc_html_e( 'Watch the video tutorial to learn how to use the plugin. It will help you start your own design quickly.', 'easy-accordion-block' ); ?>
                                                </p>
                                                <div class="video__container">
                                                    <iframe width="560" height="415" src="https://www.youtube.com/embed/Hh3LNLpwzX4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                </div>
                                            </div>
                                            <div class="tab__panel_right">
                                                <div class="recommended-products">
                                                    <h3 class="recommended__title">
                                                        <?php esc_html_e( 'Recommended Products', 'easy-accordion-block' ); ?>
                                                    </h3>
                                                    <div class="products-list">
                                                        <div class="product__item">
                                                            <a href="https://gutenlayouts.com/" target="_blank">
                                                                <h4 class="title">
                                                                    <?php esc_html_e( 'GutenLayouts', 'easy-accordion-block' ); ?>
                                                                </h4>
                                                                <p class="description">
                                                                    <?php esc_html_e( 'Collection of Gutenberg Patterns and Templates', 'easy-accordion-block' ); ?>
                                                                </p>
                                                                <span class="read-more">
                                                                    <?php esc_html_e( 'Read More', 'easy-accordion-block' ); ?>
                                                                </span>
                                                            </a>
                                                        </div>
                                                        <div class="product__item">
                                                            <a href="https://gutslider.com" target="_blank">
                                                                <h4 class="title">
                                                                    <?php esc_html_e( 'GutSlider', 'easy-accordion-block' ); ?>
                                                                </h4>
                                                                <p class="description">
                                                                    <?php esc_html_e( 'A powerful slider block for Gutenberg', 'easy-accordion-block' ); ?>
                                                                </p>
                                                                <span class="read-more">
                                                                    <?php esc_html_e( 'Read More', 'easy-accordion-block' ); ?>
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="pro-panel">
                                                    <h3 class="pro__title">
                                                        <?php 
                                                            if( class_exists( 'Esabp_Accordion_Block_Pro' ) ){
                                                                esc_html_e( 'Thank you for using the Pro version', 'easy-accordion-block' );
                                                            } else {
                                                                esc_html_e( 'Unlock Pro Features', 'easy-accordion-block' );
                                                            }
                                                        ?>
                                                    </h3>
                                                    <p class="pro__description">
                                                        <?php esc_html_e( 'More features and customization options with the pro version of the plugin.', 'easy-accordion-block' ); ?>
                                                    </p>
                                                    <ul class="features-list">
                                                        <li>
                                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><g clip-path="url(#clip0_6404_1763)"><path d="M12.9955 5.64817L7.93251 12.4574L4.99665 10.2744" stroke="#5A03EF" stroke-width="1.5"></path></g><defs><clipPath id="clip0_6404_1763"><rect width="18" height="18" rx="9" fill="white"></rect></clipPath></defs></svg>
                                                            <?php esc_html_e( 'Instant Search', 'easy-accordion-block' ); ?>
                                                        </li>
                                                        <li>
                                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><g clip-path="url(#clip0_6404_1763)"><path d="M12.9955 5.64817L7.93251 12.4574L4.99665 10.2744" stroke="#5A03EF" stroke-width="1.5"></path></g><defs><clipPath id="clip0_6404_1763"><rect width="18" height="18" rx="9" fill="white"></rect></clipPath></defs></svg>
                                                            <?php esc_html_e( 'Load More', 'easy-accordion-block' ); ?>
                                                        </li>
                                                        <li>
                                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><g clip-path="url(#clip0_6404_1763)"><path d="M12.9955 5.64817L7.93251 12.4574L4.99665 10.2744" stroke="#5A03EF" stroke-width="1.5"></path></g><defs><clipPath id="clip0_6404_1763"><rect width="18" height="18" rx="9" fill="white"></rect></clipPath></defs></svg>
                                                            <?php esc_html_e( 'Badge for Accordion', 'easy-accordion-block' ); ?>
                                                        </li>
                                                        <li>
                                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><g clip-path="url(#clip0_6404_1763)"><path d="M12.9955 5.64817L7.93251 12.4574L4.99665 10.2744" stroke="#5A03EF" stroke-width="1.5"></path></g><defs><clipPath id="clip0_6404_1763"><rect width="18" height="18" rx="9" fill="white"></rect></clipPath></defs></svg>
                                                            <?php esc_html_e( '3 Event Activators', 'easy-accordion-block' ); ?>
                                                        </li>
                                                        <li>
                                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><g clip-path="url(#clip0_6404_1763)"><path d="M12.9955 5.64817L7.93251 12.4574L4.99665 10.2744" stroke="#5A03EF" stroke-width="1.5"></path></g><defs><clipPath id="clip0_6404_1763"><rect width="18" height="18" rx="9" fill="white"></rect></clipPath></defs></svg>
                                                            <?php esc_html_e( 'Custom Icon Upload', 'easy-accordion-block' ); ?>
                                                        </li>
                                                        <li>
                                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><g clip-path="url(#clip0_6404_1763)"><path d="M12.9955 5.64817L7.93251 12.4574L4.99665 10.2744" stroke="#5A03EF" stroke-width="1.5"></path></g><defs><clipPath id="clip0_6404_1763"><rect width="18" height="18" rx="9" fill="white"></rect></clipPath></defs></svg>
                                                            <?php esc_html_e( 'Link Any Accordion', 'easy-accordion-block' ); ?>
                                                        </li>
                                                        <li>
                                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><g clip-path="url(#clip0_6404_1763)"><path d="M12.9955 5.64817L7.93251 12.4574L4.99665 10.2744" stroke="#5A03EF" stroke-width="1.5"></path></g><defs><clipPath id="clip0_6404_1763"><rect width="18" height="18" rx="9" fill="white"></rect></clipPath></defs></svg>
                                                            <?php esc_html_e( 'Disable Specific Accordion', 'easy-accordion-block' ); ?>
                                                        </li>
                                                        <li>
                                                            <?php esc_html_e( 'And more...', 'easy-accordion-block' ); ?>
                                                        </li>
                                                    </ul>
                                                    <div class="actions">
                                                        <?php 
                                                            if( class_exists( 'Esabp_Accordion_Block_Pro' ) ){
                                                                ?>
                                                                <a href="https://accordion.gutenbergkits.com/demos" class="pro__link"
                                                                    target="_blank">
                                                                        <?php esc_html_e( 'Explore Demos', 'easy-accordion-block' ); ?>
                                                                </a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                    <a href="https://accordion.gutenbergkits.com/pricing/" class="pro__link"
                                                                        target="_blank">
                                                                            <?php esc_html_e( 'Get Pro Version', 'easy-accordion-block' ); ?>
                                                                    </a>
                                                                <?php 
                                                            }
                                                        ?>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                        if( class_exists( 'Esabp_Accordion_Block_Pro' ) ): 
                                    ?>
                                    <div class="tab__panel" id="tab2">
                                        <?php 
                                            do_action( 'esab_license_tab' );
                                        ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="footer-panels">
                                    <div class="single__support_panel">
                                        <h3 class="support__title">
                                            <?php esc_html_e( 'Get Support', 'easy-accordion-block' ); ?>
                                        </h3>
                                        <p class="support__description">
                                            <?php esc_html_e( 'If you find any issue or have any suggestion, please let me know.', 'easy-accordion-block' ); ?>
                                        </p>
                                        <a href="https://wordpress.org/support/plugin/easy-accordion-block/" class="support__link" target="_blank">
                                            <?php esc_html_e( 'Support', 'easy-accordion-block' ); ?>
                                        </a>
                                    </div>
                                    <div class="single__support_panel">
                                        <h3 class="support__title">
                                            <?php esc_html_e( 'Spread Your Love', 'easy-accordion-block' ); ?>
                                        </h3>
                                        <p class="support__description">
                                            <?php esc_html_e( 'If you like this plugin, please share your opinion', 'easy-accordion-block' ); ?>
                                        </p>
                                        <a href="https://wordpress.org/support/plugin/easy-accordion-block/reviews/" class="support__link" target="_blank">
                                            <?php esc_html_e( 'Rate the Plugin', 'easy-accordion-block' ); ?>
                                        </a>
                                    </div>
                                    <div class="single__support_panel">
                                        <h3 class="support__title">
                                            <?php esc_html_e( 'Similar Blocks', 'easy-accordion-block' ); ?>
                                        </h3>
                                        <p class="support__description">
                                            <?php esc_html_e( 'Want to get more similar blocks, please visit my website', 'easy-accordion-block' ); ?>
                                        </p>
                                        <a href="https://gutenbergkits.com" class="support__link" target="_blank">
                                            <?php esc_html_e( 'Visit Our Website', 'easy-accordion-block' ); ?>
                                        </a>
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