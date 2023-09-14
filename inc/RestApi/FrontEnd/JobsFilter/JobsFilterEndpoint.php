<?php
namespace Pjs\RestApi\FrontEnd\JobsFilter;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class JobsFilterEndpoint extends \Pjs\RestApi\FrontEnd\FrontEndEndpoint
{
  protected function get_route() : string
  {
    return parent::get_route() . 'jobs-filter/';
  }

  protected function get_methods() : array
  {
    return [ 'GET' ];
  }

  protected function is_authorized() : bool
  {
    return true;
  }
}