<?php
namespace Pjs\RestApi;

use WP_Error;
use WP_REST_Request;
use \WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class Endpoint extends \Cvy\DesignPatterns\Singleton
{
  const ARG_TYPE_STR = 'string';
  const ARG_TYPE_INT = 'integer';
  const ARG_TYPE_FLOAT = 'number';
  const ARG_TYPE_BOOL = 'boolean';
  const ARG_TYPE_ARR = 'array';
  const ARG_TYPE_OBJ = 'object';

  protected $request = null;

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
      'args' => $this->get_args_data(),
    ]);
  }

  abstract protected function get_route() : string;

  abstract protected function get_methods() : array;

  abstract protected function get_args_data() : array;

  private function handle_request( WP_REST_Request $request ) : WP_REST_Response
  {
    $this->request = $request;

    return $this->get_response();
  }

  abstract protected function get_response() : WP_REST_Response;

  private function check_authorized() : bool
  {
    return $this->is_authorized();
  }

  abstract protected function is_authorized() : bool;

  protected function build_success_response( array $data = [] ) : WP_REST_Response
  {
    return $this->build_response( $data, 'success', 200 );
  }

  protected function build_response( array $data, string $status, int $code ) : WP_REST_Response
  {
    $data['status'] = $status;

    return new WP_REST_Response( $data, $code );
  }

  protected final function get_namespace() : string
  {
    return 'pjs/v1';
  }

  protected function build_arg_error( string $message, string $code = 'invalid_value' ) : WP_Error
  {
    return new WP_Error( $code, $message );
  }

  protected function get_arg( string $key )
  {
    $arg_data = $this->get_args_data()[ $key ] ?? null;

    if ( ! $arg_data )
    {
      throw new \Exception( "Param \"$key\" is not registered for this endpoint!" );
    }

    $val = $this->request->get_param( $key );

    if ( ! isset( $val ) )
    {
      return $val;
    }

    switch ( $arg_data['type'] )
    {
      case static::ARG_TYPE_INT:
        $val = (int) $val;
        break;

      case static::ARG_TYPE_FLOAT:
        $val = (float) $val;
        break;

      case static::ARG_TYPE_BOOL:
        $val = (bool) $val;
        break;
    }

    return $val;
  }
}