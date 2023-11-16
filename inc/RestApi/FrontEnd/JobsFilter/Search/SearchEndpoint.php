<?php
namespace Pjs\RestApi\FrontEnd\JobsFilter\Search;
use DateInterval;
use DateTime;
use Exception;
use Pjs\Countries\Country;
use Pjs\RestApi\ArgScheme;
use Pjs\RestApi\ArgsScheme;
use Pjs\RestApi\ArgValidationError;
use Pjs\RestApi\FrontEnd\JobsFilter\JobsFilterEndpoint;
use WP_REST_Response;
use Pjs\DB\DB;


if ( ! defined( 'ABSPATH' ) ) exit;

class SearchEndpoint extends JobsFilterEndpoint
{
  protected function get_route_suffix() : string
  {
    return 'search/';
  }

  protected function get_response() : WP_REST_Response
  {
    $this->init_session();

    return $this->build_success_response([
      'matches' => $this->get_matches(),
    ]);
  }

  private function init_session() : void
  {
    if ( ! session_id() )
    {
      session_start();
    }

    if (
      ! isset( $_SESSION[ $this->get_route() ] )
      || $this->get_session_var( 'search_id' ) !== $this->get_search_id()
    )
    {
      $this->reset_session();
    }
  }

  private function reset_session() : void
  {
    $_SESSION[ $this->get_route() ] = [];

    $this->set_session_var( 'search_id', $this->get_search_id() );
  }

  private function get_session_var( string $key )
  {
    return $_SESSION[ $this->get_route() ][ $key ];
  }

  private function set_session_var( string $key, $val )
  {
    $_SESSION[ $this->get_route() ][ $key ] = $val;
  }

  private function get_search_id() : string
  {
    // copy array
    $args = (array) (object) $this->get_args();

    ksort( $args );

    return md5( serialize( $args ) );
  }

  protected function get_matches() : array
  {
    $sql = $this->get_sql__select_from([ 'id', 'title' ]);

    $where = $this->get_where();

    $sql .= $where['sql'];
    $placeholder_values = $where['placeholder_values'];

    $sql .= 'ORDER BY j.activation_time DESC ';

    // todo: add arg "page"
    // todo: check if there is any difference (in time) for selecting 10 vs 30 vs 50 vs 100 results at all. Maybe select 30 or 50 on first page if there is no difference. And then select 30 or 50 on each new page
    $sql .= 'LIMIT %d ';
    $placeholder_values[] = $this->get_limit(); // todo: return 30 on second page and 50 for all the next pages

    /**
     * todo: get from session:
     * Session should store first match id and total returned matches
     *
     * I'm not sure if I need to add "WHERE id > first_match_id" as it'll make to apply the condition for all of the thousands results while it'll filter about 100 rows at max.
     * So it is better to test if I can run one more query that will check how much matches are there with "id <= first_match_id" and then calculate offset by the returned number + session total returned matches
     */
    $sql .= 'OFFSET %d';
    $placeholder_values[] = 0;

    return DB::get_results( $sql, $placeholder_values );
  }

  private function get_sql__select_from( array $cols, string $join = '' ) : string
  {
    $country_code = (new Country( $this->get_arg( 'country_id' ) ))->get_code();

    // todo: get time range based on the last match. Maybe add arg: "from_time"
    $period = 'last_day';

    $table_name = "pjs_jobs__{$country_code}_active_$period";

    foreach ( $cols as $i => $col )
    {
      // todo: complete list of jobs table columns
      if ( in_array( $col, [ 'id', 'title', 'activation_time' ] ) )
      {
        $col = 'j.' . $col;
      }

      $cols[ $i ] = $col;
    }

    $cols_str = implode( ', ', $cols );

    return "SELECT $cols_str FROM $table_name as j $join";
  }

  private function get_where() : array
  {
    $where = $this->get_base_where();

    $is_term_only_search = count( $this->get_filter_fields_args() ) === 1;

    /**
     * todo: this should not work only for first call but for:
     * - search ordered by date (not relevance)
     * - maybe for big tables like last month or last 2 monthes - but that's a question, maybe it'll work ok as is
     */
    if ( ! $this->is_first_call() || ! $is_term_only_search )
    {
      return $where;
    }

    $last_day_total_matches = DB::get_var(
      $this->get_sql__select_from([ 'COUNT(*)' ]) . $where['sql'],
      $where['placeholder_values']
    );

    $sec_in_min = 60;
    $sec_in_day = $sec_in_min * 60 * 24;

    $limit_threthold = $this->get_limit() * 5;

    $activation_time_offset  = floor( $sec_in_day / ( $last_day_total_matches / $limit_threthold ) );

    $max_activation_time_offset = $sec_in_min * 15;

    if ( $activation_time_offset > $max_activation_time_offset )
    {
      return $where;
    }

    $last_job_activation_time = DB::get_var(
      $this->get_sql__select_from([ 'activation_time' ]) . ' ORDER BY j.activation_time DESC LIMIT 1'
    );

    $min_activation_time = new DateTime( $last_job_activation_time );
    $min_activation_time->sub(new DateInterval('PT' . $activation_time_offset . 'S'));

    $where['sql'] .= ' AND j.activation_time >= "%s" ';
    $where['placeholder_values'][] = $min_activation_time->format('Y-m-d H:i:s');

    $where['sql'] = 'FORCE INDEX(activation_time) ' . $where['sql'];

    return $where;
  }

  private function get_base_where() : array
  {
    $clauses_sql = [];

    $placeholder_values = [];

    foreach ( $this->get_filter_fields_args() as $key => $value )
    {
      switch ( $key )
      {
        // Todo: ensure mysql mintoken size is 2 chars
        case 'search_term':
          $clauses_sql[] = 'MATCH(j.title) AGAINST("%s")';

          $placeholder_values[] = $value;
          break;

        default:
          $clause_sql = '%i=';
          $placeholder_values[] = "j.$key";

          switch ( $this->get_args_scheme()->get_one( $key )->get_type() )
          {
            case ArgScheme::TYPE_INT:
            case ArgScheme::TYPE_BOOL:
              $value_placeholder = '%d';
              break;

            case ArgScheme::TYPE_FLOAT:
              $value_placeholder = '%f';
              break;

            case ArgScheme::TYPE_STR:
              $value_placeholder = '%s';
              break;

            default:
              throw new Exception( "Can't create $key WHERE clause!" );
          }

          $clause_sql .= $value_placeholder;
          $placeholder_values[] = $value;

          $clauses_sql[] = $clause_sql;

          break;
      }
    }

    return [
      'sql' => 'WHERE ' . implode( ' AND ', $clauses_sql ) . ' ',
      'placeholder_values' => $placeholder_values,
    ];
  }

  private function get_limit() : int
  {
    // todo: maybe make 10-15 for first call
    return 50;
  }

  private function get_filter_fields_args() : array
  {
    $args = [];

    foreach ( $this->get_filter_fields_arg_schemes() as $arg_scheme )
    {
      $key = $arg_scheme->get_key();

      $args[ $key ] = $this->get_arg( $key );
    }

    return array_filter( $args, fn( $val ) => isset( $val ) );
  }

  protected function get_args_scheme() : ArgsScheme
  {
    // todo: salary from/to
    // todo: date
    // todo: category
    // todo: if category is set - category unique meta
    // todo: etc
    return new ArgsScheme(
      // todo maybe get country from cookie
      new ArgScheme( 'country_id', ArgScheme::TYPE_INT, true ),

      ...$this->get_filter_fields_arg_schemes(),
    );
  }

  private function get_filter_fields_arg_schemes() : array
  {
    return [
      new ArgScheme( 'search_term', ArgScheme::TYPE_STR, true ),
      new ArgScheme( 'region_id', ArgScheme::TYPE_INT, false ),
      new ArgScheme( 'city_id', ArgScheme::TYPE_INT, false ),
      new ArgScheme( 'is_remote', ArgScheme::TYPE_BOOL, false ),
      new ArgScheme( 'is_full_time', ArgScheme::TYPE_BOOL, false ),
      new ArgScheme( 'is_temporary', ArgScheme::TYPE_BOOL, false ),
      new ArgScheme( 'is_internship', ArgScheme::TYPE_BOOL, false ),
      new ArgScheme( 'is_contract', ArgScheme::TYPE_BOOL, false ),
      new ArgScheme( 'is_volunteer', ArgScheme::TYPE_BOOL, false ),
      new ArgScheme( 'is_freshe', ArgScheme::TYPE_BOOL, false ),
    ];
  }

  protected function normalize_arg_value( string $key, mixed $value ) : mixed
  {
    $normalizer_method_name = 'normalize_' . $key;

    return method_exists( $this, $normalizer_method_name ) ?
      $this->$normalizer_method_name( $value ) :
      $value;
  }

  protected function normalize_search_term( string $search_term ) : string
  {
    $min_length = 2;

    if ( mb_strlen( $search_term, 'UTF-8' ) < $min_length )
    {
      ArgValidationError::throw( 'search_term', "Characters number must not be less than $min_length." );
    }

    return $search_term;
  }

  protected function normalize_country_id( string $country_id ) : string
  {
    $country = new Country( $country_id );

    if ( ! $country->exists() )
    {
      ArgValidationError::throw( 'country_id', 'Unknow country.' );
    }

    return $country_id;
  }
}