<?php

require_once __DIR__ . "/kk-blog-card-cache.php";
require_once __DIR__ . "/kk-blog-card-parser.php";

add_action('rest_api_init', function() {
  register_rest_route('v1', '/kkblogcard', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'kk_blog_card_func',
    'args' => [
      'url' => [
        'validate_callback' => function($param, $request, $key) {
          return preg_match('/([\w*%#!()~\'-]+\.)+[\w*%#!()~\'-]+(\/[\w*%#!()~\'-.]+)*/u', $param);
        }
      ],
    ],
  ]);
});

function kk_blog_card_func(WP_REST_Request $request) {
  $json     = $request->get_params("JSON");
  $urlStr   = strval($json['url']);
  $gen      = new KK_Blog_Card_Parser($urlStr);
  $cache    = new KK_Blog_Card_Cache();
  $response = new WP_REST_Response();

  if ($cache->has($urlStr)) {
    // has cache
    $value = json_decode($cache->get($urlStr), true);
  } else {
    // has not cache
    $gen->fetch();
    $value = $gen->getValues();
    if (empty($value)) {
      $response->set_status(404);
    } else {
      $cache->put($urlStr, json_encode($value));
    }
  }

  $response->set_data($value);
  return $response;
}
