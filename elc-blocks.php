<?php

/**
 * Plugin Name: ELC Custom Blocks
 * Plugin URI: https://github.com/unclebubs/elc-theme
 * Description: Custom WordPress blocks for the ELC website including podcast listings, journal club content, navigation, and more.
 * Version: 1.0.0
 * Author: ELC Team
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: elc-blocks
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 8.0
 *
 * @package ELCBlocks
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}

// Define plugin constants
define('ELC_BLOCKS_VERSION', '1.0.0');
define('ELC_BLOCKS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ELC_BLOCKS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ELC_BLOCKS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class ELCBlocks
{

  /**
   * Instance of this class
   */
  private static $instance = null;

  /**
   * Available blocks
   */
  private $blocks = [
    'account-login',
    'bootstrap-pagination',
    'elc-papers-tab-navigation',
    'journal-club-listings',
    'journal-year-navigation',
    'latest-journal-club-content',
    'most-recent-podcast',
    'most-viewed-podcast',
    'news-carousel',
    'podcast-audio-player',
    'podcast-category-cards',
    'podcast-category-info',
    'podcast-listing',
    'podcast-search-form',
    'podcast-year-navigation',
    'summit-register-interest-buttons'
  ];

  /**
   * Get singleton instance
   */
  public static function get_instance()
  {
    if (null === self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Constructor
   */
  private function __construct()
  {
    add_action('init', [$this, 'init']);
    add_action('enqueue_block_assets', [$this, 'enqueue_block_assets']);

    // Plugin lifecycle hooks
    register_activation_hook(__FILE__, [$this, 'activate']);
    register_deactivation_hook(__FILE__, [$this, 'deactivate']);
  }

  /**
   * Initialize the plugin
   */
  public function init()
  {
    // Load text domain
    load_plugin_textdomain('elc-blocks', false, dirname(ELC_BLOCKS_PLUGIN_BASENAME) . '/languages');

    // Register blocks
    $this->register_blocks();

    // Handle legacy block support
    add_filter('render_block', [$this, 'handle_legacy_blocks'], 10, 2);

    // Log unregistered blocks for debugging
    add_action('init', [$this, 'debug_registered_blocks'], 20);
  }

  /**
   * Register all blocks
   */
  public function register_blocks()
  {
    $registered_blocks = [];
    $failed_blocks = [];

    foreach ($this->blocks as $block_name) {
      $block_dir = ELC_BLOCKS_PLUGIN_DIR . $block_name;

      // Check if block directory exists
      if (is_dir($block_dir)) {
        // Try direct build structure first
        $block_json_path = $block_dir . '/build/block.json';
        $render_php_path = $block_dir . '/build/render.php';
        $build_dir = $block_dir . '/build';

        // If direct structure doesn't exist, try nested structure
        if (!file_exists($block_json_path)) {
          $nested_block_json_path = $block_dir . '/build/' . $block_name . '/block.json';
          $nested_render_php_path = $block_dir . '/build/' . $block_name . '/render.php';
          $nested_build_dir = $block_dir . '/build/' . $block_name;

          if (file_exists($nested_block_json_path)) {
            $block_json_path = $nested_block_json_path;
            $render_php_path = $nested_render_php_path;
            $build_dir = $nested_build_dir;
          }
        }

        if (file_exists($block_json_path)) {
          // Register block - use render.php if it exists in build directory
          $args = [];
          if (file_exists($render_php_path)) {
            // Use the built render.php file instead of the main PHP file
            $args['render_callback'] = function ($attributes, $content, $block) use ($render_php_path, $block_name) {
              return $this->render_block_safe($render_php_path, $attributes, $content, $block, $block_name);
            };
          }

          $result = register_block_type($build_dir, $args);

          if ($result) {
            $registered_blocks[] = $block_name;
          } else {
            $failed_blocks[] = $block_name;
          }
        } else {
          $failed_blocks[] = $block_name . ' (no block.json)';
        }
      } else {
        $failed_blocks[] = $block_name . ' (no directory)';
      }
    }

    // Store results for debugging
    update_option('elc_blocks_registered', $registered_blocks);
    update_option('elc_blocks_failed', $failed_blocks);
  }

  /**
   * Enqueue block assets
   */
  public function enqueue_block_assets()
  {
    // Enqueue any global styles or scripts here if needed
  }



  /**
   * Handle legacy blocks that might have different namespaces
   */
  public function handle_legacy_blocks($block_content, $block)
  {
    // Check if this is an unregistered block that we might handle
    if (empty($block_content) && isset($block['blockName'])) {
      $block_name = $block['blockName'];

      // Extract the block name from different possible namespaces
      if (strpos($block_name, 'elc/') === 0) {
        $clean_name = str_replace('elc/', '', $block_name);

        // Check if we have a corresponding block in our plugin
        if (in_array($clean_name, $this->blocks)) {
          $php_file = ELC_BLOCKS_PLUGIN_DIR . $clean_name . '/' . $clean_name . '.php';

          if (file_exists($php_file)) {
            // Extract attributes
            $attributes = isset($block['attrs']) ? $block['attrs'] : [];

            // Include the PHP file and capture output
            ob_start();
            include $php_file;
            return ob_get_clean();
          }
        }
      }
    }

    return $block_content;
  }

  /**
   * Debug function to show all registered blocks
   */
  public function debug_registered_blocks()
  {
    if (current_user_can('manage_options')) {
      $all_registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
      $elc_blocks = array_filter(array_keys($all_registered_blocks), function ($block_name) {
        return strpos($block_name, 'elc/') === 0;
      });

      update_option('elc_blocks_all_registered', $elc_blocks);
    }
  }

  /**
   * Plugin activation
   */
  public function activate()
  {
    // Flush rewrite rules
    flush_rewrite_rules();

    // Add any activation logic here
    add_option('elc_blocks_version', ELC_BLOCKS_VERSION);
  }

  /**
   * Plugin deactivation
   */
  public function deactivate()
  {
    // Flush rewrite rules
    flush_rewrite_rules();

    // Add any deactivation logic here
  }

  /**
   * Safely render a block using its render.php file
   */
  public function render_block_safe($render_file, $attributes, $content, $block, $block_name)
  {
    if (!file_exists($render_file)) {
      return $content;
    }

    // Create isolated scope for the render file
    $render_block = function () use ($render_file, $attributes, $content, $block) {
      ob_start();
      include $render_file;
      return ob_get_clean();
    };

    return $render_block();
  }

  /**
   * Dynamic render callback loader (kept for backward compatibility)
   * This method dynamically loads the appropriate render file for each block
   */
  public function __call($method, $args)
  {
    if (strpos($method, 'render_block_') === 0) {
      $block_name = str_replace('render_block_', '', $method);
      $block_name = str_replace('_', '-', $block_name);

      $render_file = ELC_BLOCKS_PLUGIN_DIR . $block_name . '/build/render.php';

      if (file_exists($render_file)) {
        // Extract attributes and content from args
        $attributes = isset($args[0]) ? $args[0] : [];
        $content = isset($args[1]) ? $args[1] : '';
        $block = isset($args[2]) ? $args[2] : null;

        return $this->render_block_safe($render_file, $attributes, $content, $block, $block_name);
      }
    }

    return '';
  }
}

/**
 * Initialize the plugin
 */
function elc_blocks_init()
{
  return ELCBlocks::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'elc_blocks_init');

/**
 * Helper function to get plugin instance
 */
function elc_blocks()
{
  return ELCBlocks::get_instance();
}
