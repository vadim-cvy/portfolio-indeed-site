<?php
namespace Pjs\RestApi;
use Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class ArgsScheme
{
  private $scheme = [];

  public function __construct( ArgScheme ...$schemes )
  {
    foreach ( $schemes as $scheme )
    {
      $this->scheme[ $scheme->get_key() ] = $scheme;
    }
  }

  public function get_keys() : array
  {
    return array_keys( $this->scheme );
  }

  public function get_all() : array
  {
    return $this->scheme;
  }

  public function get_one( string $arg_key ) : ArgScheme
  {
    return $this->scheme[ $arg_key ];
  }

  public function get_scheme() : array
  {
    return $this->scheme;
  }

  public function get_wp_formatted() : array
  {
    return array_map(
      fn( ArgScheme $scheme ) => [
        'type' => $scheme->get_type(),
      ],
      $this->get_all()
    );
  }
}