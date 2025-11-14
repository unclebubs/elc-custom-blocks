<?php

if (!function_exists('render_podcast_years')) {


        function render_podcast_years($attributes, $content)
        {

                $term = get_queried_object();
                $y = get_query_var('y');
                global $wpdb;
                $subcategory_id = $term->term_id; // Replace with your desired subcategory ID

                $query = $wpdb->prepare("
    SELECT DISTINCT YEAR(p.post_date) AS year
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)
    INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
    WHERE p.post_status = 'publish'
    AND p.post_type = 'podcast'
    AND tt.taxonomy = %s
    AND tt.term_id = %d
    ORDER BY YEAR(p.post_date) DESC
", 'podcast-category', $subcategory_id);


                $years = $wpdb->get_col($query);

                // Return message if no years found
                if (empty($years)) {
                        return '<div class="alert alert-warning" role="alert">' . esc_html__('No available years.', 'mytheme') . '</div>';
                }

                // Build the HTML output
                $output = '<ul class="nav">';
                if (count($years) > 1) {
                        $is_all_active = !empty($y) ? '' : 'active';
                        $output .= '
                <li class="nav-item">
                        <a href="' . get_term_link($subcategory_id) . '/#podcast-listings' . '" 
                                class="btn btn-sm btn-light ' . $is_all_active . '"
                                data-year="all"
>
                                All years
                        </a>
                </li>';
                }
                if ($years)
                        foreach ($years as $year) {
                                $is_year_active = (!empty($y) &&  $y == $year) || count($years) === 1 ? 'active' : '';
                                $url = trailingslashit(get_term_link($term)) . $year . '/#podcast-listings';
                                $output .= '
                        <li class="nav-item">
                                <a href="' . esc_url($url) . '" 
                                        class="btn btn-sm btn-light ' . $is_year_active . '"
                                        data-year="' . $year . '">'
                                        . esc_html($year) .
                                        '</a>
                        </li>';
                        }
                $output .= '</ul>';

                return $output;
        }
}

if (!defined('UNIT_TEST_ENV')) {
        echo render_podcast_years($attributes, $content);
}
