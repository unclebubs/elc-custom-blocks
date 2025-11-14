<?php



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');

// use Yoast\PHPUnitPolyfills\TestCases\TestCase;
// 



class PodcastCategoryListsTest extends WP_UnitTestCase
{

  protected $parent_term_id = 62;
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

    $post3_id = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 3',
      'post_type' => 'podcast',
      'post_status' => 'publish',
      'post_date' => '2025-04-03 12:00:00', // Local time
    ]);
    wp_set_object_terms($post3_id, [$this->parent_term_id, $this->child2_term_id], 'podcast-category');



    $post1_id = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 1',
      'post_excerpt' => 'Post Excerpt',
      'post_type' => 'podcast',
      'post_status' => 'publish',
      'post_date' => '2025-01-03 12:00:00', // Local time
    ]);

    wp_set_object_terms($post1_id, [$this->parent_term_id, $this->child1_term_id], 'podcast-category');

    $post2_id = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 2',
      'post_type' => 'podcast',
      'post_status' => 'publish',
      'post_date' => '2025-01-03 12:00:00', // Local time
    ]);
    wp_set_object_terms($post2_id, [$this->parent_term_id, $this->child1_term_id], 'podcast-category');

    $post4_id = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 4',
      'post_type' => 'podcast',
      'post_status' => 'publish',
      'post_date' => '2025-05-03 12:00:00', // Local time
    ]);
    wp_set_object_terms($post4_id, [$this->parent_term_id, $this->child1_term_id], 'podcast-category');

    $post5_id = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 5',
      'post_type' => 'podcast',
      'post_status' => 'publish',
      'post_date' => '2023-01-03 12:00:00', // Local time
    ]);
    wp_set_object_terms($post5_id, [$this->parent_term_id, $this->child1_term_id], 'podcast-category');

    $post6_id = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 6',
      'post_type' => 'podcast',
      'post_status' => 'publish',
      'post_date' => '2024-01-03 12:00:00', // Local time
    ]);
    wp_set_object_terms($post6_id, [$this->parent_term_id, $this->child1_term_id], 'podcast-category');
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms
  }


  public function test_all_pocasts_listed_from_first_category()
  {
    global $wp_query;
    // Create a new WP_Query instance
    $wp_query = new WP_Query();
    $wp_query->is_category = true; // Simulate a category query

    // Retrieve the term as a WP_Term object
    $wp_query->queried_object = get_term($this->child1_term_id, 'podcast-category');
    // Call the function
    $output = render_all_podcast_listings([], '');

    $count = substr_count($output, 'data-year');
    // Assert the output contains the default title
    $this->assertEquals(4, $count, 'The string "data-year" should occur six times');
  }

  public function test_podcast_list_year()
  {
    global $wp_query;
    set_query_var('y', 2023);
    // Retrieve the term as a WP_Term object
    $wp_query->queried_object = get_term($this->child1_term_id, 'podcast-category');
    $output = render_all_podcast_listings([], '');

    $count = substr_count($output, 'data-year');
    // Assert the output contains the default title
    $this->assertEquals(1, $count, 'The string "data-year" should occur once');
    $this->assertStringContainsString('Dummy Podcast Post 5', $output);
  }
}
