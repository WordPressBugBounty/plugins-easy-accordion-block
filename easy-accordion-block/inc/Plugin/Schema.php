<?php
/**
 * FAQ / Q&A structured data.
 *
 * When an accordion has "Enable FAQ schema" turned on, its items are
 * collected while the page renders and a single FAQPage JSON-LD block is
 * printed in the footer.
 *
 * This is entirely server-side. The block's saved markup is untouched, so the
 * toggle can be flipped at any time without a block ever becoming invalid.
 *
 * @package Easy_Accordion_Block
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Esab_Schema' ) ) {

    /**
     * Builds and prints the FAQPage graph.
     */
    class Esab_Schema {

        /**
         * Instance of the class.
         *
         * @var Esab_Schema|null
         */
        private static $instance = null;

        /**
         * Collected questions, keyed by a hash of the question text so the same
         * question appearing in nested accordions is only counted once.
         *
         * @var array<string,array{name:string,text:string}>
         */
        private $items = array();

        /**
         * Whether the footer output has already run.
         *
         * @var bool
         */
        private $printed = false;

        /**
         * True while walking an accordion's tree. Rendering a panel can re-enter
         * this filter for a nested accordion; the outer walk already covers it.
         *
         * @var bool
         */
        private $collecting = false;

        /**
         * Longest answer text kept, in characters.
         */
        const MAX_ANSWER = 4000;

        /**
         * Constructor.
         */
        public function __construct() {
            add_filter( 'render_block_esab/accordion', array( $this, 'collect' ), 10, 2 );
            add_action( 'wp_footer', array( $this, 'print_graph' ), 20 );
        }

        /**
         * Pull questions out of an accordion as it renders. The content is
         * returned unchanged.
         *
         * @param string $block_content Rendered block HTML.
         * @param array  $block         Parsed block.
         * @return string
         */
        public function collect( $block_content, $block ) {
            if ( $this->collecting || is_admin() ) {
                return $block_content;
            }

            $attrs = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();

            if ( empty( $attrs['enableFaqSchema'] ) ) {
                return $block_content;
            }

            if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
                $this->collecting = true;
                $this->walk( $block['innerBlocks'] );
                $this->collecting = false;
            }

            return $block_content;
        }

        /**
         * Recurse through inner blocks collecting every accordion child.
         *
         * Nested accordions are descended into as well; the text-hash key keeps
         * duplicates out, and every real FAQ on the page belongs in the graph.
         *
         * @param array $blocks Inner blocks.
         */
        private function walk( $blocks ) {
            foreach ( $blocks as $child ) {
                if ( ! is_array( $child ) || empty( $child['blockName'] ) ) {
                    continue;
                }

                if ( 'esab/accordion-child' === $child['blockName'] ) {
                    $this->add_item( $child );
                }

                if ( ! empty( $child['innerBlocks'] ) && is_array( $child['innerBlocks'] ) ) {
                    $this->walk( $child['innerBlocks'] );
                }
            }
        }

        /**
         * Turn one accordion child into a question/answer pair.
         *
         * @param array $child Parsed esab/accordion-child block.
         */
        private function add_item( $child ) {
            $attrs   = isset( $child['attrs'] ) && is_array( $child['attrs'] ) ? $child['attrs'] : array();
            $heading = isset( $attrs['heading'] ) ? (string) $attrs['heading'] : '';

            $question = $this->plain_text( $heading );

            if ( '' === $question ) {
                return;
            }

            $answer_html = '';
            if ( ! empty( $child['innerBlocks'] ) && is_array( $child['innerBlocks'] ) ) {
                foreach ( $child['innerBlocks'] as $inner ) {
                    $answer_html .= render_block( $inner );
                }
            }

            $answer = $this->plain_text( $answer_html );

            if ( '' === $answer ) {
                return;
            }

            if ( strlen( $answer ) > self::MAX_ANSWER ) {
                $answer = rtrim( substr( $answer, 0, self::MAX_ANSWER ) ) . '…';
            }

            $key = md5( $question );

            if ( ! isset( $this->items[ $key ] ) ) {
                $this->items[ $key ] = array(
                    'name' => $question,
                    'text' => $answer,
                );
            }
        }

        /**
         * Collapse HTML to a single line of readable text.
         *
         * @param string $html Raw HTML or rich-text value.
         * @return string
         */
        private function plain_text( $html ) {
            $text = wp_strip_all_tags( (string) $html );
            $text = html_entity_decode( $text, ENT_QUOTES, get_bloginfo( 'charset' ) );
            $text = preg_replace( '/\s+/u', ' ', $text );

            return trim( (string) $text );
        }

        /**
         * Print the FAQPage graph once, in the footer.
         */
        public function print_graph() {
            if ( $this->printed ) {
                return;
            }

            $this->printed = true;

            if ( empty( $this->items ) ) {
                return;
            }

            /**
             * Allow the schema output to be suppressed, e.g. when an SEO plugin
             * already emits a FAQPage for the same content.
             *
             * @param bool $enabled Whether to print the graph.
             */
            if ( ! apply_filters( 'esab_faq_schema_enabled', true ) ) {
                return;
            }

            $main_entity = array();

            foreach ( $this->items as $item ) {
                $main_entity[] = array(
                    '@type'          => 'Question',
                    'name'           => $item['name'],
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => $item['text'],
                    ),
                );
            }

            $graph = array(
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $main_entity,
            );

            // Default flags keep "/" escaped, so a "</script>" inside answer
            // text cannot terminate the tag early.
            $json = wp_json_encode( $graph, JSON_UNESCAPED_UNICODE );

            if ( false === $json ) {
                return;
            }

            echo "\n<script type=\"application/ld+json\" class=\"esab-faq-schema\">" . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output, slashes escaped.
        }

        /**
         * Instance of the class.
         *
         * @return Esab_Schema
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }

    Esab_Schema::instance();
}
