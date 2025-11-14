<?php




//define('TEMPLATE_DIR', '/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme');





class SummitButtonsTest extends WP_UnitTestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // Include the render file
    require_once dirname(__FILE__) . '../../src/render.php';

    // Create custom table for event registrations
    global $wpdb;
    $wpdb->query("CREATE TABLE {$wpdb->prefix}event_registrations (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            event_id BIGINT(20) UNSIGNED NOT NULL
        )");
  }

  protected function tearDown(): void
  {
    parent::tearDown();

    // Clean up custom table
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}event_registrations");
  }

  public function test_logged_out_user_sees_login_button()
  {
    // Ensure user is logged out
    wp_set_current_user(0);

    // Simulate rendering the block
    $output = render_buttons([], '');

    // Assert that the output contains the Login button
    $this->assertStringContainsString('class="btn">Login</a>', $output, 'Logged-out users should see a Login button.');
  }

  public function test_logged_in_user_sees_register_button()
  {
    // Create a test user and log them in
    $user_id = self::factory()->user->create();
    wp_set_current_user($user_id);

    // Create a summit post
    $post_id = self::factory()->post->create([
      'post_type' => 'summit',
    ]);
    global $post;
    $post = get_post($post_id);
    setup_postdata($post);

    // Simulate rendering the block
    $output = render_buttons([], '');

    // Assert that the output contains the Register Interest button
    $this->assertStringContainsString('class="btn register-interest"', $output, 'Logged-in users should see a Register Interest button if not registered.');

    wp_reset_postdata();
  }

  public function test_registered_user_sees_read_more_button()
  {
    global $wpdb;

    // Create a test user and log them in
    $user_id = self::factory()->user->create();
    wp_set_current_user($user_id);

    // Create a summit post
    $post_id = self::factory()->post->create([
      'post_type' => 'summit',
    ]);
    global $post;
    $post = get_post($post_id);
    setup_postdata($post);

    // Add the user to the event_registrations table
    $wpdb->insert(
      "{$wpdb->prefix}event_registrations",
      [
        'user_id' => $user_id,
        'event_id' => $post_id,
      ]
    );

    // Simulate rendering the block
    $output = render_buttons([], '');

    // Assert that the output contains the Read More button
    $this->assertStringContainsString('Read More', $output, 'Registered users should see a Read More button.');

    wp_reset_postdata();
  }
}
