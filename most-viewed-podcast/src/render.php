<?php
$template_directory = defined('TEMPLATE_DIR') ? TEMPLATE_DIR : get_template_directory();
require_once $template_directory . '/includes/template-functions.php';

if (!function_exists('render_most_viewed_podcast')) {

  function render_most_viewed_podcast($attributes, $content)
  {


    global $wpdb;
    // Query the most viewed podcast post from the custom table
    $result = $wpdb->get_row("
        SELECT pv.id
        FROM {$wpdb->prefix}post_views AS pv
        INNER JOIN {$wpdb->posts} AS p ON pv.id = p.ID
        WHERE pv.type = 4
          AND pv.period = 'total'
          AND p.post_status = 'publish'
          AND p.post_type = 'podcast'
        ORDER BY pv.count DESC, post_date DESC
        LIMIT 1
    ");


    // Check if a result is found
    if ($result) {


      $post_id       = $result->id;
      $post_title    = get_the_title($post_id);
      $post_url      = get_permalink($post_id);
      $post_excerpt  = get_the_excerpt($post_id);
      $date = get_the_date('F j, Y');
      $episode_data = powerpress_get_enclosure_data($post_id);
      $episode_number = $episode_data['episode_no'];
      // Get the post thumbnail
      $post_thumbnail = get_the_post_thumbnail($post_id, 'medium', array('class' => 'podcast-thumbnail'));

      return render_podcast_card(cardTitle: 'Most Popular Podcast', title: $post_title, excerpt: $post_excerpt, episode_number: $episode_number, thumbnail: $post_thumbnail, link: $post_url, date: $date);
    } else {
      // Fallback if no posts are found
      return '<div class="alert alert-warning" role="alert">' . esc_html__('No most viewed podcast found.', 'mytheme') . '</div>';
    }
  }
}

if (!defined('UNIT_TEST_ENV')) {
  echo render_most_viewed_podcast($attributes, $content);
}
