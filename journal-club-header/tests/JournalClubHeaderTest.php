<?php

use function PHPUnit\Framework\assertFalse;



//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');


class JournalClubHeaderTest extends WP_UnitTestCase
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


    $this->post_id = $this->factory->post->create([
      'post_title' => 'Journal Club Post 1',
      'post_type' => 'journal-club',
      'post_status' => 'publish',
      'post_date' => '2025-01-03 12:00:00', // Local time
    ]);

    $attachment_id = $this->factory->attachment->create_upload_object(TEMPLATE_DIR . '/assets/img/testing.png');

    // Set the attachment as the featured image
    set_post_thumbnail($this->post_id, $attachment_id);


    update_field('start_date', '20240610', $this->post_id,);
    update_field('start_time', '15:30', $this->post_id,);
    update_field('end_time', '14:31', $this->post_id,);
    update_field('webinar_link', 'https://webinar.link', $this->post_id,);
  }

  protected function tearDown(): void
  {
    // Optionally clean up custom data here (e.g., non-standard tables or settings)

    parent::tearDown(); // This will clean up factory-created posts and terms
  }



  public function test_journal_club_header()
  {
    global $post;
    $post = get_post($this->post_id);
    setup_postdata($post);

    $output = render_journal_club_header([], '');

    // Assert the output contains the default title
    $this->assertStringContainsString('<h1 class="card-title ">Journal Club Post 1</h1>', $output, "The correct title should be output");
    $this->assertStringContainsString('June 10th, 2024 | 15:30 - 14:31', $output, "The correct title should be output");
    $this->assertStringContainsString('href="https://webinar.link"', $output, "The webinar link should be output");
    $this->assertFalse(strpos($output, 'href="#inline-video"'), "The watch video link should not be output by default");
  }

  public function test_journal_club_header_attributes()
  {
    global $post;
    $post = get_post($this->post_id);
    setup_postdata($post);

    $output = render_journal_club_header(['showRegisterButton' => false, 'showWatchNowButton' => true], '');

    // Assert the output contains the default title
    $this->assertStringContainsString('<h1 class="card-title ">Journal Club Post 1</h1>', $output, "The correct title should be output");
    $this->assertStringContainsString('June 10th, 2024 | 15:30 - 14:31', $output, "The correct title should be output");
    $this->assertFalse(strpos($output, 'href="https://webinar.link"'), "The webinar link should not be output");
    $this->assertStringContainsString('href="#inline-video"', $output, "The play inline video link should be output");
  }
}
