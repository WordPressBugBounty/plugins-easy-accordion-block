<?php
/**
 * REST API endpoint for accordion patterns with transient caching
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Esab_Patterns' ) ) {

    class Esab_Patterns {

        const TRANSIENT_KEY    = 'esab_patterns_cache';
        const TRANSIENT_EXPIRY = HOUR_IN_SECONDS * 12;
        const REMOTE_URL       = 'https://accordion.gutenbergkits.com/wp-json/divo-patterns/v1/patterns';
        const NAMESPACE        = 'esab/v1';
        const ROUTE            = '/patterns';

        private static $instance = null;

        public function __construct() {
            add_action( 'rest_api_init', [ $this, 'register_routes' ] );
        }

        public function register_routes() {
            register_rest_route(
                self::NAMESPACE,
                self::ROUTE,
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_patterns' ],
                    'permission_callback' => [ $this, 'permissions_check' ],
                    'args'                => [
                        'refresh' => [
                            'type'    => 'boolean',
                            'default' => false,
                        ],
                        'page' => [
                            'type'    => 'integer',
                            'default' => 1,
                        ],
                        'per_page' => [
                            'type'    => 'integer',
                            'default' => 20,
                        ],
                    ],
                ]
            );
        }

        /**
         * Only logged-in users with edit_posts capability can call this endpoint.
         */
        public function permissions_check() {
            return current_user_can( 'edit_posts' );
        }

        public function get_patterns( WP_REST_Request $request ) {
            $force_refresh = (bool) $request->get_param( 'refresh' );
            $page = (int) $request->get_param( 'page' );
            $per_page = (int) $request->get_param( 'per_page' );

            if ( ! $force_refresh ) {
                $cached = get_transient( self::TRANSIENT_KEY );
                if ( false !== $cached ) {
                    return $this->paginate_patterns( $cached, $page, $per_page );
                }
            }

            // Fetch all patterns with pagination support
            $all_patterns = [];
            $remote_page = 1;
            $per_page_remote = 100;
            $continue = true;

            while ( $continue ) {
                $url = add_query_arg(
                    [
                        'page'     => $remote_page,
                        'per_page' => $per_page_remote,
                    ],
                    self::REMOTE_URL
                );

                $response = wp_remote_get(
                    $url,
                    [
                        'timeout'    => 15,
                        'user-agent' => 'EasyAccordionBlock/' . ESAB_VERSION,
                    ]
                );

                if ( is_wp_error( $response ) ) {
                    return new WP_Error(
                        'esab_fetch_failed',
                        $response->get_error_message(),
                        [ 'status' => 502 ]
                    );
                }

                $code = wp_remote_retrieve_response_code( $response );
                if ( 200 !== (int) $code ) {
                    return new WP_Error(
                        'esab_fetch_failed',
                        sprintf( 'Remote API returned HTTP %d.', $code ),
                        [ 'status' => 502 ]
                    );
                }

                $body = wp_remote_retrieve_body( $response );
                $data = json_decode( $body, true );

                if ( ! is_array( $data ) ) {
                    return new WP_Error(
                        'esab_invalid_response',
                        'Remote API returned invalid JSON.',
                        [ 'status' => 502 ]
                    );
                }

                // If no data returned, stop pagination
                if ( empty( $data ) ) {
                    $continue = false;
                } else {
                    $all_patterns = array_merge( $all_patterns, $data );
                    $remote_page++;
                    // Stop if we got less items than requested (last page)
                    if ( count( $data ) < $per_page_remote ) {
                        $continue = false;
                    }
                }
            }

            set_transient( self::TRANSIENT_KEY, $all_patterns, self::TRANSIENT_EXPIRY );

            return $this->paginate_patterns( $all_patterns, $page, $per_page );
        }

        /**
         * Paginate patterns array
         */
        private function paginate_patterns( $patterns, $page = 1, $per_page = 20 ) {
            $total = count( $patterns );
            $offset = ( $page - 1 ) * $per_page;
            $paginated = array_slice( $patterns, $offset, $per_page );

            $response = [
                'items'       => $paginated,
                'total'       => $total,
                'page'        => $page,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total / $per_page ),
            ];

            return rest_ensure_response( $response );
        }

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }

    Esab_Patterns::instance();
}
