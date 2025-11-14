<?php

use function PHPUnit\Framework\assertFalse;



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');


class LatestJournalClubNoContentTest extends WP_UnitTestCase
{

  protected $post_id;
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
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms
  }



  public function test_journal_club_no_posts()
  {


    $output = render_latest_journal_club([], '');

    // Assert the output contains the default title
    $this->assertStringContainsString('No content', $output, "When there are no posts 'No content' should be output");
  }
}
