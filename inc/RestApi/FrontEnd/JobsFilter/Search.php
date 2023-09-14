<?php
namespace Pjs\RestApi\FrontEnd\JobsFilter;

use \Pjs\Jobs\Jobs_DB;

if ( ! defined( 'ABSPATH' ) ) exit;

class Search extends JobsFilterEndpoint
{
  protected function get_route() : string
  {
    return parent::get_route() . 'search/';
  }

  protected function get_args() : array
  {
    return [];
  }

  protected function get_response() : \WP_REST_Response
  {
    return $this->build_success_response([
      'matches' => []
    ]);
  }
}