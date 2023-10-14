<?php
namespace Pjs\RestApi\FrontEnd\JobsFilter;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class JobsFilterEndpoint extends \Pjs\RestApi\FrontEnd\FrontEndEndpoint
{
  final protected function get_route() : string
  {
    return parent::get_route() . 'jobs-filter/' . $this->get_route_suffix();
  }

  abstract protected function get_route_suffix() : string;

  protected function get_methods() : array
  {
    return [ 'GET' ];
  }

  protected function is_authorized() : bool
  {
    return true;
  }
}