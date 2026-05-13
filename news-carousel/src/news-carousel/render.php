<?php

/**
 * Render callback for the News Carousel block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the block markup.
 */

if (!function_exists('render_news_carousel')) {
  function render_news_carousel($attributes, $content, $block)
  {
    $category_id = isset($attributes['categoryId']) ? intval($attributes['categoryId']) : 0;

    // If no category selected, return empty
    if ($category_id === 0) {
      return '<div class="alert alert-info">' . esc_html__('Please select a category in the block settings.', 'news-carousel') . '</div>';
    }

    // Query args for fetching posts
    $query_args = array(
      'post_type'      => 'post',
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'cat'            => $category_id,
      'orderby'        => 'date',
      'order'          => 'DESC',
    );

    $query = new WP_Query($query_args);

    if (!$query->have_posts()) {
      wp_reset_postdata();
      return '<div class="alert alert-warning">' . esc_html__('No news articles found in the selected category.', 'news-carousel') . '</div>';
    }

    // Generate unique ID for this carousel instance
    $carousel_id = 'news-carousel-' . uniqid();

    // Build the carousel HTML
    $html = '<div class="wp-block-create-block-news-carousel">';
    $html .= '<div class="news-carousel-wrapper">';
    $html .= '<div id="' . esc_attr($carousel_id) . '" class="news-carousel">';

    while ($query->have_posts()) {
      $query->the_post();
      $post_id = get_the_ID();
      $post_title = get_the_title();
      $post_url = get_permalink();
      $post_excerpt = get_the_excerpt();
      $post_thumbnail = get_the_post_thumbnail($post_id, 'medium', array('class' => 'card-img-top'));


      // Bootstrap card markup
      $html .= '<div class="news-carousel-item">';
      $html .= '  <div class="card bg-black chamfer text-elc-white p-3 p-xl-5 h-100">';
      $html .=        '<div class="card-header">';
      $html .= '      <h3 class="card-title text-primary">' . esc_html($post_title) . '</h3>';
      $html .= '    </div>';
      $html .= '    <div class="card-body d-flex flex-column">';
      $html .= '      <p class="card-text text-elc-white">' . esc_html($post_excerpt) . '</p>';
      $html .= '      <a href="' . esc_url($post_url) . '" class="btn white-solid-pill align-self-start">' . esc_html__('Read the article', 'news-carousel') . '</a>';
      $html .= '    </div>';
      $html .= '  </div>';
      $html .= '</div>';
    }

    $html .= '</div>'; // .news-carousel
    $html .= '</div>'; // .news-carousel-wrapper
    $html .= '</div>'; // .wp-block-create-block-news-carousel

    wp_reset_postdata();

    return $html;
  }
}

// Render the carousel
if (!defined('UNIT_TEST_ENV')) {
  echo render_news_carousel($attributes, $content, $block);
}
