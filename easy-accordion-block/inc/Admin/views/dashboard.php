<?php
/**
 * Dashboard tab.
 *
 * @package Easy_Accordion_Block
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$esab_is_pro = Esab_Admin::is_pro();
$esab_steps  = Esab_Admin::setup_steps();

$esab_done      = count(
    array_filter(
        $esab_steps,
        static function ( $step ) {
            return ! empty( $step['done'] );
        }
    )
);
$esab_total     = count( $esab_steps );
$esab_remaining = $esab_total - $esab_done;

$esab_preview = [
    [
        'q' => __( 'How do I add an accordion to a page?', 'easy-accordion-block' ),
        'a' => __( 'Add the Easy Accordion block, then type your content — it’s a normal block canvas.', 'easy-accordion-block' ),
    ],
    [
        'q' => __( 'Can I keep one item open by default?', 'easy-accordion-block' ),
        'a' => __( 'Yes. Turn on the initially-open option for any item and it renders expanded on first paint.', 'easy-accordion-block' ),
    ],
    [
        'q' => __( 'Does it support nested accordions?', 'easy-accordion-block' ),
        'a' => __( 'Yes. Drop another Easy Accordion block inside a panel and nest as deep as your layout needs.', 'easy-accordion-block' ),
    ],
];

$esab_features = [
    [
        'icon'  => 'zap',
        'title' => __( 'Lightweight & fast', 'easy-accordion-block' ),
        'desc'  => __( 'Optimized code that never slows your site down.', 'easy-accordion-block' ),
    ],
    [
        'icon'  => 'blocks',
        'title' => __( 'Native interface', 'easy-accordion-block' ),
        'desc'  => __( 'Built with Gutenberg’s own components.', 'easy-accordion-block' ),
    ],
    [
        'icon'  => 'smartphone',
        'title' => __( 'Fully responsive', 'easy-accordion-block' ),
        'desc'  => __( 'Mobile-first layout with full accessibility support.', 'easy-accordion-block' ),
    ],
    [
        'icon'  => 'search-check',
        'title' => __( 'FAQ schema markup', 'easy-accordion-block' ),
        'desc'  => __( 'A dedicated schema builder that boosts SEO.', 'easy-accordion-block' ),
    ],
    [
        'icon'  => 'list-tree',
        'title' => __( 'Nested accordions', 'easy-accordion-block' ),
        'desc'  => __( 'Build complex layouts with nested items.', 'easy-accordion-block' ),
    ],
    [
        'icon'  => 'keyboard',
        'title' => __( 'Keyboard accessible', 'easy-accordion-block' ),
        'desc'  => __( 'Full ARIA roles and keyboard navigation.', 'easy-accordion-block' ),
    ],
];

$esab_pro_features = [
    __( 'Instant search across items', 'easy-accordion-block' ),
    __( 'Load more pagination', 'easy-accordion-block' ),
    __( 'Badges for accordion items', 'easy-accordion-block' ),
    __( '3 event activators', 'easy-accordion-block' ),
    __( 'Custom icon upload', 'easy-accordion-block' ),
    __( 'Link to any accordion', 'easy-accordion-block' ),
    __( 'Disable specific accordions', 'easy-accordion-block' ),
];

$esab_help_links = [
    [
        'icon'  => 'life-buoy',
        'label' => __( 'Get support', 'easy-accordion-block' ),
        'desc'  => __( 'Report an issue or ask a question', 'easy-accordion-block' ),
        'href'  => Esab_Admin::tab_url( 'help' ),
        'blank' => false,
    ],
    [
        'icon'  => 'star',
        'label' => __( 'Rate the plugin', 'easy-accordion-block' ),
        'desc'  => __( 'Leave a review on WordPress.org', 'easy-accordion-block' ),
        'href'  => Esab_Admin::link( 'review' ),
        'blank' => true,
    ],
    [
        'icon'  => 'lightbulb',
        'label' => __( 'Request a feature', 'easy-accordion-block' ),
        'desc'  => __( 'Tell us what to build next', 'easy-accordion-block' ),
        'href'  => Esab_Admin::link( 'roadmap' ),
        'blank' => true,
    ],
];

$esab_products = [
    [
        'icon'  => 'layout-template',
        'label' => __( 'GutenLayouts', 'easy-accordion-block' ),
        'desc'  => __( 'Patterns and full-page templates', 'easy-accordion-block' ),
        'href'  => Esab_Admin::link( 'gutenlayouts' ),
    ],
    [
        'icon'  => 'gallery-horizontal-end',
        'label' => __( 'GutSlider', 'easy-accordion-block' ),
        'desc'  => __( 'A powerful slider block', 'easy-accordion-block' ),
        'href'  => Esab_Admin::link( 'gutslider' ),
    ],
    [
        'icon'  => 'quote',
        'label' => __( 'Easy Testimonial', 'easy-accordion-block' ),
        'desc'  => __( 'Social proof blocks that convert', 'easy-accordion-block' ),
        'href'  => Esab_Admin::link( 'testimonial' ),
    ],
];
?>

<section class="esab-welcome">
    <div class="esab-welcome__copy">
        <p class="esab-eyebrow esab-eyebrow--light"><?php esc_html_e( 'Welcome back', 'easy-accordion-block' ); ?></p>
        <h1 class="esab-welcome__headline">
            <?php esc_html_e( 'Build accordions and FAQs, right inside Gutenberg.', 'easy-accordion-block' ); ?>
        </h1>
        <p class="esab-welcome__subhead">
            <?php esc_html_e( 'No shortcodes, no page builders, no code. Everything is native blocks — so your accordions look and behave exactly like the rest of your site.', 'easy-accordion-block' ); ?>
        </p>
        <div class="esab-welcome__actions">
            <a class="esab-btn esab-btn--primary esab-btn--lg" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>">
                <?php Esab_Admin::the_icon( 'plus', 16 ); ?>
                <span><?php esc_html_e( 'Create your first accordion', 'easy-accordion-block' ); ?></span>
            </a>
            <a class="esab-btn esab-btn--outline-light esab-btn--lg" href="<?php echo esc_url( Esab_Admin::link( 'youtube' ) ); ?>" target="_blank" rel="noopener noreferrer">
                <?php Esab_Admin::the_icon( 'play', 15 ); ?>
                <span><?php esc_html_e( 'Watch the 3-min tour', 'easy-accordion-block' ); ?></span>
            </a>
        </div>
    </div>

    <div class="esab-welcome__preview" data-esab-accordion>
        <?php foreach ( $esab_preview as $esab_i => $esab_item ) : ?>
            <div class="esab-preview-item<?php echo 0 === $esab_i ? ' is-open' : ''; ?>">
                <button type="button" class="esab-preview-item__head" aria-expanded="<?php echo 0 === $esab_i ? 'true' : 'false'; ?>">
                    <span class="esab-preview-item__q"><?php echo esc_html( $esab_item['q'] ); ?></span>
                    <span class="esab-preview-item__toggle">
                        <?php Esab_Admin::the_icon( 'minus', 15, 'esab-icon--minus' ); ?>
                        <?php Esab_Admin::the_icon( 'plus', 15, 'esab-icon--plus' ); ?>
                    </span>
                </button>
                <div class="esab-preview-item__panel">
                    <p class="esab-preview-item__a"><?php echo esc_html( $esab_item['a'] ); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<div class="esab-columns">
    <div class="esab-columns__main">

        <section class="esab-card esab-setup">
            <div class="esab-card__head esab-card__head--split">
                <div class="esab-card__headtext">
                    <h2 class="esab-card__title"><?php esc_html_e( 'Finish setting up', 'easy-accordion-block' ); ?></h2>
                    <p class="esab-card__subtitle">
                        <?php
                        if ( $esab_remaining > 0 ) {
                            printf(
                                /* translators: %s: number of remaining onboarding steps. */
                                esc_html( _n( '%s step left before your accordions are production-ready.', '%s steps left before your accordions are production-ready.', $esab_remaining, 'easy-accordion-block' ) ),
                                esc_html( number_format_i18n( $esab_remaining ) )
                            );
                        } else {
                            esc_html_e( 'You’re all set — every step is complete.', 'easy-accordion-block' );
                        }
                        ?>
                    </p>
                </div>
                <div class="esab-progress">
                    <p class="esab-progress__count">
                        <?php
                        printf(
                            /* translators: 1: completed steps, 2: total steps. */
                            esc_html__( '%1$s of %2$s complete', 'easy-accordion-block' ),
                            esc_html( number_format_i18n( $esab_done ) ),
                            esc_html( number_format_i18n( $esab_total ) )
                        );
                        ?>
                    </p>
                    <span class="esab-progress__track">
                        <span class="esab-progress__fill" style="width:<?php echo esc_attr( round( ( $esab_done / max( 1, $esab_total ) ) * 100 ) ); ?>%"></span>
                    </span>
                </div>
            </div>

            <ul class="esab-steps">
                <?php foreach ( $esab_steps as $esab_step ) : ?>
                    <li class="esab-step<?php echo $esab_step['done'] ? ' is-done' : ''; ?>">
                        <span class="esab-step__marker">
                            <?php
                            if ( $esab_step['done'] ) {
                                Esab_Admin::the_icon( 'check', 13 );
                            } else {
                                Esab_Admin::the_icon( 'circle-dashed', 13 );
                            }
                            ?>
                        </span>
                        <span class="esab-step__text">
                            <span class="esab-step__title"><?php echo esc_html( $esab_step['title'] ); ?></span>
                            <span class="esab-step__desc"><?php echo esc_html( $esab_step['desc'] ); ?></span>
                        </span>
                        <?php if ( ! $esab_step['done'] && $esab_step['href'] ) : ?>
                            <a
                                class="esab-step__action"
                                href="<?php echo esc_url( $esab_step['href'] ); ?>"
                                <?php echo ! empty( $esab_step['manual'] ) ? ' data-esab-step="' . esc_attr( $esab_step['id'] ) . '"' : ''; ?>
                                <?php echo $esab_step['external'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
                            >
                                <?php echo esc_html( $esab_step['action'] ); ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="esab-card esab-tutorial">
            <a class="esab-tutorial__thumb" href="<?php echo esc_url( Esab_Admin::link( 'youtube' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Watch the video tutorial on YouTube', 'easy-accordion-block' ); ?>">
                <img src="<?php echo esc_url( ESAB_URL . 'inc/Admin/images/tutorial-thumbnail.jpg' ); ?>" alt="" />
                <span class="esab-tutorial__play"><?php Esab_Admin::the_icon( 'play', 22 ); ?></span>
            </a>
            <div class="esab-tutorial__copy">
                <p class="esab-eyebrow"><?php esc_html_e( 'Video tutorial · 3 min', 'easy-accordion-block' ); ?></p>
                <h2 class="esab-tutorial__title"><?php esc_html_e( 'From empty page to a live FAQ section', 'easy-accordion-block' ); ?></h2>
                <p class="esab-tutorial__desc">
                    <?php esc_html_e( 'Watch how to insert the block, style it to match your theme, and publish an accordion that works on every device.', 'easy-accordion-block' ); ?>
                </p>
                <a class="esab-link" href="<?php echo esc_url( Esab_Admin::link( 'youtube' ) ); ?>" target="_blank" rel="noopener noreferrer">
                    <span><?php esc_html_e( 'Watch on YouTube', 'easy-accordion-block' ); ?></span>
                    <?php Esab_Admin::the_icon( 'arrow-up-right', 15 ); ?>
                </a>
            </div>
        </section>

        <section class="esab-card esab-card--pad esab-included">
            <div class="esab-card__head esab-card__head--split">
                <div class="esab-card__headtext">
                    <h2 class="esab-card__title"><?php esc_html_e( 'What’s included', 'easy-accordion-block' ); ?></h2>
                    <p class="esab-card__subtitle"><?php esc_html_e( 'Every block ships free with the plugin.', 'easy-accordion-block' ); ?></p>
                </div>
                <a class="esab-link esab-link--sm" href="<?php echo esc_url( Esab_Admin::link( 'features' ) ); ?>" target="_blank" rel="noopener noreferrer">
                    <span><?php esc_html_e( 'Browse all features', 'easy-accordion-block' ); ?></span>
                    <?php Esab_Admin::the_icon( 'chevron-right', 15 ); ?>
                </a>
            </div>
            <ul class="esab-grid esab-grid--3">
                <?php foreach ( $esab_features as $esab_feature ) : ?>
                    <li class="esab-tile">
                        <span class="esab-chip"><?php Esab_Admin::the_icon( $esab_feature['icon'], 17 ); ?></span>
                        <span class="esab-tile__title"><?php echo esc_html( $esab_feature['title'] ); ?></span>
                        <span class="esab-tile__desc"><?php echo esc_html( $esab_feature['desc'] ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

    </div>

    <aside class="esab-columns__side">

        <section class="esab-card esab-pro">
            <div class="esab-pro__head">
                <div class="esab-pro__badgerow">
                    <span class="esab-pro__badge">
                        <?php Esab_Admin::the_icon( 'crown', 13 ); ?>
                        <span><?php esc_html_e( 'Pro', 'easy-accordion-block' ); ?></span>
                    </span>
                    <span class="esab-pro__price">
                        <?php
                        if ( $esab_is_pro ) {
                            esc_html_e( 'Active', 'easy-accordion-block' );
                        } else {
                            esc_html_e( 'from $29/yr', 'easy-accordion-block' );
                        }
                        ?>
                    </span>
                </div>
                <h2 class="esab-pro__title">
                    <?php
                    if ( $esab_is_pro ) {
                        esc_html_e( 'The full toolkit is unlocked', 'easy-accordion-block' );
                    } else {
                        esc_html_e( 'Unlock the full toolkit', 'easy-accordion-block' );
                    }
                    ?>
                </h2>
                <p class="esab-pro__desc">
                    <?php
                    if ( $esab_is_pro ) {
                        esc_html_e( 'Thanks for going Pro. Every feature below is available in your editor.', 'easy-accordion-block' );
                    } else {
                        esc_html_e( 'Seven more features for teams building serious content.', 'easy-accordion-block' );
                    }
                    ?>
                </p>
            </div>
            <ul class="esab-pro__features">
                <?php foreach ( $esab_pro_features as $esab_pro_feature ) : ?>
                    <li>
                        <?php Esab_Admin::the_icon( 'check', 15 ); ?>
                        <span><?php echo esc_html( $esab_pro_feature ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="esab-pro__foot">
                <?php if ( $esab_is_pro ) : ?>
                    <a class="esab-btn esab-btn--primary esab-btn--block" href="<?php echo esc_url( Esab_Admin::link( 'demos' ) ); ?>" target="_blank" rel="noopener noreferrer">
                        <span><?php esc_html_e( 'Explore the demos', 'easy-accordion-block' ); ?></span>
                        <?php Esab_Admin::the_icon( 'arrow-right', 16 ); ?>
                    </a>
                <?php else : ?>
                    <a class="esab-btn esab-btn--primary esab-btn--block" href="<?php echo esc_url( Esab_Admin::link( 'pricing' ) ); ?>" target="_blank" rel="noopener noreferrer">
                        <span><?php esc_html_e( 'Get the Pro version', 'easy-accordion-block' ); ?></span>
                        <?php Esab_Admin::the_icon( 'arrow-right', 16 ); ?>
                    </a>
                    <p class="esab-pro__guarantee"><?php esc_html_e( '14-day money-back guarantee', 'easy-accordion-block' ); ?></p>
                <?php endif; ?>
            </div>
        </section>

        <section class="esab-card esab-card--list">
            <h2 class="esab-card__title"><?php esc_html_e( 'Need a hand?', 'easy-accordion-block' ); ?></h2>
            <ul class="esab-linklist">
                <?php foreach ( $esab_help_links as $esab_help ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $esab_help['href'] ); ?>"<?php echo $esab_help['blank'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                            <span class="esab-chip"><?php Esab_Admin::the_icon( $esab_help['icon'], 16 ); ?></span>
                            <span class="esab-linklist__text">
                                <span class="esab-linklist__label"><?php echo esc_html( $esab_help['label'] ); ?></span>
                                <span class="esab-linklist__desc"><?php echo esc_html( $esab_help['desc'] ); ?></span>
                            </span>
                            <?php Esab_Admin::the_icon( 'arrow-up-right', 15, 'esab-linklist__arrow' ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="esab-card esab-card--pad">
            <div class="esab-card__head">
                <h2 class="esab-card__title"><?php esc_html_e( 'More from GutenbergKits', 'easy-accordion-block' ); ?></h2>
                <p class="esab-card__subtitle"><?php esc_html_e( 'Blocks that pair well with this one.', 'easy-accordion-block' ); ?></p>
            </div>
            <ul class="esab-products">
                <?php foreach ( $esab_products as $esab_product ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $esab_product['href'] ); ?>" target="_blank" rel="noopener noreferrer">
                            <span class="esab-chip esab-chip--outline"><?php Esab_Admin::the_icon( $esab_product['icon'], 17 ); ?></span>
                            <span class="esab-products__text">
                                <span class="esab-products__label"><?php echo esc_html( $esab_product['label'] ); ?></span>
                                <span class="esab-products__desc"><?php echo esc_html( $esab_product['desc'] ); ?></span>
                            </span>
                            <?php Esab_Admin::the_icon( 'arrow-up-right', 15, 'esab-products__arrow' ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

    </aside>
</div>
