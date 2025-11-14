<?php



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');

// use Yoast\PHPUnitPolyfills\TestCases\TestCase;
// 



class PodcastNoListsTest extends WP_UnitTestCase
{

  protected $parent_term_id = 66;
  protected $child1_term_id;
  protected $child2_term_id;

  protected function setUp(): void
  {
    parent::setUp();
    require_once dirname(__FILE__) . '../../src/render.php';
    activate_plugin('advanced-custom-fields/acf.php');

    global $wpdb;

    // Register the podcast post type
    register_post_type('podcast', [
      'labels' => [
        'name' => 'Podcasts',
        'singular_name' => 'Podcast',
      ],
      'public' => true,
      'has_archive' => true,
      'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
    ]);

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


    $this->child1_term_id = $this->factory->term->create([
      'taxonomy' => 'podcast-category',
      'name' => 'Child Category 1',
      'description' => 'Description 1',
      'parent' => $this->parent_term_id,
    ]);
    update_field('order', 10, 'term_ ' .  $this->child1_term_id);

    $this->child2_term_id = $this->factory->term->create([
      'taxonomy' => 'podcast-category',
      'name' => 'Child Category 2',
      'description' => 'Description 2',
      'parent' => $this->parent_term_id,
    ]);
    update_field('order', 12, 'term_ ' .  $this->child2_term_id);

    $post1_id = $this->factory->post->create([
      'post_title' => 'Dummy Post 1',
      'post_type' => 'post',
      'post_status' => 'publish',
      'post_date' => '2025-01-03 12:00:00', // Local time
    ]);
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms


  }

  public function test_no_pocasts_listed()
  {
    // Call the function
    $output = render_all_podcast_listings([], '');

    // Assert the output contains the default title
    $this->assertStringContainsString('No podcasts found in the specified subcategory.', $output);
  }
}
