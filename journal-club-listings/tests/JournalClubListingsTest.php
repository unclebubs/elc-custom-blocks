<?php

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertGreaterThan;



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');


class JournalClubListingsTest extends WP_UnitTestCase
{

  protected $post1_id, $post2_id, $post3_id, $post4_id, $post5_id;

  protected function setUp(): void
  {
    parent::setUp();
    require_once dirname(__FILE__) . '../../src/render.php';
    activate_plugin('advanced-custom-fields/acf.php');

    global $wpdb;

    // Register the podcast post type
    register_post_type('journal-club', [
      'labels' => [
        'name' => 'Journal Clucb',
        'singular_name' => 'Journal Club',
      ],
      'public' => true,
      'has_archive' => true,
      'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
    ]);


    $this->post1_id = $this->factory->post->create([
      'post_title' => 'Journal Club Post 1',
      'post_excerpt' => 'Journal Club Excerpt 1',
      'post_type' => 'journal-club',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
    update_field('start_date', '20200101', $this->post1_id,);
    update_field('start_time', '15:00', $this->post1_id,);

    $this->post2_id  = $this->factory->post->create([
      'post_title' => 'Journal Club Post 2',
      'post_excerpt' => 'Journal Club Excerpt 2',
      'post_type' => 'journal-club',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
    update_field('start_date', '20210101', $this->post2_id,);
    update_field('start_time', '15:00', $this->post2_id,);

    $this->post3_id = $this->factory->post->create([
      'post_title' => 'Journal Club Post 3',
      'post_excerpt' => 'Journal Club Excerpt 3',
      'post_type' => 'journal-club',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
    update_field('start_date', '20220101', $this->post3_id,);
    update_field('start_time', '15:00', $this->post3_id,);

    $this->post4_id  = $this->factory->post->create([
      'post_title' => 'Journal Club Post 4',
      'post_excerpt' => 'Journal Club Excerpt 4',
      'post_type' => 'journal-club',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
    update_field('start_date', '20230101', $this->post4_id,);
    update_field('start_time', '15:00', $this->post4_id,);

    $this->post5_id = $this->factory->post->create([
      'post_title' => ' Post 1',
      'post_excerpt' => 'Excerpt 1',
      'post_type' => 'post',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
    update_field('start_date', '20200101', $this->post5_id,);
    update_field('start_time', '15:00', $this->post5_id,);
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms
  }



  public function test_listings()
  {

    $output = render_listings([], '');
    $count = substr_count($output, 'data-year');
    $this->assertEquals(4, $count, 'There should be 4 listings');
    $pos1 = strpos($output, 'Journal Club Excerpt 4');
    $pos2 = strpos($output, 'Journal Club Excerpt 3');
    $pos3 = strpos($output, 'Journal Club Excerpt 2');
    $pos4 = strpos($output, 'Journal Club Excerpt 1');
    $this->assertGreaterThan($pos4, $pos3, 'The Journals should be ordered by date descending');
    $this->assertGreaterThan($pos3, $pos2, 'The Journals should be ordered by date descending');
    $this->assertGreaterThan($pos2, $pos1, 'The Journals should be ordered by date descending');
  }

  public function test_no_listings()
  {
    wp_delete_post($this->post1_id, true);
    wp_delete_post($this->post2_id, true);
    wp_delete_post($this->post3_id, true);
    wp_delete_post($this->post4_id, true);

    $output = render_listings([], '');

    $this->assertStringContainsString('No Journals found in the specified subcategory.', $output, "There should be no journals listed");
  }
}
