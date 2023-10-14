<?php
namespace Pjs\RestApi\FrontEnd\JobsFilter\Search;
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
    return $this->build_success_response([
      'matches' => $this->get_matches()
    ]);
  }

  protected function get_matches() : array
  {
    $sql = 'SELECT * FROM pjs_jobs WHERE title LIKE %s{search_term}';

    /**
     * todo: optimize
     * - optimize tables structure
     * - optimize the query itself
     * - stopwords
     * - learn more about mysql ngram
     * - mysql plugins for doing more complex full text search (synonyms, etc)
     */
    return DB::get_results( $sql, [
      'search_term' => $this->get_arg( 'search_term' ),
    ]);
  }

  protected function get_args_data() : array
  {
    return [
      'search_term' => [
        'type' => static::ARG_TYPE_STR,
        'required' => true,
        'validate_callback' =>
          fn( string $term ) =>
            mb_strlen( $term, 'UTF-8' ) < 2 ?
            $this->build_arg_error( 'Characters number must not be less than 2.' ) :
            true,
      ],
    ];
  }
}