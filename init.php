<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/vendor/autoload.php';

define( 'PJS_ROOT_PATH', __DIR__ . '/' );
define( 'PJS_TEMPLATES_PATH', PJS_ROOT_PATH . 'templates/' );
define( 'PJS_ASSETS_PATH', PJS_ROOT_PATH . 'assets/' );
define( 'PJS_JS_PATH', PJS_ASSETS_PATH . 'js/dist/' );
define( 'PJS_CSS_PATH', PJS_ASSETS_PATH . 'css/' );

\Pjs\Shortcodes\JobsFilter::get_instance();

Pjs\RestApi\FrontEnd\JobsFilter\Search::get_instance();