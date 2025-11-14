<?php







class RenderPaginationTest extends WP_UnitTestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    require_once dirname(__FILE__) . '../../src/render.php';
    global $wp_query;

    self::factory()->post->create_many(12, [
      'post_type' => 'podcast',
    ]);

    // Create mock query with pagination
    $wp_query = new WP_Query([
      'post_type'      => 'podcast',
      'posts_per_page' => 5,
      'paged'          => 1,
      'total'          => 10,
    ]);
  }

  protected function tearDown(): void
  {
    parent::tearDown();
    unset($GLOBALS['wp_query']);
  }

  public function test_pagination_links_generated()
  {
    global $wp_query;

    // Capture the output of the render_pagination function
    $output = render_pagination([], '');



    // Assert that the nav and ul elements exist
    $this->assertStringContainsString('<nav aria-label="Page navigation">', $output, 'The navigation wrapper is missing.');
    $this->assertStringContainsString('<ul class="search-pagination">', $output, 'The pagination list is missing.');

    // // Assert that at least one link is generated
    $this->assertStringContainsString('page-link', $output, 'The pagination links are missing or incorrectly formatted.');
  }

  public function test_bootstrap_classes_applied()
  {
    global $wp_query;

    // Capture the output of the render_pagination function
    $output = render_pagination([], '');

    // Assert that the active page has the correct class
    $this->assertStringContainsString('class="page-item active"', $output, 'The active page item is missing or incorrectly formatted.');

    // Assert that non-active pages have the page-item class
    $this->assertStringContainsString('class="page-item"', $output, 'Non-active page items are missing or incorrectly formatted.');
  }

  public function test_custom_anchor_modifications()
  {
    global $wp_query;

    // Capture the output of the render_pagination function
    $output = render_pagination([], '');

    // Assert that all links have #podcast-listings appended
    $this->assertMatchesRegularExpression('/href="[^"]+#podcast-listings"/', $output, 'The href attribute does not contain #podcast-listings.');
  }

  // public function test_html_structure()
  // {
  //   global $wp_query;

  //   // Capture the output of the render_pagination function
  //   $output = render_pagination([], '');


  //   // Assert that the structure includes nav, ul, and li
  //   $this->assertStringContainsString('<nav', $output, 'The nav element is missing.');
  //   $this->assertStringContainsString('<ul', $output, 'The ul element is missing.');
  //   $this->assertStringContainsString('<li', $output, 'The li elements are missing.');
  // }
}
