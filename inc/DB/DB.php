<?php
namespace Pjs\DB;

if ( ! defined( 'ABSPATH' ) ) exit;

class DB
{
  static public function get_results( string $sql, array $placeholder_values = [] ) : array
  {
    global $wpdb;

    return $wpdb->get_results( static::replace_placeholders( $sql, $placeholder_values ) );
  }

  static public function replace_placeholders( string $sql, array $placeholder_values = [] ) : string
  {
    global $wpdb;

    $placeholder_pattern = '~%\w{(\w+)}~';

    preg_match_all( $placeholder_pattern, $sql, $placeholder_matches );

    $wpdb_prepare_args = [];

    foreach ( $placeholder_matches[1] as $placeholder_key )
    {
      $sql = str_replace( '{' . $placeholder_key . '}', '', $sql );

      $wpdb_prepare_args[] = $placeholder_values[ $placeholder_key ];
    }

    return $wpdb->prepare( $sql, $wpdb_prepare_args );
  }
}