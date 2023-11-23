<?php
namespace Pjs\Countries;
use Pjs\DB\DB;

if ( ! defined( 'ABSPATH' ) ) exit;

class Country
{
  private $id;

  public function __construct( int $id )
  {
    $this->id = $id;
  }

  public function get_id() : int
  {
    return $this->id;
  }

  public function get_code() : string
  {
    return DB::get_var( DB::prepare(
      'SELECT code FROM pjs_countries WHERE id = %d',
      [ $this->id ]
    ));
  }

  public function exists() : bool
  {
    return ! empty( $this->get_code() );
  }
}