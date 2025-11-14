<?php



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');

// use Yoast\PHPUnitPolyfills\TestCases\TestCase;
// 



class PodcastCategoryNoCategoriesTest extends WP_UnitTestCase
{

  protected $parent_term_id = 62;
  protected $child_term_id;
  protected $attachment_id;

  protected function setUp(): void
  {
    parent::setUp();
    require_once dirname(__FILE__) . '../../src/render.php';

    activate_plugin('advanced-custom-fields/acf.php');


    // Register taxonomy if not already registered
    register_taxonomy('podcast-category', 'post', [
      'hierarchical' => true,
      'labels' => ['name' => 'Podcast Categories'],
    ]);

    // Simulate the queried object
    global $wp_query;
    $wp_query = new WP_Query();
    $wp_query->queried_object = get_term($this->child_term_id);
    $wp_query->queried_object_id = $this->child_term_id;
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms
  }

  public function test_block_renders_when_empty()
  {
    $output = render([], '');

    $this->assertStringContainsString('No child categories available.', $output);
  }
}
