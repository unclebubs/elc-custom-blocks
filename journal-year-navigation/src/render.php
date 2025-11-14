<?php

function render_journal_club_navigation($attributes, $content)
{
        $term = get_queried_object();
        global $wpdb;

        $years = $wpdb->get_col("
    SELECT DISTINCT YEAR(pm.meta_value) as year
    FROM {$wpdb->postmeta} pm
    INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
    WHERE pm.meta_key = 'start_date'
    AND p.post_type = 'journal-club'
    AND p.post_status = 'publish'
    AND pm.meta_value IS NOT NULL
    ORDER BY year DESC
");

        // Return message if no years found
        if (empty($years)) {
                return '<div class="alert alert-warning" role="alert">' . esc_html__('No available years.', 'mytheme') . '</div>';
        }

        // Build the HTML output
        $html  = '<ul class="nav">';
        if (count($years) > 1) {
                $is_all_active = !empty($y) ? '' : 'active';
                $html  .= '
                <li class="nav-item">
                        <a href="/#podcast-listings' . '" 
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
                        $url = '/#podcast-listings';
                        $html  .= '
                        <li class="nav-item">
                                <a href="' . esc_url($url) . '" 
                                        class="btn btn-sm btn-light ' . $is_year_active . '"
                                        data-year="' . $year . '">'
                                . esc_html($year) .
                                '</a>
                        </li>';
                }
        $html  .= '</ul>';

        return $html;
}

if (!defined('UNIT_TEST_ENV')) {
        echo render_journal_club_navigation($attributes, $content);
}
