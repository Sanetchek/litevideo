<?php
/*
Plugin Name: LiteVideo
Description: Converts uploaded videos to WebM with VP9 codec for optimized performance.
Version: 1.0
Author: Oleksandr Gryshko
Author URI: https://github.com/Sanetchek
Text Domain: litevideo
Domain Path: /languages
*/

// Load Text Domain
add_action('plugins_loaded', 'litevideo_load_textdomain');
function litevideo_load_textdomain() {
  load_plugin_textdomain('litevideo', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Check FFmpeg
function litevideo_check_ffmpeg() {
  return shell_exec('ffmpeg -version') !== null;
}

// Convert Uploaded Videos
add_filter('wp_handle_upload', 'litevideo_convert_video_to_webm');
function litevideo_convert_video_to_webm($upload) {
  if (get_option('litevideo_enable_conversion', 1) && strpos($upload['type'], 'video/') === 0) {
    $input_file = $upload['file'];
    $output_file = str_replace(pathinfo($input_file, PATHINFO_EXTENSION), 'webm', $input_file);
    $command = "ffmpeg -i {$input_file} -c:v vp9 -c:a opus {$output_file} 2>&1";
    exec($command, $output, $return_var);
    if ($return_var === 0) {
      $upload['file'] = $output_file;
      $upload['url'] = str_replace(basename($input_file), basename($output_file), $upload['url']);
      $upload['type'] = 'video/webm';
      unlink($input_file);
    }
  }
  return $upload;
}

// Batch Convert Existing Videos
function litevideo_batch_convert_videos() {
  if (!current_user_can('manage_options')) return;
  $videos = get_posts(['post_type' => 'attachment', 'post_mime_type' => 'video', 'numberposts' => -1]);
  foreach ($videos as $video) {
    $input_file = get_attached_file($video->ID);
    $output_file = str_replace(pathinfo($input_file, PATHINFO_EXTENSION), 'webm', $input_file);
    $command = "ffmpeg -i {$input_file} -c:v vp9 -c:a opus {$output_file} 2>&1";
    exec($command, $output, $return_var);
    if ($return_var === 0) {
      update_attached_file($video->ID, $output_file);
      wp_update_attachment_metadata($video->ID, wp_generate_attachment_metadata($video->ID, $output_file));
      unlink($input_file);
    }
  }
}
add_action('admin_init', function() {
  if (isset($_GET['litevideo_batch_convert']) && current_user_can('manage_options')) {
    litevideo_batch_convert_videos();
    wp_redirect(admin_url('options-general.php?page=litevideo-settings'));
    exit;
  }
});

// Admin Settings Page
add_action('admin_menu', 'litevideo_add_settings_page');
function litevideo_add_settings_page() {
  add_options_page(
    __('LiteVideo Settings', 'litevideo'),
    __('LiteVideo', 'litevideo'),
    'manage_options',
    'litevideo-settings',
    'litevideo_settings_page'
  );
}
function litevideo_settings_page() {
  ?>
  <div class="wrap">
    <h1><?php _e('LiteVideo Settings', 'litevideo'); ?></h1>
    <?php if (!litevideo_check_ffmpeg()) : ?>
      <div class="notice notice-error">
        <p><?php _e('FFmpeg is not installed on the server. Video conversion to WebM is disabled until FFmpeg is installed.', 'litevideo'); ?></p>
      </div>
    <?php endif; ?>
    <form method="post" action="options.php">
      <?php
      settings_fields('litevideo_settings_group');
      do_settings_sections('litevideo-settings');
      submit_button();
      ?>
    </form>
    <p><a href="<?php echo admin_url('options-general.php?page=litevideo-settings&litevideo_batch_convert=1'); ?>">
      <?php _e('Convert all existing videos to WebM', 'litevideo'); ?>
    </a></p>
  </div>
  <?php
}

// Register Settings
add_action('admin_init', 'litevideo_register_settings');
function litevideo_register_settings() {
  register_setting('litevideo_settings_group', 'litevideo_enable_conversion', ['default' => 1]);
  add_settings_section('litevideo_main_section', __('Main Settings', 'litevideo'), null, 'litevideo-settings');
  add_settings_field(
    'litevideo_enable_conversion',
    __('Enable Video Conversion', 'litevideo'),
    'litevideo_enable_conversion_field',
    'litevideo-settings',
    'litevideo_main_section'
  );
}
function litevideo_enable_conversion_field() {
  $enabled = get_option('litevideo_enable_conversion', 1);
  ?>
  <input type="checkbox" name="litevideo_enable_conversion" value="1" <?php checked(1, $enabled); ?> />
  <label><?php _e('Convert uploaded videos to WebM (VP9)', 'litevideo'); ?></label>
  <?php
}

// FFmpeg Notice
add_action('admin_notices', 'litevideo_ffmpeg_notice');
function litevideo_ffmpeg_notice() {
  if (!litevideo_check_ffmpeg()) {
    echo '<div class="notice notice-error"><p>' . __('FFmpeg is not installed. Please install FFmpeg to use LiteVideo.', 'litevideo') . '</p></div>';
  }
}

// Add Settings Link in Plugins List
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'litevideo_settings_link');
function litevideo_settings_link($links) {
  $settings_link = '<a href="' . admin_url('options-general.php?page=litevideo-settings') . '">' . __('Settings', 'litevideo') . '</a>';
  array_unshift($links, $settings_link);
  return $links;
}