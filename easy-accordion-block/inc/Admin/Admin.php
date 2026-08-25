<?php
/**
 * Admin Page Handler
 *
 * Renders the plugin dashboard (Dashboard / Help & Support / License tabs).
 * Markup for each tab lives in inc/Admin/views/.
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
     * Menu slug for the dashboard page.
     */
    const PAGE_SLUG = 'esab-accordion';

    /**
     * Option holding the steps the user has ticked off manually.
     */
    const ONBOARDING_OPTION = 'esab_onboarding_steps';

    /**
     * Transient caching the "has an accordion block" lookup.
     */
    const BLOCK_USED_TRANSIENT = 'esab_block_in_use';

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
        add_action( 'wp_ajax_esab_complete_step', [ $this, 'esab_complete_step' ] );
    }

    /**
     * Enqueue admin scripts
     *
     * @param string $screen Current admin screen hook.
     */
    public function esab_admin_assets( $screen ) {
        if ( 'settings_page_' . self::PAGE_SLUG !== $screen ) {
            return;
        }

        wp_enqueue_style(
            'esab-admin-fonts',
            'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap',
            [],
            null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts is versioned upstream.
        );

        wp_enqueue_style( 'esab-admin-style', ESAB_URL . 'inc/Admin/admin.css', [ 'esab-admin-fonts' ], ESAB_VERSION );
        wp_enqueue_script( 'esab-admin-script', ESAB_URL . 'inc/Admin/admin.js', [], ESAB_VERSION, true );

        wp_localize_script(
            'esab-admin-script',
            'esabAdmin',
            [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'esab_admin' ),
                'i18n'    => [
                    'copied'     => __( 'Copied', 'easy-accordion-block' ),
                    'copyFailed' => __( 'Press Ctrl/Cmd + C to copy', 'easy-accordion-block' ),
                ],
            ]
        );
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
            self::PAGE_SLUG,
            [ $this, 'esab_admin_page' ]
        );
    }

    /**
     * Is the Pro add-on running?
     *
     * @return bool
     */
    public static function is_pro() {
        return class_exists( 'Esabp_Accordion_Block_Pro' );
    }

    /**
     * External and internal links used across the dashboard.
     *
     * Update the values below to point at the real destinations.
     *
     * @param string $key Link key.
     * @return string
     */
    public static function link( $key ) {
        $links = [
            // Verified destinations already used elsewhere in the plugin.
            'pricing'    => 'https://accordion.gutenbergkits.com/pricing/',
            'demos'      => 'https://accordion.gutenbergkits.com/demos',
            'forum'      => 'https://wordpress.org/support/plugin/easy-accordion-block/',
            'review'     => 'https://wordpress.org/support/plugin/easy-accordion-block/reviews/',
            'youtube'    => 'https://www.youtube.com/watch?v=Hh3LNLpwzX4',
            'website'    => 'https://gutenbergkits.com',
            'gutenlayouts' => 'https://gutenlayouts.com/',
            'gutslider'  => 'https://gutslider.com',

            // TODO: replace the placeholders below with the real URLs.
            'docs'       => 'https://accordion.gutenbergkits.com/docs/',
            'features'   => 'https://accordion.gutenbergkits.com/features/',
            'support'    => 'https://accordion.gutenbergkits.com/support/',
            'ticket'     => 'https://accordion.gutenbergkits.com/support/',
            'whatsapp'   => 'https://accordion.gutenbergkits.com/support/',
            'telegram'   => 'https://accordion.gutenbergkits.com/support/',
            'email'      => 'mailto:zbinsaifullah@gmail.com',
            'roadmap'    => 'https://accordion.gutenbergkits.com/roadmap/',
            'changelog'  => 'https://wordpress.org/plugins/easy-accordion-block/#developers',
            'testimonial' => 'https://gutenbergkits.com',
        ];

        /**
         * Filter the dashboard link map.
         *
         * @param array $links Key => URL pairs.
         */
        $links = apply_filters( 'esab_admin_links', $links );

        return isset( $links[ $key ] ) ? $links[ $key ] : '';
    }

    /**
     * URL of a dashboard tab.
     *
     * @param string $tab Tab slug.
     * @return string
     */
    public static function tab_url( $tab ) {
        return add_query_arg(
            [
                'page' => self::PAGE_SLUG,
                'tab'  => $tab,
            ],
            admin_url( 'options-general.php' )
        );
    }

    /**
     * The tab currently being viewed.
     *
     * @return string
     */
    public static function current_tab() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only tab switch.
        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'dashboard';

        $allowed = [ 'dashboard', 'help' ];
        if ( self::is_pro() ) {
            $allowed[] = 'license';
        }

        return in_array( $tab, $allowed, true ) ? $tab : 'dashboard';
    }

    /**
     * Has the accordion block been used anywhere on this site yet?
     *
     * @return bool
     */
    public static function has_accordion_block() {
        $cached = get_transient( self::BLOCK_USED_TRANSIENT );
        if ( false !== $cached ) {
            return 'yes' === $cached;
        }

        $found = get_posts(
            [
                'post_type'        => 'any',
                'post_status'      => [ 'publish', 'draft', 'pending', 'private', 'future' ],
                'numberposts'      => 1,
                'fields'           => 'ids',
                's'                => 'wp:esab/accordion',
                'suppress_filters' => true,
                'no_found_rows'    => true,
            ]
        );

        $result = empty( $found ) ? 'no' : 'yes';
        set_transient( self::BLOCK_USED_TRANSIENT, $result, HOUR_IN_SECONDS );

        return 'yes' === $result;
    }

    /**
     * Steps shown in the "Finish setting up" card.
     *
     * @return array
     */
    public static function setup_steps() {
        $manual = get_option( self::ONBOARDING_OPTION, [] );
        $manual = is_array( $manual ) ? $manual : [];

        return [
            [
                'id'       => 'install',
                'title'    => __( 'Install & activate the plugin', 'easy-accordion-block' ),
                'desc'     => __( 'Easy Accordion Block is running on this site.', 'easy-accordion-block' ),
                'done'     => true,
                'action'   => '',
                'href'     => '',
                'external' => false,
                'manual'   => false,
            ],
            [
                'id'       => 'first-block',
                'title'    => __( 'Add your first accordion block', 'easy-accordion-block' ),
                'desc'     => __( 'Open any post or page and search for “Accordion”.', 'easy-accordion-block' ),
                'done'     => self::has_accordion_block(),
                'action'   => __( 'Set up', 'easy-accordion-block' ),
                'href'     => admin_url( 'post-new.php?post_type=page' ),
                'external' => false,
                'manual'   => false,
            ],
            [
                'id'       => 'pattern',
                'title'    => __( 'Start from a premade pattern', 'easy-accordion-block' ),
                'desc'     => __( 'Insert a ready-made accordion layout, then make it yours.', 'easy-accordion-block' ),
                'done'     => ! empty( $manual['pattern'] ),
                'action'   => __( 'Set up', 'easy-accordion-block' ),
                'href'     => admin_url( 'post-new.php?post_type=page' ),
                'external' => false,
                'manual'   => true,
            ],
            [
                'id'       => 'tutorial',
                'title'    => __( 'Watch the 3-minute tutorial', 'easy-accordion-block' ),
                'desc'     => __( 'See a page go from empty to a live FAQ section.', 'easy-accordion-block' ),
                'done'     => ! empty( $manual['tutorial'] ),
                'action'   => __( 'Set up', 'easy-accordion-block' ),
                'href'     => self::link( 'youtube' ),
                'external' => true,
                'manual'   => true,
            ],
        ];
    }

    /**
     * Mark an onboarding step complete.
     */
    public function esab_complete_step() {
        check_ajax_referer( 'esab_admin', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( null, 403 );
        }

        $step    = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
        $allowed = [ 'pattern', 'tutorial' ];

        if ( ! in_array( $step, $allowed, true ) ) {
            wp_send_json_error( null, 400 );
        }

        $manual = get_option( self::ONBOARDING_OPTION, [] );
        $manual = is_array( $manual ) ? $manual : [];

        $manual[ $step ] = true;
        update_option( self::ONBOARDING_OPTION, $manual, false );

        wp_send_json_success();
    }

    /**
     * Environment rows for the system report card.
     *
     * @return array
     */
    public static function system_report() {
        $theme = wp_get_theme();

        return [
            [
                'key'   => __( 'Plugin version', 'easy-accordion-block' ),
                'value' => ESAB_VERSION,
                'ok'    => true,
            ],
            [
                'key'   => __( 'WordPress', 'easy-accordion-block' ),
                'value' => get_bloginfo( 'version' ),
                'ok'    => version_compare( get_bloginfo( 'version' ), '6.6', '>=' ),
            ],
            [
                'key'   => __( 'PHP', 'easy-accordion-block' ),
                'value' => PHP_VERSION,
                'ok'    => version_compare( PHP_VERSION, '7.4', '>=' ),
            ],
            [
                'key'   => __( 'Active theme', 'easy-accordion-block' ),
                'value' => $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ),
                'ok'    => true,
            ],
            [
                'key'   => __( 'Memory limit', 'easy-accordion-block' ),
                'value' => WP_MEMORY_LIMIT,
                'ok'    => true,
            ],
            [
                'key'   => __( 'Block editor', 'easy-accordion-block' ),
                'value' => function_exists( 'register_block_type' ) ? __( 'Enabled', 'easy-accordion-block' ) : __( 'Disabled', 'easy-accordion-block' ),
                'ok'    => function_exists( 'register_block_type' ),
            ],
        ];
    }

    /**
     * Attributes allowed inside the inline icon markup.
     *
     * @return array
     */
    public static function svg_kses() {
        return [
            'svg'     => [
                'xmlns'            => [],
                'width'            => [],
                'height'           => [],
                'viewbox'          => [],
                'fill'             => [],
                'stroke'           => [],
                'stroke-width'     => [],
                'stroke-linecap'   => [],
                'stroke-linejoin'  => [],
                'stroke-dasharray' => [],
                'class'            => [],
                'aria-hidden'      => [],
                'focusable'        => [],
            ],
            'path'    => [
                'd'                => [],
                'fill'             => [],
                'stroke-dasharray' => [],
            ],
            'circle'  => [
                'cx'               => [],
                'cy'               => [],
                'r'                => [],
                'fill'             => [],
                'stroke-dasharray' => [],
            ],
            'rect'    => [
                'x'      => [],
                'y'      => [],
                'width'  => [],
                'height' => [],
                'rx'     => [],
                'ry'     => [],
                'fill'   => [],
            ],
            'polygon' => [
                'points' => [],
                'fill'   => [],
            ],
        ];
    }

    /**
     * Return an inline Lucide icon.
     *
     * @param string $name  Icon name.
     * @param int    $size  Pixel size.
     * @param string $class Extra class name.
     * @return string
     */
    public static function icon( $name, $size = 16, $class = '' ) {
        $icons = [
            'rows-3'         => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M21 9H3"/><path d="M21 15H3"/>',
            'book-open'      => '<path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/>',
            'life-buoy'      => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/>',
            'zap'            => '<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>',
            'plus'           => '<path d="M5 12h14"/><path d="M12 5v14"/>',
            'minus'          => '<path d="M5 12h14"/>',
            'play'           => '<polygon points="6 3 20 12 6 21 6 3"/>',
            'check'          => '<path d="M20 6 9 17l-5-5"/>',
            'circle-dashed'  => '<circle cx="12" cy="12" r="9" stroke-dasharray="3.6 3"/>',
            'arrow-up-right' => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
            'arrow-right'    => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
            'chevron-right'  => '<path d="m9 18 6-6-6-6"/>',
            'blocks'         => '<rect x="14" y="3" width="7" height="7" rx="1"/><path d="M10 21V8a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1H3"/>',
            'smartphone'     => '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>',
            'search-check'   => '<path d="m8 11 2 2 4-4"/><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
            'list-tree'      => '<path d="M21 12h-8"/><path d="M21 6H8"/><path d="M21 18h-8"/><path d="M3 6v4c0 1.1.9 2 2 2h3"/><path d="M3 10v6c0 1.1.9 2 2 2h3"/>',
            'keyboard'       => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h.01"/><path d="M10 8h.01"/><path d="M14 8h.01"/><path d="M18 8h.01"/><path d="M8 12h.01"/><path d="M12 12h.01"/><path d="M16 12h.01"/><path d="M7 16h10"/>',
            'crown'          => '<path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"/><path d="M5 21h14"/>',
            'star'           => '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/>',
            'lightbulb'      => '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>',
            'layout-template' => '<rect x="3" y="3" width="18" height="7" rx="1"/><rect x="3" y="14" width="9" height="7" rx="1"/><rect x="16" y="14" width="5" height="7" rx="1"/>',
            'gallery-horizontal-end' => '<path d="M2 7v10"/><path d="M6 5v14"/><rect x="10" y="3" width="12" height="18" rx="2"/>',
            'quote'          => '<path d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/><path d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/>',
            'ticket'         => '<path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/><path d="M13 5v2"/><path d="M13 11v2"/><path d="M13 17v2"/>',
            'message-circle' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22z"/>',
            'send'           => '<path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/>',
            'mail'           => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
            'triangle-alert' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
            'messages-square' => '<path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/>',
            'history'        => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>',
            'circle-check'   => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
            'copy'           => '<rect x="8" y="8" width="14" height="14" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>',
        ];

        if ( ! isset( $icons[ $name ] ) ) {
            return '';
        }

        $filled = in_array( $name, [ 'play', 'star', 'quote', 'crown' ], true );

        return sprintf(
            '<svg class="esab-icon %1$s" xmlns="http://www.w3.org/2000/svg" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="%3$s" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%4$s</svg>',
            esc_attr( $class ),
            (int) $size,
            $filled ? 'currentColor' : 'none',
            $icons[ $name ]
        );
    }

    /**
     * Echo an inline icon.
     *
     * @param string $name  Icon name.
     * @param int    $size  Pixel size.
     * @param string $class Extra class name.
     */
    public static function the_icon( $name, $size = 16, $class = '' ) {
        echo wp_kses( self::icon( $name, $size, $class ), self::svg_kses() );
    }

    /**
     * Admin page
     */
    public function esab_admin_page() {
        $tab        = self::current_tab();
        $is_pro     = self::is_pro();
        $plugin_name = $is_pro
            ? __( 'Easy Accordion Block Pro', 'easy-accordion-block' )
            : __( 'Easy Accordion Block', 'easy-accordion-block' );

        $tabs = [
            'dashboard' => __( 'Dashboard', 'easy-accordion-block' ),
            'help'      => __( 'Help & Support', 'easy-accordion-block' ),
        ];

        if ( $is_pro ) {
            $tabs['license'] = __( 'License', 'easy-accordion-block' );
        }
        ?>
        <div class="esab-admin">
            <header class="esab-topbar">
                <div class="esab-brand">
                    <span class="esab-brand__mark"><?php self::the_icon( 'rows-3', 22 ); ?></span>
                    <span class="esab-brand__text">
                        <span class="esab-brand__row">
                            <span class="esab-brand__name"><?php echo esc_html( $plugin_name ); ?></span>
                            <span class="esab-badge">v<?php echo esc_html( ESAB_VERSION ); ?></span>
                        </span>
                        <span class="esab-brand__tagline">
                            <?php esc_html_e( 'Accordions & FAQs for the Gutenberg editor', 'easy-accordion-block' ); ?>
                        </span>
                    </span>
                </div>
                <nav class="esab-topbar__actions">
                    <a class="esab-btn esab-btn--ghost" href="<?php echo esc_url( self::link( 'docs' ) ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php self::the_icon( 'book-open', 15 ); ?>
                        <span><?php esc_html_e( 'Documentation', 'easy-accordion-block' ); ?></span>
                    </a>
                    <a class="esab-btn esab-btn--ghost" href="<?php echo esc_url( self::tab_url( 'help' ) ); ?>">
                        <?php self::the_icon( 'life-buoy', 15 ); ?>
                        <span><?php esc_html_e( 'Support', 'easy-accordion-block' ); ?></span>
                    </a>
                    <?php if ( $is_pro ) : ?>
                        <a class="esab-btn esab-btn--primary" href="<?php echo esc_url( self::link( 'demos' ) ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php self::the_icon( 'crown', 15 ); ?>
                            <span><?php esc_html_e( 'Explore Demos', 'easy-accordion-block' ); ?></span>
                        </a>
                    <?php else : ?>
                        <a class="esab-btn esab-btn--primary" href="<?php echo esc_url( self::link( 'pricing' ) ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php self::the_icon( 'zap', 15 ); ?>
                            <span><?php esc_html_e( 'Upgrade to Pro', 'easy-accordion-block' ); ?></span>
                        </a>
                    <?php endif; ?>
                </nav>
            </header>

            <nav class="esab-tabs">
                <?php foreach ( $tabs as $slug => $label ) : ?>
                    <a
                        class="esab-tabs__item<?php echo $slug === $tab ? ' is-active' : ''; ?>"
                        href="<?php echo esc_url( self::tab_url( $slug ) ); ?>"
                        <?php echo $slug === $tab ? ' aria-current="page"' : ''; ?>
                    >
                        <?php echo esc_html( $label ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <main class="esab-body">
                <?php
                if ( 'help' === $tab ) {
                    require ESAB_PATH . 'inc/Admin/views/help-support.php';
                } elseif ( 'license' === $tab ) {
                    echo '<div class="esab-card esab-card--pad esab-license">';
                    do_action( 'esab_license_tab' );
                    echo '</div>';
                } else {
                    require ESAB_PATH . 'inc/Admin/views/dashboard.php';
                }
                ?>
            </main>
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
