<?php





class LoginSectionTest extends WP_UnitTestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    activate_plugin('ultimate-member/ultimate-member.php');

    // Create mock pages for Ultimate Member core functionality
    $account_page_id = self::factory()->post->create([
      'post_title'   => 'Account Page',
      'post_type'    => 'page',
      'post_status'  => 'publish',
    ]);
    $logout_page_id = self::factory()->post->create([
      'post_title'   => 'Logout Page',
      'post_type'    => 'page',
      'post_status'  => 'publish',
    ]);
    $register_page_id = self::factory()->post->create([
      'post_title'   => 'Register Page',
      'post_type'    => 'page',
      'post_status'  => 'publish',
    ]);
    $login_page_id = self::factory()->post->create([
      'post_title'   => 'Login Page',
      'post_type'    => 'page',
      'post_status'  => 'publish',
    ]);

    // Set up Ultimate Member core pages
    update_option('um_options', [
      'core_page' => [
        'account'  => $account_page_id,
        'logout'   => $logout_page_id,
        'register' => $register_page_id,
        'login'    => $login_page_id,
      ],
    ]);
  }

  protected function tearDown(): void
  {
    parent::tearDown();
  }

  public function test_logged_in_user_view()
  {
    // Create a test user and log them in
    $user_id = self::factory()->user->create([
      'display_name' => 'Test User',
      'user_email'   => 'testuser@example.com',
    ]);
    wp_set_current_user($user_id);
    $account_page = um_get_core_page('account');
    var_dump($account_page, get_permalink($account_page));
    // Capture the output of the login section
    ob_start();
    include dirname(__FILE__) . '../../src/render.php';
    $output = ob_get_clean();

    // Assert the user's display name and email are shown
    $this->assertStringContainsString('Test User', $output, 'The user display name should be visible.');
    $this->assertStringContainsString('testuser@example.com', $output, 'The user email should be visible.');

    // TODO: Assert the Account and Logout links are present
    // $this->assertStringContainsString('href="' . esc_url(um_get_core_page('account')) . '"', $output, 'The Account link should be visible.');
    // $this->assertStringContainsString('href="' . esc_url(um_get_core_page('logout')) . '"', $output, 'The Logout link should be visible.');
  }

  public function test_logged_out_user_view()
  {
    // Ensure the user is logged out
    wp_set_current_user(0);

    // Capture the output of the login section
    ob_start();
    include dirname(__FILE__) . '../../src/render.php';
    $output = ob_get_clean();

    // Assert that the default avatar and "My Account" text are shown
    $this->assertStringContainsString('bi bi-person', $output, 'The default avatar should be visible for logged-out users.');
    $this->assertStringContainsString('My Account', $output, 'The "My Account" text should be visible for logged-out users.');

    // Assert the Register and Sign In links are present
    // $this->assertStringContainsString('href="' . esc_url(um_get_core_page('register')) . '"', $output, 'The Register link should be visible.');
    // $this->assertStringContainsString('href="' . esc_url(um_get_core_page('login')) . '"', $output, 'The Sign In link should be visible.');
  }

  public function test_html_structure()
  {
    // Capture the output of the login section
    ob_start();
    include dirname(__FILE__) . '../../src/render.php';
    $output = ob_get_clean();

    // Assert that the main container elements are present
    $this->assertStringContainsString('login-outer', $output, 'The outer login container should be present.');
    $this->assertStringContainsString('login-container', $output, 'The inner login container should be present.');
  }
}
