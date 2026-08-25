<?php
/**
 * Help & Support tab.
 *
 * @package Easy_Accordion_Block
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$esab_is_pro = Esab_Admin::is_pro();

$esab_channels = [
    [
        'icon'   => 'ticket',
        'tone'   => 'brand',
        'title'  => __( 'Create Support Ticket', 'easy-accordion-block' ),
        'desc'   => __( 'Create a support ticket to run one to one conversation.', 'easy-accordion-block' ),
        'button' => __( 'Create Ticket', 'easy-accordion-block' ),
        'href'   => Esab_Admin::link( 'ticket' ),
    ],
    [
        'icon'   => 'message-circle',
        'tone'   => 'whatsapp',
        'title'  => __( 'WhatsApp Group', 'easy-accordion-block' ),
        'desc'   => __( 'Join our WhatsApp Group to get instant reply for your questions.', 'easy-accordion-block' ),
        'button' => __( 'Join Now', 'easy-accordion-block' ),
        'href'   => Esab_Admin::link( 'whatsapp' ),
    ],
    [
        'icon'   => 'send',
        'tone'   => 'telegram',
        'title'  => __( 'Telegram Group', 'easy-accordion-block' ),
        'desc'   => __( 'Send a direct message to us so that we can respond within a short time.', 'easy-accordion-block' ),
        'button' => __( 'Join Now', 'easy-accordion-block' ),
        'href'   => Esab_Admin::link( 'telegram' ),
    ],
    [
        'icon'   => 'mail',
        'tone'   => 'mail',
        'title'  => __( 'Direct Mail', 'easy-accordion-block' ),
        'desc'   => __( 'Send a direct email to us so that we can respond within a short time.', 'easy-accordion-block' ),
        'button' => __( 'Send Email', 'easy-accordion-block' ),
        'href'   => Esab_Admin::link( 'email' ),
    ],
];

$esab_faqs = [
    [
        'q' => __( 'Can I use any content inside an accordion?', 'easy-accordion-block' ),
        'a' => __( 'Yes. Each accordion panel is a full block canvas, so you can nest paragraphs, images, columns, embeds — anything Gutenberg supports.', 'easy-accordion-block' ),
    ],
    [
        'q' => __( 'Can I keep an accordion item open on page load?', 'easy-accordion-block' ),
        'a' => __( 'Yes. Enable the initially-open option on any item and it renders expanded on the first paint, with no layout shift.', 'easy-accordion-block' ),
    ],
    [
        'q' => __( 'Does it support nested accordions?', 'easy-accordion-block' ),
        'a' => __( 'Yes. Add another Easy Accordion block inside a panel and nest as deep as your layout needs. Each level keeps its own settings.', 'easy-accordion-block' ),
    ],
    [
        'q' => __( 'Will it match my theme’s color palette?', 'easy-accordion-block' ),
        'a' => __( 'By default the block inherits your theme’s colors and typography. You can override any of it — colors, spacing, borders, icons — from the block sidebar.', 'easy-accordion-block' ),
    ],
    [
        'q' => __( 'Does it work with page builders?', 'easy-accordion-block' ),
        'a' => __( 'It is a native Gutenberg block, so it works anywhere the block editor runs, including page builders that can embed native blocks.', 'easy-accordion-block' ),
    ],
];

$esab_checks = [
    [
        'title' => __( 'Clear your cache', 'easy-accordion-block' ),
        'desc'  => __( 'Purge both the plugin cache and any CDN layer, then reload.', 'easy-accordion-block' ),
    ],
    [
        'title' => __( 'Test with a default theme', 'easy-accordion-block' ),
        'desc'  => __( 'Switch to Twenty Twenty-Five to rule out theme conflicts.', 'easy-accordion-block' ),
    ],
    [
        'title' => __( 'Disable other plugins', 'easy-accordion-block' ),
        'desc'  => __( 'Deactivate in batches to find a conflicting plugin fast.', 'easy-accordion-block' ),
    ],
];

$esab_other = [
    [
        'icon'  => 'messages-square',
        'label' => __( 'WordPress.org forum', 'easy-accordion-block' ),
        'desc'  => __( 'Public support threads', 'easy-accordion-block' ),
        'href'  => Esab_Admin::link( 'forum' ),
    ],
    [
        'icon'  => 'lightbulb',
        'label' => __( 'Feature requests', 'easy-accordion-block' ),
        'desc'  => __( 'Vote on the roadmap', 'easy-accordion-block' ),
        'href'  => Esab_Admin::link( 'roadmap' ),
    ],
    [
        'icon'  => 'star',
        'label' => __( 'Rate the plugin', 'easy-accordion-block' ),
        'desc'  => __( 'Leave a 5-star review', 'easy-accordion-block' ),
        'href'  => Esab_Admin::link( 'review' ),
    ],
    [
        'icon'  => 'history',
        'label' => __( 'Changelog', 'easy-accordion-block' ),
        'desc'  => __( 'See what shipped recently', 'easy-accordion-block' ),
        'href'  => Esab_Admin::link( 'changelog' ),
    ],
];

$esab_report = Esab_Admin::system_report();

$esab_report_text = [];
foreach ( $esab_report as $esab_row ) {
    $esab_report_text[] = $esab_row['key'] . ': ' . $esab_row['value'];
}
$esab_report_text[] = __( 'Site URL', 'easy-accordion-block' ) . ': ' . home_url();
?>

<section class="esab-hero">
    <h1 class="esab-hero__title"><?php esc_html_e( 'How can we help?', 'easy-accordion-block' ); ?></h1>
    <p class="esab-hero__subtitle">
        <?php esc_html_e( 'Pick a support channel below, or check the questions we get asked most — we answer every ticket within one business day.', 'easy-accordion-block' ); ?>
    </p>
</section>

<div class="esab-columns">
    <div class="esab-columns__main">

        <section class="esab-card esab-card--pad">
            <div class="esab-card__head esab-card__head--split">
                <div class="esab-card__headtext">
                    <h2 class="esab-card__title"><?php esc_html_e( 'Get instant support', 'easy-accordion-block' ); ?></h2>
                    <p class="esab-card__subtitle"><?php esc_html_e( 'Four ways to reach us — pick whichever is fastest for you.', 'easy-accordion-block' ); ?></p>
                </div>
                <a class="esab-link esab-link--sm" href="<?php echo esc_url( Esab_Admin::link( 'support' ) ); ?>" target="_blank" rel="noopener noreferrer">
                    <span><?php esc_html_e( 'Visit support page', 'easy-accordion-block' ); ?></span>
                    <?php Esab_Admin::the_icon( 'chevron-right', 15 ); ?>
                </a>
            </div>
            <ul class="esab-grid esab-grid--2">
                <?php foreach ( $esab_channels as $esab_channel ) : ?>
                    <li class="esab-tile esab-tile--center">
                        <span class="esab-chip esab-chip--lg esab-chip--<?php echo esc_attr( $esab_channel['tone'] ); ?>">
                            <?php Esab_Admin::the_icon( $esab_channel['icon'], 23 ); ?>
                        </span>
                        <span class="esab-tile__title esab-tile__title--lg"><?php echo esc_html( $esab_channel['title'] ); ?></span>
                        <span class="esab-tile__desc"><?php echo esc_html( $esab_channel['desc'] ); ?></span>
                        <a class="esab-btn esab-btn--<?php echo esc_attr( $esab_channel['tone'] ); ?>" href="<?php echo esc_url( $esab_channel['href'] ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html( $esab_channel['button'] ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="esab-card esab-faq">
            <div class="esab-card__head esab-card__head--inset">
                <h2 class="esab-card__title"><?php esc_html_e( 'Frequently asked questions', 'easy-accordion-block' ); ?></h2>
                <p class="esab-card__subtitle"><?php esc_html_e( 'The questions we get most often from plugin users.', 'easy-accordion-block' ); ?></p>
            </div>
            <ul class="esab-faq__list" data-esab-accordion>
                <?php foreach ( $esab_faqs as $esab_i => $esab_faq ) : ?>
                    <li class="esab-faq__item<?php echo 0 === $esab_i ? ' is-open' : ''; ?>">
                        <button type="button" class="esab-faq__q" aria-expanded="<?php echo 0 === $esab_i ? 'true' : 'false'; ?>">
                            <span><?php echo esc_html( $esab_faq['q'] ); ?></span>
                            <span class="esab-faq__toggle">
                                <?php Esab_Admin::the_icon( 'minus', 13, 'esab-icon--minus' ); ?>
                                <?php Esab_Admin::the_icon( 'plus', 13, 'esab-icon--plus' ); ?>
                            </span>
                        </button>
                        <div class="esab-faq__panel">
                            <p class="esab-faq__a"><?php echo esc_html( $esab_faq['a'] ); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="esab-troubleshoot">
            <div class="esab-troubleshoot__head">
                <?php Esab_Admin::the_icon( 'triangle-alert', 18 ); ?>
                <div class="esab-troubleshoot__headtext">
                    <h2 class="esab-troubleshoot__title"><?php esc_html_e( 'Before you open a ticket', 'easy-accordion-block' ); ?></h2>
                    <p class="esab-troubleshoot__subtitle"><?php esc_html_e( 'These three checks resolve most reported issues in under a minute.', 'easy-accordion-block' ); ?></p>
                </div>
            </div>
            <ol class="esab-troubleshoot__list">
                <?php foreach ( $esab_checks as $esab_i => $esab_check ) : ?>
                    <li class="esab-troubleshoot__item">
                        <span class="esab-troubleshoot__num"><?php echo esc_html( number_format_i18n( $esab_i + 1 ) ); ?></span>
                        <span class="esab-troubleshoot__itemtitle"><?php echo esc_html( $esab_check['title'] ); ?></span>
                        <span class="esab-troubleshoot__itemdesc"><?php echo esc_html( $esab_check['desc'] ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>

    </div>

    <aside class="esab-columns__side">

        <section class="esab-priority">
            <h2 class="esab-priority__title">
                <?php
                if ( $esab_is_pro ) {
                    esc_html_e( 'Priority support is active', 'easy-accordion-block' );
                } else {
                    esc_html_e( 'Priority support with Pro', 'easy-accordion-block' );
                }
                ?>
            </h2>
            <p class="esab-priority__desc">
                <?php
                if ( $esab_is_pro ) {
                    esc_html_e( 'Your licence skips the queue — every ticket goes straight to a named engineer.', 'easy-accordion-block' );
                } else {
                    esc_html_e( 'Pro licence holders skip the queue and get a named engineer on every ticket.', 'easy-accordion-block' );
                }
                ?>
            </p>
            <p class="esab-priority__sla">
                <?php Esab_Admin::the_icon( 'zap', 15 ); ?>
                <span><?php esc_html_e( 'Pro first reply: under 2 hours', 'easy-accordion-block' ); ?></span>
            </p>
            <?php if ( $esab_is_pro ) : ?>
                <a class="esab-btn esab-btn--primary esab-btn--block" href="<?php echo esc_url( Esab_Admin::link( 'ticket' ) ); ?>" target="_blank" rel="noopener noreferrer">
                    <?php Esab_Admin::the_icon( 'ticket', 16 ); ?>
                    <span><?php esc_html_e( 'Open a priority ticket', 'easy-accordion-block' ); ?></span>
                </a>
            <?php else : ?>
                <a class="esab-btn esab-btn--primary esab-btn--block" href="<?php echo esc_url( Esab_Admin::link( 'pricing' ) ); ?>" target="_blank" rel="noopener noreferrer">
                    <?php Esab_Admin::the_icon( 'crown', 16 ); ?>
                    <span><?php esc_html_e( 'Upgrade to Pro', 'easy-accordion-block' ); ?></span>
                </a>
            <?php endif; ?>
        </section>

        <section class="esab-card esab-card--list">
            <h2 class="esab-card__title"><?php esc_html_e( 'Other ways to reach us', 'easy-accordion-block' ); ?></h2>
            <ul class="esab-linklist">
                <?php foreach ( $esab_other as $esab_link ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $esab_link['href'] ); ?>" target="_blank" rel="noopener noreferrer">
                            <span class="esab-chip"><?php Esab_Admin::the_icon( $esab_link['icon'], 16 ); ?></span>
                            <span class="esab-linklist__text">
                                <span class="esab-linklist__label"><?php echo esc_html( $esab_link['label'] ); ?></span>
                                <span class="esab-linklist__desc"><?php echo esc_html( $esab_link['desc'] ); ?></span>
                            </span>
                            <?php Esab_Admin::the_icon( 'arrow-up-right', 15, 'esab-linklist__arrow' ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="esab-card esab-card--pad esab-report">
            <div class="esab-card__head">
                <h2 class="esab-card__title"><?php esc_html_e( 'System report', 'easy-accordion-block' ); ?></h2>
                <p class="esab-card__subtitle"><?php esc_html_e( 'Attach this to your ticket so we can skip the back-and-forth.', 'easy-accordion-block' ); ?></p>
            </div>
            <ul class="esab-report__list">
                <?php foreach ( $esab_report as $esab_row ) : ?>
                    <li>
                        <span class="esab-report__key"><?php echo esc_html( $esab_row['key'] ); ?></span>
                        <span class="esab-report__value<?php echo $esab_row['ok'] ? '' : ' is-warning'; ?>">
                            <span><?php echo esc_html( $esab_row['value'] ); ?></span>
                            <?php Esab_Admin::the_icon( $esab_row['ok'] ? 'circle-check' : 'triangle-alert', 14 ); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="esab-btn esab-btn--ghost esab-btn--block" data-esab-copy="<?php echo esc_attr( implode( "\n", $esab_report_text ) ); ?>">
                <?php Esab_Admin::the_icon( 'copy', 15 ); ?>
                <span><?php esc_html_e( 'Copy system report', 'easy-accordion-block' ); ?></span>
            </button>
        </section>

    </aside>
</div>
