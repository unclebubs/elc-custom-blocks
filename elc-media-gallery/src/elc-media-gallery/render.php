<?php

/**
 * Render callback for the ELC Media Gallery block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the block markup.
 */

if (!function_exists('render_elc_media_gallery')) {
  function render_elc_media_gallery($attributes, $content, $block)
  {
    $image_ids = isset($attributes['imageIds']) ? $attributes['imageIds'] : array();

    // If no images selected, return empty
    if (empty($image_ids)) {
      return '<div class="alert alert-info">' . esc_html__('Please select images in the block settings.', 'elc-media-gallery') . '</div>';
    }

    // Fetch image data from WordPress media library
    $images = array();
    foreach ($image_ids as $image_id) {
      $attachment = get_post($image_id);
      if ($attachment && $attachment->post_type === 'attachment') {
        // Get ACF fields
        $speakers = get_field('speakers', $image_id);
        $session_title = get_field('session_title', $image_id);
        $date = get_field('date', $image_id);
        $location = get_field('location', $image_id);

        $images[] = array(
          'id' => $image_id,
          'url' => wp_get_attachment_url($image_id),
          'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
          'thumbnail' => wp_get_attachment_image_url($image_id, 'thumbnail'),
          'large' => wp_get_attachment_image_url($image_id, 'large'),
          'speakers' => $speakers ? $speakers : '',
          'session_title' => $session_title ? $session_title : '',
          'date' => $date ? $date : '',
          'location' => $location ? $location : '',
        );
      }
    }

    if (empty($images)) {
      return '<div class="alert alert-warning">' . esc_html__('No valid images found.', 'elc-media-gallery') . '</div>';
    }

    // Generate unique ID for this gallery instance
    $gallery_id = 'elc-media-gallery-' . uniqid();
    $modal_id = 'elc-media-modal-' . uniqid();

    // Build the gallery HTML
    ob_start();
?>
    <div class="wp-block-create-block-elc-media-gallery">
      <div class="elc-media-gallery-wrapper container">
        <!-- Image Cards Grid -->
        <div class="row g-4">
          <?php foreach ($images as $index => $image) : ?>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
              <div class="card elc-gallery-card chamfer bg-white h-100"
                role="button"
                tabindex="0"
                aria-label="<?php echo esc_attr('View image: ' . ($image['session_title'] ?: ($image['alt'] ?: 'Image ' . ($index + 1)))); ?>"
                data-bs-toggle="modal"
                data-bs-target="#<?php echo esc_attr($modal_id); ?>"
                data-image-index="<?php echo esc_attr($index); ?>">
                <div class="ratio ratio-1x1 elc-card-img-wrapper">
                  <img src="<?php echo esc_url($image['thumbnail']); ?>"
                    class="card-img-top"
                    alt="<?php echo esc_attr($image['alt']); ?>">
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Bootstrap Modal -->
        <div class="modal fade"
          id="<?php echo esc_attr($modal_id); ?>"
          tabindex="-1"
          aria-label="Image viewer"
          aria-hidden="true"
          data-bs-backdrop="true"
          data-bs-keyboard="true"
          data-gallery-id="<?php echo esc_attr($gallery_id); ?>"
          data-images='<?php echo esc_attr(json_encode($images)); ?>'>
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content chamfer bg-white h-auto">
              <div class="modal-body position-relative" style="max-height: 90vh; min-height: 400px; overflow-y: auto;">
                <!-- Action buttons wrapper (fullscreen + download) -->
                <div class="elc-action-btns position-absolute top-0 start-0 m-3  p-3" style="z-index: 10;">
                  <!-- Fullscreen button -->
                  <button type="button" class="btn btn-light elc-fullscreen-btn d-none d-md-flex" aria-label="Toggle fullscreen" data-bs-toggle="tooltip" data-bs-placement="right" title="Fullscreen" style="opacity: 0.9;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M1.5 1a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4A1.5 1.5 0 0 1 1.5 0h4a.5.5 0 0 1 0 1h-4zM10 .5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 16 1.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5zM.5 10a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 0 14.5v-4a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5z" />
                    </svg>
                  </button>

                  <!-- Download button -->
                  <button type="button" class="btn btn-light elc-download-btn d-none d-md-flex" aria-label="Download image" data-bs-toggle="tooltip" data-bs-placement="right" title="Download" style="opacity: 0.9;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                      <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                    </svg>
                  </button>
                </div>

                <!-- Close button -->
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10; background-color: rgba(255, 255, 255, 0.8); border-radius: 50%; padding: 0.5rem;"></button>

                <!-- Loading Spinner -->
                <div class="elc-image-loader position-absolute top-50 start-50 translate-middle" style="z-index: 5; display: none;">
                  <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>

                <!-- Image -->
                <div class="elc-modal-image-wrapper position-relative  mb-3">
                  <img src="" alt="" class="img-fluid elc-modal-image" style="width: 100%; max-height: 70vh; object-fit: contain;">

                  <!-- Navigation buttons -->
                  <button type="button" class="btn btn-light position-absolute start-0 translate-middle-y ms-2 elc-nav-prev" aria-label="Previous image" style="z-index: 10; opacity: 0.9;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
                    </svg>
                  </button>
                  <button type="button" class="btn btn-light position-absolute end-0 translate-middle-y me-2 elc-nav-next" aria-label="Next image" style="z-index: 10; opacity: 0.9;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" />
                    </svg>
                  </button>

                </div>



                <!-- Image Meta Data -->
                <div class="elc-modal-meta-data">
                  <div class='d-flex justify-content-end w-100'>
                    <button type="button" class="btn btn-link elc-download-text d-flex d-md-none text-small" aria-label="Download image" data-bs-toggle="tooltip" data-bs-placement="right" title="Download" style="opacity: 0.9;">
                      download
                    </button>
                  </div>
                  <h5 class="elc-modal-session-title mb-2"></h5>
                  <p class="elc-modal-speakers mb-1"></p>
                  <p class="elc-modal-location mb-1"></p>
                  <p class="elc-modal-date mb-0"></p>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php
    return ob_get_clean();
  }
}

// Render the gallery
if (!defined('UNIT_TEST_ENV')) {
  echo render_elc_media_gallery($attributes, $content, $block);
}
