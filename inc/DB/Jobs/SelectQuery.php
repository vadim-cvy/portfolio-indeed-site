<?php
namespace Pjs\DB\Jobs;
use Exception;
use \Pjs\DB\DB;

if ( ! defined( 'ABSPATH' ) ) exit;

class SelectQuery
{
  const PERIOD_LAST_DAY = 'last_day';

  const PERIOD_LAST_3_DAYS = 'last_3_days';

  const PERIOD_LAST_7_DAYS = 'last_7_days';

  const PERIOD_LAST_14_DAYS = 'last_14_days';

  const PERIOD_LAST_30_DAYS = 'last_30_days';

  const PERIOD_30_DAYS_OLDER = 'old';

  const STATUS_ACTIVE = 'active';

  const STATUS_INACTIVE = 'inactive';

  const STATUS_PENDING = 'pending';

  private string $country_code;

  private array $statuses;

  private array $cols;

  private int $limit = -1;

  private int $offset = -1;

  private array $activation_periods = [];

  private array $forced_indexes = [];

  private array $where = [
    'sql' => '',
    'placeholder_values' => [],
  ];

  private string $order_by = '';

  public function __construct( string $country_code, array $statuses, array $cols )
  {
    $this->country_code = $country_code;
    $this->statuses = $statuses;
    $this->cols = $cols;
  }

  public function set_activation_periods( array $activation_periods ) : SelectQuery
  {
    $this->activation_periods = $activation_periods;

    return $this;
  }

  public function force_index( array $indexes ) : SelectQuery
  {
    $this->forced_indexes = $indexes;

    return $this;
  }

  public function add_where( string $sql, array $placeholder_values ) : SelectQuery
  {
    if ( preg_match( '~\s(OR|AND)\s~', $this->where['sql'] ) )
    {
      $this->where['sql'] = "( {$this->where['sql']} )";
    }

    $this->where['sql'] .= $sql . ' ';

    $this->where['placeholder_values'] = array_merge( $this->where['placeholder_values'], $placeholder_values );

    return $this;
  }

  public function set_order_by( string $order_by ) : SelectQuery
  {
    $this->order_by = $order_by;

    return $this;
  }

  public function set_limit( int $limit ) : SelectQuery
  {
    $this->limit = $limit;

    return $this;
  }

  public function set_offset( int $offset ) : SelectQuery
  {
    $this->offset = $offset;

    return $this;
  }

  public function get_results() : array
  {
    return DB::get_results( $this->get_sql() );
  }

  public function get_var() : float | int | string
  {
    return DB::get_var( $this->get_sql() );
  }

  private function get_sql() : string
  {
    $sql = '';
    $placeholder_values = [];

    foreach ( $this->statuses as $status )
    {
      if ( $status === static::STATUS_ACTIVE )
      {
        if ( empty( $this->activation_periods ) )
        {
          throw new Exception( "Activation periods must be passed when doing lookup through \"$status\" status." );
        }

        $periods = $this->activation_periods;
      }
      else
      {
        $periods = [];
      }

      foreach ( $periods as $period )
      {
        if ( ! empty( $sql ) )
        {
          $sql .= 'UNION ';
        }

        $sql .=
          $this->get_select_statement() .
          $this->get_from_statement( $status, $period );

        if ( $this->has_meta_cols() )
        {
          $sql .= $this->get_join_meta_statement();
        }

        if ( ! empty( $this->forced_indexes ) )
        {
          $sql .= $this->get_force_index_statement();
        }

        if ( ! empty( $this->where['sql'] ) )
        {
          $sql .= 'WHERE ' . $this->where['sql'];
          $placeholder_values += $this->where['placeholder_values'];
        }

        if ( $this->order_by )
        {
          $sql .= "ORDER BY {$this->order_by} ";
        }

        if ( $this->limit !== -1 )
        {
          $sql .= 'LIMIT %d ';
          $placeholder_values[] = $this->limit;
        }

        if ( $this->offset !== -1 )
        {
          $sql .= 'OFFSET %d ';
          $placeholder_values[] = $this->offset;
        }
      }
    }

    return DB::prepare( $sql, $placeholder_values );
  }

  private function get_select_statement() : string
  {
    $cols = $this->cols;

    foreach ( $cols as $i => $col )
    {
      $table_prefixes = [ 'j', 'm' ];

      foreach ( $table_prefixes as $table_prefix )
      {
        $table_prefix .= '.';

        if (
          strpos( $col, $table_prefix ) !== false
          && strpos( strtoupper( $col ), ' AS ' ) === false
        )
        {
          $cols[ $i ] .= ' AS ' . str_replace( $table_prefix, '', $col );
        }
      }
    }

    return 'SELECT ' . implode( ', ', $cols ) . ' ';
  }

  private function get_from_statement( string $status, string $period ) : string
  {
    return "FROM pjs_jobs__{$this->country_code}_{$status}_$period AS j ";
  }

  private function get_join_meta_statement() : string
  {
    return 'JOIN pjs_jobs_meta AS m ON j.id = m.id ';
  }

  private function get_jobs_table_name_statement( string $status, string $period = '' ) : string
  {
    $table_name = "pjs_jobs__{$this->country_code}_$status AS j";

    if ( $period )
    {
      $table_name .= '_' . $period;
    }

    return $table_name;
  }

  private function get_force_index_statement() : string
  {
    $indexes_str = implode( ', ', $this->forced_indexes );

    return "FORCE INDEX( $indexes_str ) ";
  }

  private function has_meta_cols() : bool
  {
    $meta_cols = array_filter(
      $this->cols,
      fn( $col ) => strpos( $col, 'm.' ) === 0
    );

    return ! empty( $meta_cols );
  }
}