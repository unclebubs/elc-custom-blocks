<?php



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');

// use Yoast\PHPUnitPolyfills\TestCases\TestCase;
// 



class MostViwedPodcastTest extends WP_UnitTestCase
{



  protected function setUp(): void
  {

    parent::setUp();
    require_once dirname(__FILE__) . '../../src/render.php';
    global $wpdb;

    activate_plugin('post-views-counter/post-views-counter.php');
    $views_table = $wpdb->prefix . 'post_views';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $views_table (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        type BIGINT(20) NOT NULL,
        period VARCHAR(20) NOT NULL,
        id BIGINT(20) UNSIGNED NOT NULL,
        count BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

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
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms
  }

  public function test_block_renders_when_no_podcasts()
  {

    $output = render_most_viewed_podcast([], '');

    $this->assertStringContainsString('No most viewed podcast found.', $output);
  }

  public function test_block_renders_with_content()
  {

    global $wpdb;

    $views_table = $wpdb->prefix . 'post_views';

    $post1_id = $this->factory->post->create([
      'post_title' => 'Podcast Post 1',
      'post_type' => 'podcast',
      'post_status' => 'publish',
      'post_date' => '2025-01-03 12:00:00', // Local time
    ]);

    $wpdb->insert(
      $views_table,
      [
        'type' => 4,
        'period' => 'total',
        'id' => $post1_id,
        'count' => 10,
      ]
    );

    $post2_id = $this->factory->post->create([
      'post_title' => 'Podcast Post 2',
      'post_type' => 'podcast',
      'post_excerpt' => 'Post Excerpt',
      'post_status' => 'publish',
      'post_date' => '2025-01-03 12:00:00', // Local time
    ]);
    $wpdb->insert(
      $views_table,
      [
        'type' => 4,
        'period' => 'total',
        'id' => $post2_id,
        'count' => 100,
      ]
    );

    $post3_id = $this->factory->post->create([
      'post_title' => 'Podcast Post 3',
      'post_type' => 'podcast',
      'post_status' => 'publish',
      'post_date' => '2025-01-03 12:00:00', // Local time
    ]);
    $wpdb->insert(
      $views_table,
      [
        'type' => 4,
        'period' => 'total',
        'id' => $post3_id,
        'count' => 11,
      ]
    );

    $output = render_most_viewed_podcast([], '');

    $this->assertStringContainsString('Most Popular Podcast', $output);
    $this->assertStringContainsString('Podcast Post 2', $output);
    $this->assertStringContainsString('Post Excerpt', $output);
  }
}
