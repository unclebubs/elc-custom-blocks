<?php



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');

// use Yoast\PHPUnitPolyfills\TestCases\TestCase;
// 



class RenderRecentPodcastBlockTest extends WP_UnitTestCase
{

  protected $parent_term_id = 62;
  protected $child_term_id;

  protected function setUp(): void
  {
    parent::setUp();
    require_once dirname(__FILE__) . '../../src/render.php';


    global $wpdb;

    // Register taxonomy if not already registered
    register_taxonomy('podcast-category', 'post', [
      'hierarchical' => true,
      'labels' => ['name' => 'Podcast Categories'],
    ]);


    $parent_id = $this->factory->term->create([
      'taxonomy' => 'podcast-category',
      'name' => 'Parent Category',
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

    $this->child_term_id = $this->factory->term->create([
      'taxonomy' => 'podcast-category',
      'name' => 'Child Category 1',
      'parent' => $this->parent_term_id,
    ]);

    $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post',
      'post_type' => 'post',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms
  }

  public function test_block_renders_with_default_title()
  {
    // Call the function
    $output = render_recent_podcast_block([], '');

    // Assert the output contains the default title
    $this->assertStringContainsString('No recent podcast posts found.', $output);
  }

  public function test_block_renders_with_most_recent_podcast()
  {

    // a 2nd post with more recent post data
    $post_id_1 = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 1',
      'post_type' => 'post',
      'post_status' => 'publish',
      'post_date' => '2025-01-02 11:00:00', // Local time
    ]);

    // a 2nd post with more recent post data
    $post_id_2 = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 2',
      'post_type' => 'post',
      'post_status' => 'publish',
      'post_date' => '2025-01-02 12:00:00', // Local time
    ]);

    // a 3rd post with more recent post date but no terms
    $post_id_3 = $this->factory->post->create([
      'post_title' => 'Dummy Podcast Post 3',
      'post_type' => 'post',
      'post_status' => 'publish',
      'post_date' => '2025-01-03 12:00:00', // Local time
    ]);

    wp_set_object_terms($post_id_1, [$this->parent_term_id, $this->child_term_id], 'podcast-category');
    wp_set_object_terms($post_id_2, [$this->parent_term_id, $this->child_term_id], 'podcast-category');

    // Call the function
    $output = render_recent_podcast_block([], '');

    // Assert the output contains the default title
    $this->assertStringContainsString('Dummy Podcast Post 2', $output);
  }

  public function test_block_renders_with_correct_data()
  {



    // a post with all required data
    $post_id_5 = $this->factory->post->create([
      'post_title' => 'Title 1',
      'post_type' => 'post',
      'post_excerpt' => 'Excerpt 1',
      'post_status' => 'publish',
      'post_date' => '2025-01-05 12:00:00', // Local time
    ]);

    // Create an attachment (featured image)
    $attachment_id = $this->factory->attachment->create_upload_object(TEMPLATE_DIR . '/assets/img/testing.png');

    // Set the attachment as the featured image
    set_post_thumbnail($post_id_5, $attachment_id);
    wp_set_object_terms($post_id_5, [$this->parent_term_id], 'podcast-category');

    // Call the function
    $output = render_recent_podcast_block([], '');

    // Assert the output contains the default title
    $this->assertStringContainsString('Most Recent', $output);
    $this->assertStringContainsString('Title 1', $output);
    $this->assertStringContainsString('Excerpt 1', $output);
    $this->assertStringContainsString('bi bi-play-fill', $output);
    $this->assertStringContainsString('January 5, 2025', $output);
    $this->assertStringContainsString('/testing', $output);
  }
}
