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
        $images[] = array(
          'id' => $image_id,
          'url' => wp_get_attachment_url($image_id),
          'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
          'caption' => wp_get_attachment_caption($image_id),
          'description' => $attachment->post_content,
          'thumbnail' => wp_get_attachment_image_url($image_id, 'thumbnail'),
          'large' => wp_get_attachment_image_url($image_id, 'large'),
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
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
              <div class="card elc-gallery-card chamfer bg-white h-100"
                role="button"
                tabindex="0"
                aria-label="<?php echo esc_attr('View image: ' . ($image['alt'] ?: 'Image ' . ($index + 1))); ?>"
                data-bs-toggle="modal"
                data-bs-target="#<?php echo esc_attr($modal_id); ?>"
                data-image-index="<?php echo esc_attr($index); ?>"
                data-large-url="<?php echo esc_url($image['large']); ?>"
                data-alt="<?php echo esc_attr($image['alt']); ?>"
                data-caption="<?php echo esc_attr($image['caption']); ?>"
                data-description="<?php echo esc_attr(wp_strip_all_tags($image['description'])); ?>">
                <img src="<?php echo esc_url($image['thumbnail']); ?>"
                  class="card-img-top"
                  alt="<?php echo esc_attr($image['alt']); ?>"
                  style="height: 200px; object-fit: cover;">
                <div class="card-body">
                  <?php if (!empty($image['caption'])) : ?>
                    <p class="card-text text-muted small mb-0">
                      <?php echo esc_html($image['caption']); ?>
                    </p>
                  <?php endif; ?>
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
            <div class="modal-content chamfer bg-white">
              <div class="modal-body position-relative" style="max-height: 80vh; overflow-y: auto;">
                <!-- Close button -->
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10; background-color: rgba(255, 255, 255, 0.8); border-radius: 50%; padding: 0.5rem;"></button>

                <!-- Fullscreen button -->
                <button type="button" class="btn btn-light position-absolute top-0 start-0 m-3 elc-fullscreen-btn" aria-label="Toggle fullscreen" style="z-index: 10; opacity: 0.9;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1.5 1a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4A1.5 1.5 0 0 1 1.5 0h4a.5.5 0 0 1 0 1h-4zM10 .5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 16 1.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5zM.5 10a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 0 14.5v-4a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5z" />
                  </svg>
                </button>

                <!-- Image -->
                <img src="" alt="" class="img-fluid mb-3 elc-modal-image" style="width: 100%; max-height: 70vh; object-fit: contain;">

                <!-- Navigation buttons -->
                <button type="button" class="btn btn-light position-absolute top-50 start-0 translate-middle-y ms-2 elc-nav-prev" aria-label="Previous image" style="z-index: 10; opacity: 0.9;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
                  </svg>
                </button>
                <button type="button" class="btn btn-light position-absolute top-50 end-0 translate-middle-y me-2 elc-nav-next" aria-label="Next image" style="z-index: 10; opacity: 0.9;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" />
                  </svg>
                </button>

                <div class="elc-modal-caption mb-2"></div>
                <div class="elc-modal-description text-muted"></div>
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
