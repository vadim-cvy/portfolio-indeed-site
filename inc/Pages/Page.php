<?php
namespace Pjs\Pages;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class Page extends \Cvy\DesignPatterns\Singleton
{
  abstract static public function get_id() : int;

  static public function is_current() : bool
  {
    global $wp_query;

    if (
      isset( $wp_query->queried_object ) &&
      isset( $wp_query->queried_object->ID ) &&
      $wp_query->queried_object->post_type === 'page'
    )
    {
      return $wp_query->queried_object->ID === static::get_id();
    }

    return false;
  }
}