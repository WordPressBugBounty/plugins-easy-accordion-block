<?php
/**
 * Initialize all necessary classes and functions
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Esab_Style' ) ) {

    class Esab_Style {

        /**
         * Instance of the class
         *
         * @var null
         */
        private static $instance = null;

        /**
         * Whether an accordion block rendered on this request.
         *
         * @var bool
         */
        private $has_rendered = false;

        /**
         * Constructor
         */
        public function __construct() {
            add_filter( 'render_block', [ $this, 'generate_style' ], 10, 2 );
            // editor css
            add_action( 'enqueue_block_editor_assets', [ $this, 'editor_assets' ] );
            // no-JS fallback
            add_action( 'wp_footer', [ $this, 'no_js_fallback' ] );
        }

        /**
         * Panels are hidden in CSS and only revealed by the view script, so with
         * JavaScript unavailable every answer would be invisible. Emitted only
         * when a block actually rendered, and only read by browsers with
         * scripting off — so it costs nothing for everyone else.
         *
         * @since 1.5.2
         * @return void
         */
        public function no_js_fallback() {
            if ( ! $this->has_rendered ) {
                return;
            }

            echo '<noscript><style>.wp-block-esab-accordion .esab__body,.wp-block-esab-faqs .esab__body{display:block !important}</style></noscript>';
        }

        /**
         * Generate Style
         * 
         * @return void
         */
        public function generate_style( $block_content, $block ) {

            if ( isset( $block['blockName'] ) && str_contains( $block['blockName'], 'esab/' ) ) {

                $this->has_rendered = true;

                // Trigger the custom action for rendering the block.
                do_action( 'esab_render_block', $block );

                $attrs = $block['attrs'] ?? [];
            
                // Early return if attributes are empty.
                if ( empty( $attrs ) ) {
                    return $block_content;
                }
            
                // Get the unique ID safely. It becomes a class selector, so anything
                // beyond a plain identifier is rejected.
                $unique_id = $attrs['uniqueId'] ?? '';
                if ( ! is_string( $unique_id ) || ! preg_match( '/^[A-Za-z0-9_-]+$/', $unique_id ) ) {
                    return $block_content;
                }
            
                $block_style = $attrs['blockStyle'] ?? '';

                if ( ! empty( $block_style ) ) {
                    $handle = 'esab-accordion-style-' . $unique_id;
                    $this->render_inline_css( $handle, $block_style, $unique_id );
                    return $block_content;
                }
            
                // Define default attributes.
                $defaults = [
                    'accordionsGap'              => 10,
                    'marginTop'                  => '0',
                    'marginBottom'               => '0',
                    'marginLeft'                 => '0',
                    'marginRight'                => '0',
                    'accordionBorderWidth'       => 1,
                    'accordionBorderStyle'       => 'solid',
                    'accordionBorderColor'       => '#E0E0E0',
                    'accordionActiveBorderColor' => '#3fa796',
                    'accordionBorderRadius'      => '3',
                    'headingActiveColor'         => '#3fa796',
                    'headerActiveBg'             => '#f0fffd',
                    'headerTopPadding'           => '10',
                    'headerBottomPadding'        => '10',
                    'headerLeftPadding'          => '10',
                    'headerRightPadding'         => '10',
                    'linkedHeaderPadding'        => '10',
                    'enableLinkedHeaderPadding'  => false,
                    'iconSize'                   => 20,
                    'activeIconColor'            => '#3fa796',
                    'bodyActiveBg'               => '#f0fffd',
                    'bodyTopPadding'             => '10',
                    'bodyBottomPadding'          => '10',
                    'bodyLeftPadding'            => '10',
                    'bodyRightPadding'           => '10',
                    'linkedBodyPadding'          => '10',
                    'enableLinkedBodyPadding'    => false,
                    'inactiveIconColor'          => 'inherit',  // Added to ensure it's always defined.
                    'zindex'                     => null,       // Added to prevent undefined errors.
                ];
            
                // Merge attributes with defaults.
                $attributes = wp_parse_args( $attrs, $defaults );
            
                // Start building custom CSS.
                $custom_css = '';
                $prefix = '.' . $unique_id;
            
                // Handle z-index if set.
                if ( isset( $attributes['zindex'] ) ) {
                    $custom_css .= "{$prefix} { z-index: {$attributes['zindex']}; }";
                }
            
                // Accordion styles.
                if ( isset( $attributes['accordionBorderRadius'] ) ) {
                    $custom_css .= "{$prefix} .wp-block-esab-accordion-child { border-radius: {$attributes['accordionBorderRadius']}px; }";
                }
                if ( isset( $attributes['accordionActiveBorderColor'] ) ) {
                    $custom_css .= "{$prefix} .wp-block-esab-accordion-child.esab__active_accordion { border-color: {$attributes['accordionActiveBorderColor']} !important; }";
                }
                if ( isset( $attributes['headerActiveBg'] ) ) {
                    $custom_css .= "{$prefix} .wp-block-esab-accordion-child.esab__active_accordion .esab__head { background: {$attributes['headerActiveBg']} !important; }";
                }
                if ( isset( $attributes['headingActiveColor'] ) ) {
                    $custom_css .= "{$prefix} .wp-block-esab-accordion-child.esab__active_accordion .esab__heading_tag { color: {$attributes['headingActiveColor']} !important; }";
                }
                if ( isset( $attributes['bodyActiveBg'] ) ) {
                    $custom_css .= "{$prefix} .wp-block-esab-accordion-child.esab__active_accordion .esab__body { background-color: {$attributes['bodyActiveBg']} !important; }";
                }
            
                // Icon styles.
                $icon_size = isset( $attributes['iconSize'] ) ? $attributes['iconSize'] : 20;
                $inactive_icon_color = isset( $attributes['inactiveIconColor'] ) ? $attributes['inactiveIconColor'] : 'inherit';
                $active_icon_color = isset( $attributes['activeIconColor'] ) ? $attributes['activeIconColor'] : 'inherit';
            
                $custom_css .= "{$prefix} .esab__collapse svg { width: {$icon_size}px; fill: {$inactive_icon_color}; }";
                $custom_css .= "{$prefix} .esab__expand svg { width: {$icon_size}px; fill: {$active_icon_color}; }";
            
                // Render the inline CSS.
                $handle = 'esab-accordion-style-' . $unique_id;
                $this->render_inline_css( $handle, $custom_css, $unique_id );
            }
        
            return $block_content;
        }
        /**
         * Render Inline CSS
        */
        public function render_inline_css( $handle, $css, $unique_id = '' ) {
            // A block can render more than once per request (e.g. FAQ schema
            // renders answers again); add its CSS only once.
            if ( ! is_string( $css ) || wp_style_is( $handle, 'enqueued' ) ) {
                return;
            }
            wp_register_style( $handle, false, array(), ESAB_VERSION, 'all' );
            wp_enqueue_style( $handle, false, array(), ESAB_VERSION, 'all' );
            wp_add_inline_style( $handle, $this->sanitize_css( $css, $unique_id ) );
        }

        /**
         * Sanitize CSS method
         *
         * @since 1.0.0
         * @param string $css       CSS to Sanitize.
         * @param string $unique_id Block id every selector must be scoped to.
         * @return string
         */
        private function sanitize_css( $css, $unique_id = '' ) {
            // Validate UTF-8 encoding
            $css = wp_check_invalid_utf8( $css );

            if ( empty( $css ) ) {
                return '';
            }

            // Remove CSS comments (can hide payloads: /* expression */background:url() */)
            $css = preg_replace( '!/\*.*?\*/!s', '', $css );

            // Normalize whitespace to prevent obfuscation tricks
            $css = preg_replace( '/\s+/', ' ', $css );

            // Remove backslash escapes used to bypass keyword filters (e.g. \65 xpression)
            $css = preg_replace( '/\\\\[0-9a-fA-F]{0,6}\s?/', '', $css );

            // Markup that could escape the <style> context is the one case where
            // rejecting everything is right — it is never a styling mistake.
            if ( preg_match( '/<\s*\/?\s*(script|style|link|meta|object|embed|iframe)/i', $css ) ) {
                return '';
            }

            // Rebuild the stylesheet rule by rule. Only rules scoped to this
            // block survive, so a block cannot restyle the rest of the page, and
            // at-rules other than conditional groups (@import, @font-face, ...)
            // are dropped whether or not they end in a semicolon.
            $css = $this->sanitize_rules( $css, $unique_id );

            // Trim and return
            return trim( $css );
        }

        /**
         * Keep only well-formed rules scoped to the block.
         *
         * @since 1.6.0
         * @param string $css       Stylesheet.
         * @param string $unique_id Block id selectors must start with.
         * @return string
         */
        private function sanitize_rules( $css, $unique_id ) {
            $out = '';
            $len = strlen( $css );
            $pos = 0;

            while ( $pos < $len ) {
                $open = strpos( $css, '{', $pos );
                if ( false === $open ) {
                    break; // Trailing text outside any rule (e.g. a bare @import).
                }

                $prelude = trim( substr( $css, $pos, $open - $pos ) );
                $close   = $this->matching_brace( $css, $open );
                if ( false === $close ) {
                    break; // Unbalanced: drop the rest.
                }
                $body = substr( $css, $open + 1, $close - $open - 1 );
                $pos  = $close + 1;

                // A stray "}" or text before the prelude (e.g. "@import x;") is
                // carried in $prelude; keep only what follows the last ";" or "}".
                $prelude = trim( preg_replace( '/^.*[;}]/s', '', $prelude ) );

                if ( '' === $prelude ) {
                    continue;
                }

                if ( '@' === $prelude[0] ) {
                    if ( preg_match( '/^@(media|supports|container)\b/i', $prelude ) && ! preg_match( '/url\s*\(/i', $prelude ) ) {
                        $inner = $this->sanitize_rules( $body, $unique_id );
                        if ( '' !== $inner ) {
                            $out .= $prelude . '{' . $inner . '}';
                        }
                    }
                    continue;
                }

                if ( ! $this->is_scoped_selector( $prelude, $unique_id ) ) {
                    continue;
                }

                $declarations = $this->sanitize_declarations( $body );
                if ( '' !== $declarations ) {
                    $out .= $prelude . '{' . $declarations . '}';
                }
            }

            return $out;
        }

        /**
         * Position of the brace closing the one at $open, or false.
         *
         * @since 1.6.0
         * @param string $css  Stylesheet.
         * @param int    $open Offset of an opening brace.
         * @return int|false
         */
        private function matching_brace( $css, $open ) {
            $depth = 0;
            $len   = strlen( $css );
            for ( $i = $open; $i < $len; $i++ ) {
                if ( '{' === $css[ $i ] ) {
                    ++$depth;
                } elseif ( '}' === $css[ $i ] ) {
                    --$depth;
                    if ( 0 === $depth ) {
                        return $i;
                    }
                }
            }
            return false;
        }

        /**
         * Whether every selector in a list targets this block.
         *
         * @since 1.6.0
         * @param string $selectors Comma-separated selector list.
         * @param string $unique_id Block id.
         * @return bool
         */
        private function is_scoped_selector( $selectors, $unique_id ) {
            if ( '' === $unique_id ) {
                return false;
            }

            $scope = '/^\.' . preg_quote( $unique_id, '/' ) . '(?![A-Za-z0-9_-])/';

            foreach ( explode( ',', $selectors ) as $selector ) {
                if ( ! preg_match( $scope, trim( $selector ) ) ) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Filter a single declaration block, keeping everything that is safe.
         *
         * @since 1.5.2
         * @param string $declarations Contents between braces.
         * @return string
         */
        private function sanitize_declarations( $declarations ) {
            $kept = array();

            foreach ( $this->split_declarations( $declarations ) as $declaration ) {
                $declaration = trim( $declaration );

                if ( '' === $declaration ) {
                    continue;
                }

                if ( $this->is_allowed_declaration( $declaration ) ) {
                    $kept[] = $declaration;
                }
            }

            return empty( $kept ) ? '' : implode( ';', $kept ) . ';';
        }

        /**
         * Split on semicolons that actually terminate a declaration.
         *
         * A naive explode() would break on semicolons inside quoted strings or
         * url() values.
         *
         * @since 1.5.2
         * @param string $declarations Contents between braces.
         * @return array
         */
        private function split_declarations( $declarations ) {
            $parts   = array();
            $buffer  = '';
            $quote   = '';
            $depth   = 0;
            $length  = strlen( $declarations );

            for ( $i = 0; $i < $length; $i++ ) {
                $char = $declarations[ $i ];

                if ( '' !== $quote ) {
                    $buffer .= $char;
                    if ( $char === $quote && ( $i === 0 || '\\' !== $declarations[ $i - 1 ] ) ) {
                        $quote = '';
                    }
                    continue;
                }

                if ( '"' === $char || "'" === $char ) {
                    $quote   = $char;
                    $buffer .= $char;
                    continue;
                }

                if ( '(' === $char ) {
                    $depth++;
                } elseif ( ')' === $char && $depth > 0 ) {
                    $depth--;
                }

                if ( ';' === $char && 0 === $depth ) {
                    $parts[] = $buffer;
                    $buffer  = '';
                    continue;
                }

                $buffer .= $char;
            }

            if ( '' !== trim( $buffer ) ) {
                $parts[] = $buffer;
            }

            return $parts;
        }

        /**
         * Decide whether one declaration may be emitted.
         *
         * @since 1.5.2
         * @param string $declaration A single "property: value" pair.
         * @return bool
         */
        private function is_allowed_declaration( $declaration ) {
            if ( false === strpos( $declaration, ':' ) ) {
                return false;
            }

            // Script execution vectors, wherever they appear in the declaration.
            if ( preg_match( '/expression\s*\(|javascript\s*:|vbscript\s*:|behavior\s*:|-moz-binding\s*:/i', $declaration ) ) {
                return false;
            }

            list( $property, $value ) = explode( ':', $declaration, 2 );

            $property = strtolower( trim( $property ) );
            $value    = trim( $value );

            if ( '' === $value ) {
                return false;
            }

            // Property must be a plain identifier (custom properties allowed).
            if ( ! preg_match( '/^-{0,2}[a-z][a-z0-9-]*$/', $property ) ) {
                return false;
            }

            // url() is permitted, but only for http(s), protocol-relative and
            // site-relative targets — never data: or any other scheme.
            if ( preg_match_all( '/url\s*\(\s*([\'"]?)(.*?)\1\s*\)/i', $value, $urls, PREG_SET_ORDER ) ) {
                foreach ( $urls as $url ) {
                    $target = trim( $url[2] );

                    if ( '' === $target ) {
                        return false;
                    }

                    // Absolute or protocol-relative.
                    if ( preg_match( '#^(https?:)?//#i', $target ) ) {
                        continue;
                    }

                    // Anything else carrying a scheme (data:, javascript:, ...).
                    if ( preg_match( '/^[a-z][a-z0-9+.-]*:/i', $target ) ) {
                        return false;
                    }
                }
            }

            // content: is legitimate on pseudo-elements but is also the classic
            // way to smuggle a payload, so restrict it to inert values.
            if ( 'content' === $property ) {
                return (bool) preg_match(
                    '/^(\'[^\']*\'|"[^"]*"|none|normal|inherit|initial|unset|revert|counter\([^()]*\)|attr\([^()]*\))$/i',
                    $value
                );
            }

            return true;
        }

        /**
         * Editor Assets
         * 
         * @return void
         */
        public function editor_assets() {
            wp_enqueue_style( 'esab-accordion-editor', ESAB_URL . 'css/editor.css', [], ESAB_VERSION );
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

    Esab_Style::instance(); // Initialize the class

}