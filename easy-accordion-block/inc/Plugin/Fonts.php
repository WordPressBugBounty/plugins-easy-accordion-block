<?php
/**
 * Load Google Fonts
 *
 * @package Easy_Accordion_Block
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Esab_Fonts' ) ) {

	/**
	 * Load Google Fonts
	 *
	 * @since 1.0.0
	 */
	class Esab_Fonts {

		/**
		 * Instance of the class
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Collected families, keyed by family name, each holding the set of
		 * weights actually used on the page.
		 *
		 * @var array
		 */
		private static $all_fonts = array();

		/**
		 * Handles already enqueued this request.
		 *
		 * @var array
		 */
		private static $loaded = array();

		/**
		 * Families the plugin never requests remotely.
		 *
		 * @var array
		 */
		private static $system = array(
			'Default',
			'Arial',
			'Tahoma',
			'Verdana',
			'Helvetica',
			'Times New Roman',
			'Trebuchet MS',
			'Georgia',
		);

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct() {
			// Collect before wp_head runs. The render_block pass below fires from
			// inside the_content, by which point wp_head has already printed and
			// the stylesheet would be stranded in the footer.
			add_action( 'wp_enqueue_scripts', array( $this, 'collect_from_queried_post' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'fonts_loader' ), 10 );

			// Fallback for content the early pass cannot see: archives, widgets,
			// block-theme template parts.
			add_action( 'esab_render_block', array( $this, 'font_generator' ) );

			add_filter( 'wp_resource_hints', array( $this, 'resource_hints' ), 10, 2 );
		}

		/**
		 * Whether remote font loading is switched off.
		 *
		 * @since 1.5.2
		 * @return bool
		 */
		public function is_disabled() {
			/**
			 * Filter to disable Google Fonts entirely.
			 *
			 * @since 1.5.2
			 * @param bool $disabled Whether to skip remote font requests.
			 */
			return (bool) apply_filters( 'esab_disable_google_fonts', get_option( 'esab_disable_google_fonts', false ) );
		}

		/**
		 * Parse the queried post up front so fonts land in <head>.
		 *
		 * @since 1.5.2
		 * @return void
		 */
		public function collect_from_queried_post() {
			if ( $this->is_disabled() ) {
				return;
			}

			$post = get_post();

			if ( $post instanceof WP_Post && ! empty( $post->post_content ) && has_blocks( $post->post_content ) ) {
				$this->scan_blocks( parse_blocks( $post->post_content ) );
			}

			$this->fonts_loader();
		}

		/**
		 * Walk a parsed block tree collecting font settings.
		 *
		 * @since 1.5.2
		 * @param array $blocks Parsed blocks.
		 * @return void
		 */
		private function scan_blocks( $blocks ) {
			if ( ! is_array( $blocks ) ) {
				return;
			}

			foreach ( $blocks as $block ) {
				if ( ! empty( $block['blockName'] ) && str_contains( $block['blockName'], 'esab/' ) && ! empty( $block['attrs'] ) ) {
					$this->collect( $block['attrs'] );
				}

				if ( ! empty( $block['innerBlocks'] ) ) {
					$this->scan_blocks( $block['innerBlocks'] );
				}
			}
		}

		/**
		 * Font generator
		 *
		 * @param array $block Block attributes.
		 * @return void
		 */
		public function font_generator( $block ) {
			if ( $this->is_disabled() ) {
				return;
			}

			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$this->collect( $block['attrs'] );
				$this->fonts_loader();
			}
		}

		/**
		 * Pull families and their weights out of a block's attributes.
		 *
		 * Typography attributes are generated in pairs — esab_<name>FontFamily
		 * holds the family, esab_<name>FontWeight an object whose `value` key
		 * holds the weight.
		 *
		 * @since 1.5.2
		 * @param array $attributes Block attributes.
		 * @return void
		 */
		private function collect( $attributes ) {
			foreach ( $attributes as $key => $value ) {
				if ( empty( $value ) || 0 !== strpos( $key, 'esab_' ) || false === strpos( $key, 'FontFamily' ) ) {
					continue;
				}

				$family = trim( (string) $value );

				if ( '' === $family || in_array( $family, self::$system, true ) ) {
					continue;
				}

				if ( ! isset( self::$all_fonts[ $family ] ) ) {
					self::$all_fonts[ $family ] = array();
				}

				$weight_key = str_replace( 'FontFamily', 'FontWeight', $key );

				if ( ! empty( $attributes[ $weight_key ]['value'] ) ) {
					$weight = preg_replace( '/[^0-9]/', '', (string) $attributes[ $weight_key ]['value'] );

					if ( '' !== $weight ) {
						self::$all_fonts[ $family ][ $weight ] = true;
					}
				}
			}
		}

		/**
		 * Load fonts
		 *
		 * @return void
		 */
		public function fonts_loader() {
			if ( $this->is_disabled() || empty( self::$all_fonts ) ) {
				return;
			}

			foreach ( self::$all_fonts as $family => $weights ) {
				$handle = 'esab-font-' . sanitize_title( $family );

				if ( isset( self::$loaded[ $handle ] ) ) {
					continue;
				}

				$name = str_replace( ' ', '+', $family );

				// Request only the weights in use. Every family used to be
				// fetched at all nine weights, which is several hundred KB of
				// font data nobody asked for.
				if ( ! empty( $weights ) ) {
					$list = array_keys( $weights );
					sort( $list, SORT_NUMERIC );
					$name .= ':' . implode( ',', $list );
				}

				$url = add_query_arg(
					array(
						'family'  => $name,
						'display' => 'swap',
					),
					'https://fonts.googleapis.com/css'
				);

				wp_register_style( $handle, $url, array(), ESAB_VERSION, 'all' );
				wp_enqueue_style( $handle );

				self::$loaded[ $handle ] = true;
			}
		}

		/**
		 * Warm up the font connections when we know we will use them.
		 *
		 * @since 1.5.2
		 * @param array  $hints Current hints.
		 * @param string $relation Hint relation type.
		 * @return array
		 */
		public function resource_hints( $hints, $relation ) {
			if ( 'preconnect' !== $relation || $this->is_disabled() || empty( self::$all_fonts ) ) {
				return $hints;
			}

			$hints[] = 'https://fonts.googleapis.com';
			$hints[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);

			return $hints;
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

	Esab_Fonts::instance(); // Initialize the class.
}
