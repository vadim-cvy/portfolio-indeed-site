<?php
namespace Pjs\RestApi\FrontEnd\JobsFilter\Search;

if ( ! defined( 'ABSPATH' ) ) exit;

class SearchSession
{
  private string $key = 'pjs_jobs_filter_search';

  private string $search_id;

  public function __construct( string $search_id )
  {
    $this->search_id = $search_id;

    if ( ! session_id() )
    {
      session_start();
    }

    if (
      ! isset( $_SESSION[ $this->key ] )
      || $this->get( 'search_id' ) !== $this->search_id
    )
    {
      $this->reset();
    }
  }

  private function reset() : void
  {
    $_SESSION[ $this->key ] = [];

    $this->set( 'search_id', $this->search_id );
  }

  private function get( string $key )
  {
    return $_SESSION[ $this->key ][ $key ];
  }

  private function set( string $key, $val )
  {
    $_SESSION[ $this->key ][ $key ] = $val;
  }
}