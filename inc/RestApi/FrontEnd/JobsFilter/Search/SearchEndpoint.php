<?php
namespace Pjs\RestApi\FrontEnd\JobsFilter\Search;
use DateInterval;
use DateTime;
use Exception;
use Pjs\Countries\Country;
use Pjs\DB\Jobs\SelectQuery;
use Pjs\RestApi\ArgScheme;
use Pjs\RestApi\ArgsScheme;
use Pjs\RestApi\ArgValidationError;
use Pjs\RestApi\FrontEnd\JobsFilter\JobsFilterEndpoint;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) exit;

class SearchEndpoint extends JobsFilterEndpoint
{
  private SearchSession $session;

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
    $this->session = new SearchSession( $this->get_search_id() );
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
    $query = $this->create_query_instance([
      'j.id',
      'j.title',
    ]);

    $this->set_query_where( $query );

    return $query
      ->set_order_by( 'j.activation_time DESC' )
      ->set_limit( $this->get_limit() )
      /**
       * todo: get from session:
       * Session should store first match id and total returned matches
       *
       * I'm not sure if I need to add "WHERE id > first_match_id" as it'll make to apply the condition for all of the thousands results while it'll filter about 100 rows at max.
       * So it is better to test if I can run one more query that will check how much matches are there with "id <= first_match_id" and then calculate offset by the returned number + session total returned matches
       */
      // ->set_offset( )
      ->get_results();
  }

  private function create_query_instance( array $cols ) : SelectQuery
  {
    $query = new SelectQuery( $this->get_country_code(), [ 'active' ], $cols );

    // todo: get it dynamically
    $query->set_activation_periods( [ 'last_day' ] );

    return $query;
  }

  private function set_query_where( SelectQuery $query ) : void
  {
    $this->set_query_base_where( $query );

    $this->optimize_query_where( $query );
  }

  private function optimize_query_where( SelectQuery $query ) : void
  {
    $is_term_only_search = count( $this->get_filter_fields_args() ) === 1;

    // todo: make dynamic
    $is_first_call = true;
    // todo: make dynamic
    $is_order_by_date = false;

    if ( ! ( $is_first_call || $is_order_by_date ) || ! $is_term_only_search )
    {
      return;
    }

    $last_day_total_matches_query = $this->create_query_instance( [ 'COUNT(*)' ] );

    $this->set_query_base_where( $last_day_total_matches_query );

    $last_day_total_matches = intval( $last_day_total_matches_query->get_var() );

    if ( $last_day_total_matches === 0 )
    {
      return;
    }

    $sec_in_min = 60;
    $sec_in_day = $sec_in_min * 60 * 24;

    $limit_threthold = $this->get_limit() * 5;

    $activation_time_offset  = floor( $sec_in_day / ( $last_day_total_matches / $limit_threthold ) );

    $max_activation_time_offset = $sec_in_min * 15;

    if ( $activation_time_offset > $max_activation_time_offset )
    {
      return;
    }

    $last_job_activation_time =
      $this->create_query_instance([ 'j.activation_time' ])
      ->set_limit( 1 )
      ->get_var();

    $min_activation_time = new DateTime( $last_job_activation_time );
    $min_activation_time->sub(new DateInterval('PT' . $activation_time_offset . 'S'));

    $query->add_where(
      ' AND j.activation_time >= "%s" ',
      [ $min_activation_time->format('Y-m-d H:i:s') ]
    );

    $query->force_index([ 'activation_time' ]);
  }

  private function set_query_base_where( SelectQuery $query ) : void
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

    $sql = implode( ' AND ', $clauses_sql );

    $query->add_where( $sql, $placeholder_values );
  }

  private function get_limit() : int
  {
    return 50;
  }

  private function get_country_code() : string
  {
    return (new Country( $this->get_arg( 'country_id' ) ))->get_code();
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