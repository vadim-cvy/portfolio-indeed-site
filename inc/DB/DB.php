<?php
namespace Pjs\DB;

if ( ! defined( 'ABSPATH' ) ) exit;

class DB
{
  static private $cache = [];

  static private $debug_data = [];

  static public function prepare( string $sql, array $placeholder_values ) : string
  {
    global $wpdb;

    return $wpdb->prepare( $sql, $placeholder_values );
  }

  static public function get_results( string $sql, bool $return_cached = true ) : array
  {
    return static::select( 'get_results', $sql, $return_cached );
  }

  static public function get_var( string $sql, bool $return_cached = true ) : string | int | float
  {
    $val = static::select( 'get_var', $sql, $return_cached );

    if ( is_numeric( $val ) )
    {
      $val =
        (int) $val == (float) $val ?
        (int) $val :
        (float) $val;
    }
    else if ( ! isset( $val ) )
    {
      $val = '';
    }

    return $val;
  }

  static private function select( string $wpdb_method_name, string $sql, bool $return_cached = true )
  {
    global $wpdb;

    $start_time_nanosec = hrtime( true );

    $is_result_from_cache = false;

    if ( $return_cached && isset( static::$cache[ $sql ] ) )
    {
      $result = static::$cache[ $sql ];

      $is_result_from_cache = true;
    }
    else
    {
      $result = $wpdb->$wpdb_method_name( $sql );

      static::$cache[ $sql ] = $result;
    }

    $end_time_nanosec = hrtime( true );

    $operation_time_nanosec = $end_time_nanosec - $start_time_nanosec;

    static::$debug_data[] = [
      'query' => $sql,
      'time' => round( $operation_time_nanosec / 1e9, 5 ),
      'is_from_cache' => $is_result_from_cache,
    ];

    return $result;
  }

  static public function get_debug_data() : array
  {
    return static::$debug_data;
  }
}