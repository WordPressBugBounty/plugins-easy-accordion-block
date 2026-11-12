<?php
/**
 * Load Google Fonts
 *
 * Fonts used by the plugin's blocks are requested in a single stylesheet,
 * limited to the weights and styles actually used. By default the font files
 * are then cached in uploads/esab-fonts and served from the site itself, so
 * visitors never connect to Google (faster on HTTP/2, and GDPR friendly).
 *
 * Filters:
 * - esab_disable_google_fonts  Skip web fonts entirely.
 * - esab_local_google_fonts    Return false to always load from Google.
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
		 * Cron hook that downloads a stylesheet and its font files.
		 */
		const CACHE_HOOK = 'esab_cache_google_fonts';

		/**
		 * Folder inside uploads that holds cached fonts.
		 */
		const CACHE_DIR = 'esab-fonts';

		/**
		 * Instance of the class
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Collected families, keyed by family name: array( 'weights' => array, 'italic' => bool ).
		 *
		 * @var array
		 */
		private static $all_fonts = array();

		/**
		 * Families already enqueued this request.
		 *
		 * @var array
		 */
		private static $loaded = array();

		/**
		 * Whether any stylesheet is served from Google this request (for preconnect hints).
		 *
		 * @var bool
		 */
		private static $remote = false;

		/**
		 * Families the plugin never requests remotely.
		 *
		 * @var array
		 */
		private static $system = array(
			'Default',
			'inherit',
			'Arial',
			'Tahoma',
			'Verdana',
			'Helvetica',
			'Times New Roman',
			'Times-New-Roman',
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

			// Fallback for content the early pass cannot see: archives, widgets,
			// block-theme template parts.
			add_action( 'esab_render_block', array( $this, 'font_generator' ) );

			add_filter( 'wp_resource_hints', array( $this, 'resource_hints' ), 10, 2 );

			add_action( self::CACHE_HOOK, array( $this, 'cache_stylesheet' ) );
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
		 * Whether font files should be served from this site.
		 *
		 * @since 1.6.0
		 * @return bool
		 */
		public function is_local() {
			/**
			 * Filter to serve Google Fonts from the site instead of Google's CDN.
			 *
			 * @since 1.6.0
			 * @param bool $local Default true.
			 */
			return (bool) apply_filters( 'esab_local_google_fonts', true );
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
		 * Pull families, weights and italic use out of a block's attributes.
		 *
		 * Typography attributes are generated in sets — esab_<name>FontFamily
		 * holds the family, esab_<name>FontWeight an object whose `value` key
		 * holds the weight, esab_<name>FontStyle the style.
		 *
		 * @since 1.5.2
		 * @param array $attributes Block attributes.
		 * @return void
		 */
		private function collect( $attributes ) {
			foreach ( $attributes as $key => $value ) {
				if ( empty( $value ) || ! is_string( $value ) || 0 !== strpos( $key, 'esab_' ) || false === strpos( $key, 'FontFamily' ) ) {
					continue;
				}

				$family = trim( $value );

				if ( '' === $family || in_array( $family, self::$system, true ) ) {
					continue;
				}

				if ( ! isset( self::$all_fonts[ $family ] ) ) {
					self::$all_fonts[ $family ] = array(
						'weights' => array(),
						'italic'  => false,
					);
				}

				$weight_key = str_replace( 'FontFamily', 'FontWeight', $key );
				$weight     = '';

				if ( ! empty( $attributes[ $weight_key ]['value'] ) ) {
					$weight = preg_replace( '/[^0-9]/', '', (string) $attributes[ $weight_key ]['value'] );
				}

				if ( '' !== $weight ) {
					self::$all_fonts[ $family ]['weights'][ $weight ] = true;
				} else {
					// No weight set: the theme decides, which is usually regular
					// text and bold headings. Load both so the browser does not
					// fake bold.
					self::$all_fonts[ $family ]['weights']['400'] = true;
					self::$all_fonts[ $family ]['weights']['700'] = true;
				}

				$style_key = str_replace( 'FontFamily', 'FontStyle', $key );

				if ( isset( $attributes[ $style_key ] ) && 'italic' === $attributes[ $style_key ] ) {
					self::$all_fonts[ $family ]['italic'] = true;
				}
			}
		}

		/**
		 * Google Fonts v1 `family` spec, e.g. "Open+Sans:400,700,700italic".
		 *
		 * v1 rather than css2: css2 rejects the whole request when a family
		 * lacks one of the requested weights, v1 skips it. Both serve woff2.
		 *
		 * @since 1.6.0
		 * @param string $family Family name.
		 * @param array  $data   Collected weights and italic flag.
		 * @return string
		 */
		private function family_spec( $family, $data ) {
			$weights = array_keys( $data['weights'] );
			sort( $weights, SORT_NUMERIC );

			$variants = $weights;

			if ( $data['italic'] ) {
				foreach ( $weights as $weight ) {
					$variants[] = $weight . 'italic';
				}
			}

			return str_replace( ' ', '+', $family ) . ':' . implode( ',', $variants );
		}

		/**
		 * Enqueue every collected family not loaded yet, in one request.
		 *
		 * @return void
		 */
		public function fonts_loader() {
			if ( $this->is_disabled() || empty( self::$all_fonts ) ) {
				return;
			}

			$families = array();

			foreach ( self::$all_fonts as $family => $data ) {
				if ( ! isset( self::$loaded[ $family ] ) ) {
					$families[ $family ] = $this->family_spec( $family, $data );
				}
			}

			if ( empty( $families ) ) {
				return;
			}

			ksort( $families );

			$url = add_query_arg(
				array(
					'family'  => implode( '|', $families ),
					'display' => 'swap',
				),
				'https://fonts.googleapis.com/css'
			);

			$handle = 'esab-fonts-' . substr( md5( $url ), 0, 8 );
			$src    = $this->stylesheet_src( $url );

			wp_register_style( $handle, $src, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- the URL is content-addressed.
			wp_enqueue_style( $handle );

			foreach ( array_keys( $families ) as $family ) {
				self::$loaded[ $family ] = true;
			}
		}

		/**
		 * Local copy of a Google stylesheet when cached, otherwise the remote
		 * URL (and a background job to cache it for next time).
		 *
		 * @since 1.6.0
		 * @param string $url Google Fonts stylesheet URL.
		 * @return string
		 */
		private function stylesheet_src( $url ) {
			if ( $this->is_local() ) {
				$paths = $this->cache_paths();
				$file  = md5( $url ) . '.css';

				if ( $paths && file_exists( $paths['dir'] . '/' . $file ) ) {
					return $paths['url'] . '/' . $file;
				}

				if ( $paths && ! get_transient( 'esab_font_fail_' . md5( $url ) ) && ! wp_next_scheduled( self::CACHE_HOOK, array( $url ) ) ) {
					wp_schedule_single_event( time(), self::CACHE_HOOK, array( $url ) );
				}
			}

			self::$remote = true;

			return $url;
		}

		/**
		 * Cache folder path and URL.
		 *
		 * @since 1.6.0
		 * @return array|false
		 */
		private function cache_paths() {
			$uploads = wp_upload_dir( null, false );

			if ( ! empty( $uploads['error'] ) ) {
				return false;
			}

			return array(
				'dir' => trailingslashit( $uploads['basedir'] ) . self::CACHE_DIR,
				'url' => set_url_scheme( trailingslashit( $uploads['baseurl'] ) . self::CACHE_DIR ),
			);
		}

		/**
		 * Download a Google stylesheet and its font files into uploads.
		 *
		 * Runs from WP-Cron so no visitor waits on it. The stylesheet is
		 * written last; until it exists the page keeps using Google.
		 *
		 * @since 1.6.0
		 * @param string $url Google Fonts stylesheet URL.
		 * @return void
		 */
		public function cache_stylesheet( $url ) {
			if ( ! is_string( $url ) || 0 !== strpos( $url, 'https://fonts.googleapis.com/' ) ) {
				return;
			}

			$paths = $this->cache_paths();

			if ( ! $paths || ! wp_mkdir_p( $paths['dir'] ) || ! wp_is_writable( $paths['dir'] ) ) {
				return;
			}

			$key      = md5( $url );
			$fail     = 'esab_font_fail_' . $key;
			$response = wp_remote_get(
				$url,
				array(
					'timeout'    => 15,
					// A current browser user agent, so Google answers with woff2.
					'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
				)
			);

			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				set_transient( $fail, 1, DAY_IN_SECONDS );
				return;
			}

			$css = wp_remote_retrieve_body( $response );

			if ( ! preg_match_all( '#url\((https://fonts\.gstatic\.com/[^)\s\'"]+)\)#', $css, $matches ) ) {
				set_transient( $fail, 1, DAY_IN_SECONDS );
				return;
			}

			foreach ( array_unique( $matches[1] ) as $font_url ) {
				$ext  = strtolower( pathinfo( wp_parse_url( $font_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
				$ext  = in_array( $ext, array( 'woff2', 'woff', 'ttf', 'otf' ), true ) ? $ext : 'woff2';
				$name = substr( $key, 0, 8 ) . '-' . substr( md5( $font_url ), 0, 12 ) . '.' . $ext;
				$path = $paths['dir'] . '/' . $name;

				if ( ! file_exists( $path ) ) {
					$font = wp_remote_get( $font_url, array( 'timeout' => 15 ) );

					if ( is_wp_error( $font ) || 200 !== wp_remote_retrieve_response_code( $font ) || ! $this->write_file( $path, wp_remote_retrieve_body( $font ) ) ) {
						set_transient( $fail, 1, DAY_IN_SECONDS );
						return;
					}
				}

				// Relative to the stylesheet, so it survives http/https and domain changes.
				$css = str_replace( $font_url, $name, $css );
			}

			$this->write_file( $paths['dir'] . '/' . $key . '.css', "/* Cached by Easy Accordion Block from Google Fonts */\n" . $css );
		}

		/**
		 * Write a file with the WordPress filesystem API.
		 *
		 * @since 1.6.0
		 * @param string $path     Absolute path.
		 * @param string $contents File contents.
		 * @return bool
		 */
		private function write_file( $path, $contents ) {
			global $wp_filesystem;

			if ( '' === $contents ) {
				return false;
			}

			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			// Direct access only: cron has no way to ask for FTP credentials.
			if ( 'direct' !== get_filesystem_method( array(), dirname( $path ) ) || ! WP_Filesystem() ) {
				return false;
			}

			return (bool) $wp_filesystem->put_contents( $path, $contents, FS_CHMOD_FILE );
		}

		/**
		 * Warm up the Google connections, only when fonts actually come from Google.
		 *
		 * @since 1.5.2
		 * @param array  $hints Current hints.
		 * @param string $relation Hint relation type.
		 * @return array
		 */
		public function resource_hints( $hints, $relation ) {
			if ( 'preconnect' !== $relation || ! self::$remote ) {
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
