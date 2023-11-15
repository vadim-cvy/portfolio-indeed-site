<?php
namespace Pjs\Jobs;

if ( ! defined( 'ABSPATH' ) ) exit;

class Job
{
  protected $raw_data;

  public function __construct( int $id, array $raw_data = [] )
  {
    $raw_data['id'] = $id;

    $this->raw_data = $raw_data;
  }

  public function get_id() : int
  {
    return $this->raw_data['id'];
  }
}