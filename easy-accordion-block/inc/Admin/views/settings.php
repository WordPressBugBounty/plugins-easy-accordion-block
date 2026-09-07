<?php
/**
 * Settings tab — feature toggles.
 *
 * @package Easy_Accordion_Block
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$esab_settings = Esab_Settings::all();

$esab_toggles = [
    [
        'key'   => 'a11y_enhanced',
        'icon'  => 'keyboard',
        'title' => __( 'Enhanced keyboard navigation', 'easy-accordion-block' ),
        'desc'  => __( 'Arrow-key / Home / End movement between headers and a visible focus ring, following the WAI-ARIA Accordion pattern. Screen-reader labelling stays on whether or not this is enabled.', 'easy-accordion-block' ),
    ],
];
?>

<section class="esab-card esab-card--pad esab-settings" data-esab-settings>
    <div class="esab-card__head">
        <h2 class="esab-card__title"><?php esc_html_e( 'Feature control', 'easy-accordion-block' ); ?></h2>
        <p class="esab-card__subtitle">
            <?php esc_html_e( 'Turn individual features on or off for the whole site. Changes save automatically and never affect content you have already published.', 'easy-accordion-block' ); ?>
        </p>
    </div>

    <ul class="esab-toggles">
        <?php foreach ( $esab_toggles as $esab_toggle ) : ?>
            <?php $esab_on = ! empty( $esab_settings[ $esab_toggle['key'] ] ); ?>
            <li class="esab-toggle">
                <span class="esab-chip"><?php Esab_Admin::the_icon( $esab_toggle['icon'], 17 ); ?></span>
                <span class="esab-toggle__text">
                    <span class="esab-toggle__title"><?php echo esc_html( $esab_toggle['title'] ); ?></span>
                    <span class="esab-toggle__desc"><?php echo esc_html( $esab_toggle['desc'] ); ?></span>
                    <span class="esab-toggle__status" aria-live="polite"></span>
                </span>
                <label class="esab-switch">
                    <input
                        type="checkbox"
                        class="esab-switch__input"
                        data-esab-toggle="<?php echo esc_attr( $esab_toggle['key'] ); ?>"
                        <?php checked( $esab_on ); ?>
                    />
                    <span class="esab-switch__track" aria-hidden="true"><span class="esab-switch__thumb"></span></span>
                    <span class="screen-reader-text"><?php echo esc_html( $esab_toggle['title'] ); ?></span>
                </label>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
