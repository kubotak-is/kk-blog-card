<?php
/*
Plugin Name: kk-blog-card
Plugin URI: https://github.com/kubotak-is/kk-blog-card
Description: ブログカードコンポーネント生成プラグイン
Author: kubotak
Version: 0.1
Author URI: https://github.com/kubotak-is
*/
require_once __DIR__ . "/kk-blog-card-shortcode.php";
require_once __DIR__ . "/kk-blog-card-api.php";

add_action('wp_enqueue_scripts', function() {
  wp_enqueue_script(
    'kk-blog-card'
    , get_option('siteurl').'/wp-content/plugins/kk-blog-card/index.js'
    , []
    , '1.0.0'
    , true
  );
});