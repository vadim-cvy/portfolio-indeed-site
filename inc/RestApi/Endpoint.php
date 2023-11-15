<?php
namespace Pjs\RestApi;

use WP_REST_Request;
use WP_REST_Response;
use Pjs\DB\DB;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class Endpoint extends \Cvy\DesignPatterns\Singleton
{
  private $args;

  protected function __construct()
  {
    add_action( 'rest_api_init', fn() => $this->register() );
  }

  private function register() : void
  {
    $route = '/' . $this->get_route();

    register_rest_route( $this->get_namespace(), $route, [
      'methods' => $this->get_methods(),
      'callback' => fn( WP_REST_Request $request ) => $this->handle_request( $request ),
      'permission_callback' => fn() => $this->check_authorized(),
      'args' => $this->get_args_scheme()->get_wp_formatted(),
    ]);
  }

  abstract protected function get_route() : string;

  abstract protected function get_methods() : array;

  abstract protected function get_args_scheme() : ArgsScheme;

  private function handle_request( WP_REST_Request $request ) : WP_REST_Response
  {
    try
    {
      $this->set_args( $request->get_params() );
    }
    catch ( ArgValidationError $e )
    {
      return $this->build_error_response( $e->getMessage() );
    }

    return $this->get_response();
  }

  private function set_args( array $args ) : void
  {
    foreach ( $this->get_args_scheme()->get_all() as $key => $scheme )
    {
      $value = $args[ $key ] ?? null;

      if ( $scheme->get_is_required() && ( ! isset( $value ) || $value === '' ) )
      {
        ArgValidationError::throw( $key, 'Argument is required but is missed.' );
      }

      if ( $scheme->get_type() === ArgScheme::TYPE_FLOAT )
      {
        $value = (float) $value;
      }

      $args[ $key ] = $value;
    }

    foreach ( $args as $key => $value )
    {
      if ( ! in_array( $key, $this->get_args_scheme()->get_keys() ) )
      {
        ArgValidationError::throw( $key, 'Argument does not appear in allowed arguments list.' );
      }

      $this->args[ $key ] = $this->normalize_arg_value( $key, $value );
    }
  }

  abstract protected function normalize_arg_value( string $key, mixed $value ) : mixed;

  abstract protected function get_response() : WP_REST_Response;

  private function check_authorized() : bool
  {
    return $this->is_authorized();
  }

  abstract protected function is_authorized() : bool;

  protected function build_success_response( array $data = [] ) : WP_REST_Response
  {
    return $this->build_response( $data, 'success' );
  }

  protected function build_error_response( string $err_msg ) : WP_REST_Response
  {
    return $this->build_response( [ 'error_message' => $err_msg ], 'error' );
  }

  private function build_response( array $data, string $status ) : WP_REST_Response
  {
    $data['status'] = $status;

    $data['debug'] = [
      'db' => DB::get_debug_data()
    ];

    return new WP_REST_Response( $data, 200 );
  }

  protected final function get_namespace() : string
  {
    return 'pjs/v1';
  }

  protected function get_args()
  {
    return array_filter( $this->args );
  }

  protected function get_arg( string $key )
  {
    return $this->get_args()[ $key ] ?? null;
  }
}