<?php



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');

// use Yoast\PHPUnitPolyfills\TestCases\TestCase;
// 



class PodcastCategoryInfoTest extends WP_UnitTestCase
{

  protected $parent_term_id = 62;
  protected $child_term_id;
  protected $attachment_id;

  protected function setUp(): void
  {
    parent::setUp();
    require_once dirname(__FILE__) . '../../src/render.php';

    activate_plugin('advanced-custom-fields/acf.php');

    global $wpdb;

    // Register taxonomy if not already registered
    register_taxonomy('podcast-category', 'post', [
      'hierarchical' => true,
      'labels' => ['name' => 'Podcast Categories'],
    ]);


    $parent_id = $this->factory->term->create([
      'taxonomy' => 'podcast-category',
      'name' => 'Parent Category',
      'slug' => 'podcast'
    ]);

    $wpdb->update(
      $wpdb->terms,
      ['term_id' => $this->parent_term_id],
      ['term_id' => $parent_id]
    );

    $wpdb->update(
      $wpdb->term_taxonomy,
      ['term_id' => $this->parent_term_id],
      ['term_id' => $parent_id]
    );

    $child1_id = $this->factory->term->create([
      'taxonomy' => 'podcast-category',
      'name' => 'Child Category 1',
      'description' => 'Description 1',
      'parent' => $this->parent_term_id,
    ]);
    update_field('order', 10, 'term_ ' .  $child1_id);

    $this->child_term_id = $this->factory->term->create([
      'taxonomy' => 'podcast-category',
      'name' => 'Child Category',
      'description' => 'Description',
      'parent' => $this->parent_term_id,
    ]);
    // Add ACF field data to the term
    update_field('order', 1, 'term_ ' .  $this->child_term_id);

    $this->attachment_id = $this->factory->attachment->create_upload_object(TEMPLATE_DIR . '/assets/img/testing.png');
    update_field('image', $this->attachment_id, 'term_'  .  $this->child_term_id);

    $child2_id = $this->factory->term->create([
      'taxonomy' => 'podcast-category',
      'name' => 'Child Category 2',
      'description' => 'Description 2',
      'parent' => $this->parent_term_id,
    ]);
    update_field('order', 11, 'term_ ' .  $child2_id);

    // add posts
    $post1_id = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 3',
      'post_type' => 'post',
      'post_status' => 'publish',
      'post_date' => '2025-01-03 12:00:00', // Local time
    ]);

    wp_set_object_terms($post1_id, [$this->parent_term_id, $child1_id], 'podcast-category');
    wp_set_object_terms($post1_id, [$this->parent_term_id, $child2_id], 'podcast-category');
    wp_set_object_terms($post1_id, [$this->parent_term_id, $this->child_term_id], 'podcast-category');





    // Simulate the queried object
    global $wp_query;
    $wp_query = new WP_Query();
    $wp_query->queried_object = get_term($this->parent_term_id);
    $wp_query->queried_object_id = $this->parent_term_id;
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms
  }


  public function test_block_renders_with_content()
  {

    //mock the attributes
    $attributes = ['userContent' => 'RichText'];
    // Call the function
    $output = render($attributes, '');

    // test the acf field
    $acf_order = get_field('order', 'term_' . $this->child_term_id);

    // Assert the ACF field value is as expected
    $this->assertEquals(1, $acf_order);

    // Assert the output contains the default title
    $this->assertStringContainsString('Child Category', $output);
    $this->assertStringContainsString('Description', $output);
    $this->assertStringContainsString('testing', $output);
    $this->assertStringContainsString('RichText', $output);
  }
}
