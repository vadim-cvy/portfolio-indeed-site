<?php
namespace Pjs\RestApi;

use Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class ArgValidationError extends Exception
{
  static public function throw( string $arg_key, string $msg ) : void
  {
    throw new static( $arg_key . ' argument error! ' . $msg );
  }
}