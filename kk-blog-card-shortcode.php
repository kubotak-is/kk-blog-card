<?php

add_shortcode('blog-card', function($atts) {
  extract(shortcode_atts([
    'href' => '/',
    'type' => 'fb',
  ], $atts));
  return '<blog-card href="'.$href.'" data-type="'.$type.'"></blog-card>';
});

add_action('admin_print_footer_scripts', function() {
  if (wp_script_is('quicktags')) {
  ?>
  <script type="text/javascript">
  QTags.addButton('blog-card', 'blog-card', '[blog-card href="" type=""]', '');
  </script>
  <?php
  }
});

add_filter('mce_buttons', 'blogcard_plugin_register_buttons');

function blogcard_plugin_register_buttons($buttons) {
   array_push($buttons, 'separator', 'blogcard_shortcode');
   return $buttons;
}

add_filter('mce_external_plugins', 'blogcard_plugin_register_tinymce_javascript');

function blogcard_plugin_register_tinymce_javascript($plugin_array) {
   $plugin_array['blogcard_plugin_script'] = plugins_url('/editor.js', __FILE__);
   return $plugin_array;
}
