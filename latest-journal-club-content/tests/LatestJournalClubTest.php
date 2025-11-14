<?php

use function PHPUnit\Framework\assertFalse;



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');


class LatestJournalClubTest extends WP_UnitTestCase
{

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


    $post1_id = $this->factory->post->create([
      'post_title' => 'Journal Club Post 1',
      'post_content' => 'Journal Club Content 1',
      'post_type' => 'journal-club',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
    update_field('start_date', '20250101', $post1_id,);

    $post2_id  = $this->factory->post->create([
      'post_title' => 'Journal Club Post 2',
      'post_content' => 'Journal Club Content 2',
      'post_type' => 'journal-club',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
    update_field('start_date', '20250101', $post2_id,);

    $post3_id = $this->factory->post->create([
      'post_title' => 'Journal Club Post 3',
      'post_content' => 'Journal Club Content 3',
      'post_type' => 'journal-club',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
    update_field('start_date', $this->get_tomorrow_date_string(), $post3_id,);

    $post4_id  = $this->factory->post->create([
      'post_title' => 'Journal Club Post 4',
      'post_content' => 'Journal Club Content 4',
      'post_type' => 'journal-club',
      'post_status' => 'publish',
      'post_date' => '2025-01-01 12:00:00', // Local time
    ]);
    update_field('start_date', $this->get_tomorrow_next_year_date_string(), $post4_id,);
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms
  }



  public function test_journal_club_header()
  {


    $output = render_latest_journal_club([], '');

    // Assert the output contains the default title
    $this->assertStringContainsString('Journal Club Content 3', $output, "The correct title 'Journal Club Content 3' should be output");
  }

  private function get_tomorrow_date_string()
  {
    $date = new DateTime();
    $date->modify('+1 day');
    $tomorrow = $date->format('Ymd');

    return $tomorrow;
  }

  private function get_tomorrow_next_year_date_string()
  {
    $date = new DateTime();
    $date->modify('+1 day');
    $date->modify('+1 year');
    $tomorrow = $date->format('Ymd');

    return $tomorrow;
  }
}
