<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'JLX_ROOT_PATH', __DIR__ . '/' );
define( 'JLX_ASSETS_PATH', JLX_ROOT_PATH . 'assets/' );
define( 'JLX_JS_PATH', JLX_ASSETS_PATH . 'js/dist/' );
define( 'JLX_CSS_PATH', JLX_ASSETS_PATH . 'css/' );

\Jlx\Example::do_something();