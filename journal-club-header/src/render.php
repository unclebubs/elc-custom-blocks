<?php

function render_journal_club_header($attributes, $content)
{
  $showRegisterButton = $attributes['showRegisterButton'] ?? true;
  $showWatchNowButton = $attributes['showWatchNowButton'] ?? false;

  // Fetch ACF fields
  $start_date  = get_field('start_date'); // e.g., 2024-06-10
  if ($start_date) {
    $formatted_date = '';
    $date_object = DateTime::createFromFormat('Ymd', $start_date);
    $formatted_date = $date_object->format('F jS, Y');
  }
  $start_time  = get_field('start_time');
  $formatted_start_time = '';
  if ($start_time) {
    $formatted_start_time = date('H:i', strtotime($start_time)); // 24-hour format
  }
  $end_time   = get_field('end_time');   // e.g., 11:00 AM
  $formatted_end_time = '';
  if ($end_time) {
    $formatted_end_time   = date('H:i', strtotime($end_time));
  }

  $webinar_link = get_field('webinar_link'); // URL to register
  $webinar_recording = get_field('local_webinar_recording'); // URL to register

  $html = '<div class="card" style="height: 400px; background-image: url("' . get_the_post_thumbnail_url() . '");">';
  $html .= '<div class="card-body ">';
  $html .= '<h1 class="card-title ">' . get_the_title() . '</h1>';
  $html .= '<p class="card-text">';
  $html .= esc_html($formatted_date) . ' | ' . esc_html($formatted_start_time) . ' - ' . esc_html($formatted_end_time);
  $html .= '</p> ';
  $html .= '<div> ';
  if ($showRegisterButton && $webinar_link) {
    $html .= '<a href="' . esc_url($webinar_link) . '" data-bs-toggle="modal" data-bs-target="#tempModal" class="black-solid-pill">Register</a> ';
  }
  if ($showWatchNowButton) {
    $html .= '<a href="#inline-video" class="btn-left">Watch</a> ';
  }
  $html .= ' <a href="#more" class="btn-right">Learn More</a> ';
  $html .= '<div class="modal" id="tempModal" tabindex="-1"> ';
  $html .= '<div class="modal-dialog"> ';
  $html .= '<div class="modal-content"> ';
  $html .= '<div class="modal-header"> ';
  $html .= '<h5 class="modal-title class="text-elc-black" id="modalBasicLabel">To be implemented</h5> ';
  $html .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> ';
  $html .= '</div> ';
  $html .= '<div class="modal-body"> ';
  $html .= '<p class="text-elc-black text-start">This button will prompt the user to either login and register for the Webinar or transfer the user to the Webinar site (eg zoom) to register</p> ';
  $html .= '</div> ';
  $html .= '</div> ';
  $html .= '</div> ';
  $html .= '</div> ';
  $html .= '</div> ';
  $html .= '</div> ';
  $html .= '</div> ';


  return $html;
}


if (!defined('UNIT_TEST_ENV')) {
  echo render_journal_club_header($attributes, $content);
}
