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
      // todo: create package for exactly my package(theme/plugin) boilerplate (extend WP\Assets\...) and use it here.
      $entry_point_name = 'jobs-filter';

      $assets_rel_path = "/assets/dist/$entry_point_name/";

      $assets_abs_path = get_stylesheet_directory() . $assets_rel_path;
      $assets_url = get_stylesheet_directory_uri() . $assets_rel_path;

      $css_url = $assets_url . 'index.css';
      $css_ver = filemtime( $assets_abs_path . 'index.css' );

      $css_url = $assets_url . 'index.css';
      $css_ver = filemtime( $assets_abs_path . 'index.css' );

      $js_url = $assets_url . 'index.dev.js';
      // todo: handle prod as well
      $js_ver = filemtime( $assets_abs_path . 'index.dev.js' );

      wp_enqueue_style( $entry_point_name, $css_url, [], $css_ver );
      wp_enqueue_script( $entry_point_name, $js_url, [], $js_ver );
    }
  }
}