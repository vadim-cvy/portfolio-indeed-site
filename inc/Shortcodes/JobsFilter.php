<?php
namespace Pjs\Shortcodes;
use \Pjs\Pages\JobsFilter as JobsFilterPage;

if ( ! defined( 'ABSPATH' ) ) exit;

class JobsFilter extends \Cvy\DesignPatterns\Singleton
{
  protected function __construct()
  {
    add_shortcode( 'pjs_jobs_filter', fn() => $this->get_content() );

    add_action( 'wp_enqueue_scripts', fn() => $this->enqueue_assets() );
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

  private function enqueue_assets() : void
  {
    if ( JobsFilterPage::is_current() )
    {
      $assets_abs_path = get_stylesheet_directory() . '/assets/dist/';
      $assets_url = get_stylesheet_directory_uri() . '/assets/dist/';

      $css_file_rel_path = 'css/jobs-filter/index.min.css';
      $js_file_rel_path = 'js/jobs-filter/index.dev.js';

      $css_url = $assets_url . $css_file_rel_path;
      $css_ver = filemtime( $assets_abs_path . $css_file_rel_path );

      $js_url = $assets_url . $js_file_rel_path;
      $js_ver = filemtime( $assets_abs_path . $js_file_rel_path );

      wp_enqueue_style( 'jobs-filter', $css_url, [], $css_ver );
      wp_enqueue_script( 'jobs-filter', $js_url, [], $js_ver );
    }
  }
}