<?php
namespace Pjs\RestApi;
use Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class ArgScheme
{
  const TYPE_STR = 'string';
  const TYPE_INT = 'integer';
  const TYPE_FLOAT = 'number';
  const TYPE_BOOL = 'boolean';
  const TYPE_ARR = 'array';
  const TYPE_OBJ = 'object';

  private $key;

  private $type;

  private $value_validator;

  private $is_required;

  private $default_value_getter;

  public function __construct( string $key, string $type, bool $is_required, callable $default_value_getter = null )
  {
    $this->key = $key;
    $this->type = $type;
    $this->is_required = $is_required;
    $this->default_value_getter = $default_value_getter;
  }

  public function get_key() : string
  {
    return $this->key;
  }

  private function set_key( string $key ) : void
  {
    $this->key = $key;
  }

  public function get_type() : string
  {
    return $this->type;
  }

  private function set_type( string $type ) : void
  {
    $supported_types = [
      self::TYPE_STR,
      self::TYPE_INT,
      self::TYPE_FLOAT,
      self::TYPE_BOOL,
      self::TYPE_ARR,
      self::TYPE_OBJ,
      self::TYPE_OBJ,
    ];

    if ( in_array( $type, $supported_types ) )
    {
      $this->throw_validation_error(sprintf(
        'Unexpcted type: "%s"! Use %s::TYPE_* constants instead of writing type manually.',
        $type,
        self::class,
      ));
    }

    $this->type = $type;
  }

  public function get_is_required() : bool
  {
    return $this->is_required;
  }

  private function set_is_required( bool $is_required ) : void
  {
    $this->is_required = $is_required;
  }

  public function get_default_value_getter() : callable | null
  {
    return call_user_func( $this->default_value_getter );
  }

  private function set_default_value_getter( mixed $default_value_getter ) : void
  {
    if ( $this->is_required && isset( $default_value_getter ) )
    {
      $this->throw_validation_error(
        '$default_value_getter value must not be passed if $is_required is equal to true.'
      );
    }

    $this->default_value_getter = $default_value_getter;
  }

  private function throw_validation_error( string $msg ) : void
  {
    throw new Exception(sprintf( "Argument scheme is invalid - %s! %s",
      $this->key,
      $msg
    ));
  }
}