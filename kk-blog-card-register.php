<?php
/*
Plugin Name: kk-blog-card
Plugin URI: https://github.com/kubotak-is/kk-blog-card
Description: ブログカードコンポーネント生成プラグイン
Author: kubotak
Version: 1.3
Author URI: https://github.com/kubotak-is
*/
require_once __DIR__ . "/kk-blog-card-shortcode.php";
require_once __DIR__ . "/kk-blog-card-api.php";

add_action('wp_enqueue_scripts', function() {
  wp_enqueue_script(
    'kk-blog-card'
    , plugins_url('/index.js', __FILE__)
    , []
    , '1.3'
    , true
  );
});
