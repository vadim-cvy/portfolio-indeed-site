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
      $this->enqueue_css();
      $this->enqueue_js();
    }
  }

  private function enqueue_css() : void
  {
    $file_name = 'index.min.css';

    $url = $this->get_asset_url( 'css', $file_name );
    $ver = filemtime( $this->get_asset_path( 'css', $file_name ) );

    wp_enqueue_style( 'pjs-jobs-filter', $url, [], $ver );
  }

  private function enqueue_js() : void
  {
    $vue_handle = 'pjs-vue';

    wp_enqueue_script( $vue_handle, 'https://unpkg.com/vue@3.3.7/dist/vue.global.js', [], null, [
      'in_footer' => true,
    ]);

    $main_handle = 'pjs-jobs-filter';

    $file_name = 'index.dev.js';

    $url = $this->get_asset_url( 'js', $file_name );
    $ver = filemtime( $this->get_asset_path( 'js', $file_name ) );

    wp_enqueue_script( $main_handle, $url, [ $vue_handle ], $ver, [
      'in_footer' => true,
    ]);

    wp_localize_script( $main_handle, 'pjsJobsFilter', [
      // todo: get dynamically
      'countryId' => 1,
    ]);

    wp_enqueue_script( 'pjs-swal-2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.all.min.js', [], null , [
      'in_footer' => true,
    ]);
  }

  private function get_asset_url( string $asset_type, string $file_name ) : string
  {
    return get_stylesheet_directory_uri() . $this->get_asset_rel_path( $asset_type, $file_name );
  }

  private function get_asset_path( string $asset_type, string $file_name ) : string
  {
    return get_stylesheet_directory() . $this->get_asset_rel_path( $asset_type, $file_name );
  }

  private function get_asset_rel_path( string $asset_type, string $file_name ) : string
  {
    return "/assets/dist/$asset_type/jobs-filter/$file_name";
  }
}