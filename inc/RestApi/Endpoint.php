<?php
namespace Pjs\RestApi;

use \WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class Endpoint extends \Cvy\DesignPatterns\Singleton
{
  protected function __construct()
  {
    add_action( 'rest_api_init', [ $this, '_register' ] );
  }

  public function _register() : void
  {
    $route = '/' . $this->get_route();

    register_rest_route( $this->get_namespace(), $route, [
      'methods' => $this->get_methods(),
      'callback' => [ $this, '_get_response' ],
      'permission_callback' => [ $this, '_check_authorized' ],
      'args' => $this->get_args(),
    ]);
  }

  abstract protected function get_route() : string;

  abstract protected function get_methods() : array;

  protected function get_args() : array
  {
    return [];
  }

  public final function _get_response() : WP_REST_Response
  {
    return $this->get_response();
  }

  abstract protected function get_response() : WP_REST_Response;

  public final function _check_authorized() : bool
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
}