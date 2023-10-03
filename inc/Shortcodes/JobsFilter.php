<?php
namespace Pjs\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) exit;

class JobsFilter extends \Cvy\DesignPatterns\Singleton
{
  protected function __construct()
  {
    add_shortcode( 'pjs_jobs_filter', fn() => $this->get_content() );
  }

  private function get_content() : string
  {
    ob_start();

    $this->render_content();

    $output = ob_get_contents();

    ob_end_clean();

    return $output;
  }

  private function render_content() : void
  {
    require_once PJS_TEMPLATES_PATH . 'jobs-filter/jobs-filter.php';
  }
}