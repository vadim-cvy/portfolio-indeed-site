<?php
namespace Pjs\Pages;

if ( ! defined( 'ABSPATH' ) ) exit;

class JobsFilter extends Page
{
  static public function get_id() : int
  {
    return 10;
  }
}