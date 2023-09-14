<?php
namespace Pjs\RestApi\FrontEnd;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class FrontEndEndpoint extends \Pjs\RestApi\Endpoint
{
  protected function get_route() : string
  {
    return 'frontend/';
  }
}