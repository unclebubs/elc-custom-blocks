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
      'post_status'    => 'private',
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

    // Pagination settings
    $posts_per_page = 6;
    $paged = isset($_GET['news_page']) ? max(1, intval($_GET['news_page'])) : 1;

    // Update query args with pagination
    $query_args['posts_per_page'] = $posts_per_page;
    $query_args['paged'] = $paged;

    // Re-run query with pagination
    $query = new WP_Query($query_args);

    // Build the grid HTML
    $html = '<div class="wp-block-create-block-news-carousel">';
    $html .= '<div class="news-grid-wrapper">';
    $html .= '<div class="row g-4">';

    while ($query->have_posts()) {
      $query->the_post();
      $post_id = get_the_ID();
      $post_title = get_the_title();
      $post_url = get_permalink();
      $post_excerpt = get_the_excerpt();
      $post_thumbnail = get_the_post_thumbnail($post_id, 'medium', array('class' => 'card-img-top'));

      // Bootstrap card markup in column
      $html .= '<div class="col-md-6 col-lg-4">';
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

    $html .= '</div>'; // .row

    // Add Bootstrap pagination
    $total_pages = $query->max_num_pages;
    if ($total_pages > 1) {
      $html .= '<nav aria-label="News pagination" class="mt-4">';
      $html .= '<ul class="pagination justify-content-center">';

      // Previous button
      if ($paged > 1) {
        $prev_url = add_query_arg('news_page', $paged - 1);
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . esc_url($prev_url) . '" aria-label="Previous">';
        $html .= '<span aria-hidden="true">&laquo;</span>';
        $html .= '</a>';
        $html .= '</li>';
      } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link"><span aria-hidden="true">&laquo;</span></span>';
        $html .= '</li>';
      }

      // Page numbers
      for ($i = 1; $i <= $total_pages; $i++) {
        $page_url = add_query_arg('news_page', $i);
        $active_class = ($i === $paged) ? ' active' : '';
        $html .= '<li class="page-item' . $active_class . '">';
        if ($i === $paged) {
          $html .= '<span class="page-link">' . $i . '</span>';
        } else {
          $html .= '<a class="page-link" href="' . esc_url($page_url) . '">' . $i . '</a>';
        }
        $html .= '</li>';
      }

      // Next button
      if ($paged < $total_pages) {
        $next_url = add_query_arg('news_page', $paged + 1);
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . esc_url($next_url) . '" aria-label="Next">';
        $html .= '<span aria-hidden="true">&raquo;</span>';
        $html .= '</a>';
        $html .= '</li>';
      } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link"><span aria-hidden="true">&raquo;</span></span>';
        $html .= '</li>';
      }

      $html .= '</ul>';
      $html .= '</nav>';
    }

    $html .= '</div>'; // .news-grid-wrapper
    $html .= '</div>'; // .wp-block-create-block-news-carousel

    wp_reset_postdata();

    return $html;
  }
}

// Render the carousel
if (!defined('UNIT_TEST_ENV')) {
  echo render_news_carousel($attributes, $content, $block);
}
