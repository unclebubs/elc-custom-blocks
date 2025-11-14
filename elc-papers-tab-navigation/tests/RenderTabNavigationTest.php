<?php







class RenderTabNavigationTest extends WP_UnitTestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    require_once dirname(__FILE__) . '../../src/render.php';

    // Register a custom taxonomy for testing
    register_taxonomy('paper-category', 'post', [
      'hierarchical' => true,
      'public'       => true,
    ]);
  }

  protected function tearDown(): void
  {
    parent::tearDown();

    // Unregister the taxonomy after each test
    unregister_taxonomy('paper-category');
  }

  public function test_render_with_categories()
  {
    // Create categories in the custom taxonomy
    $parent_cat = self::factory()->term->create([
      'taxonomy' => 'paper-category',
      'name'     => 'Parent Category',
    ]);

    $child_cat = self::factory()->term->create([
      'taxonomy' => 'paper-category',
      'name'     => 'Child Category',
      'parent'   => $parent_cat,
    ]);

    global $wp_query;

    // Reset the global query
    wp_reset_query();

    // Simulate a taxonomy query
    $wp_query = new WP_Query([
      'taxonomy' => 'paper-category',
      'term'     => get_term_field('slug', $parent_cat, 'paper-category'),
    ]);

    // Generate the navigation output
    $output = render_tab_navigation([], '');

    // Validate that the parent and child categories appear in the rendered output
    $this->assertStringContainsString(
      'Parent Category',
      $output,
      'The parent category should appear in the output.'
    );
    $this->assertStringContainsString(
      'Child Category',
      $output,
      'The child category should appear in the output.'
    );

    // Validate the HTML structure of the navigation output
    $this->assertStringContainsString(
      '<ul class="nav nav-tabs">',
      $output,
      'The navigation list is missing.'
    );
    $this->assertStringContainsString(
      '<li class="nav-item">',
      $output,
      'The list item structure is missing.'
    );

    // Clean up after the test
    wp_reset_query();
  }


  public function test_active_category()
  {
    // Create a category in the custom taxonomy
    $category_id = self::factory()->term->create([
      'taxonomy' => 'paper-category',
      'name'     => 'Active Category',
    ]);

    global $wp_query;

    // Reset the global query
    wp_reset_query();

    // Simulate a taxonomy query
    $wp_query = new WP_Query([
      'taxonomy' => 'paper-category',
      'term'     => get_term_field('slug', $category_id, 'paper-category'),
    ]);


    // Generate the navigation output
    $output = render_tab_navigation([], '');

    // Assert that the active class is applied to the current category
    $this->assertStringContainsString('class="nav-link active"', $output, 'The active class should be applied to the current category.');
  }


  public function test_no_categories()
  {
    // Ensure no categories exist
    $terms = get_terms([
      'taxonomy'   => 'paper-category',
      'hide_empty' => false,
    ]);

    $this->assertEmpty($terms, 'No categories should exist.');

    $output = render_tab_navigation([], '');

    // Assert that no navigation is rendered
    $this->assertEmpty($output, 'The output should be empty when no categories exist.');
  }

  public function test_handle_wp_error()
  {
    // Mock get_terms to return a WP_Error
    add_filter('pre_get_terms', function () {
      return new WP_Error('mock_error', 'Mock error occurred');
    });

    $output = render_tab_navigation([], '');

    // Assert that no navigation is rendered
    $this->assertEmpty($output, 'The output should be empty when get_terms returns a WP_Error.');
  }
}
