<?php

require_once __DIR__ . "/kk-blog-card-cache.php";

add_action('rest_api_init', function() {
  register_rest_route('v1', '/kkblogcard', [
    'methods' => WP_REST_Server::READABLE,
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
  $url      = $request->get_params("url");
  $urlStr   = strval($url['url']);
  $gen      = new JSON_REST_API_Kk_Blog_Card_Generator($urlStr);
  $cache    = new KK_Blog_Caed_Cache();
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

class JSON_REST_API_Kk_Blog_Card_Generator
{
  /**
   * @val string
   */
  private $url;

  /**
   * @val array
   */
  private $values = [];

  /**
   * @param string $url
   */
  public function __construct($url)
  {
    $this->url = $url;
  }

  /**
   * @retrun array
   */
  public function getValues()
  {
    return $this->values;
  }

  /**
   * @return void
   */
  public function fetch()
  {
    $response = wp_remote_get($this->url, [
      'timeout' => '10'
    ]);
    if (is_wp_error($response) || $response['response']['code'] !== 200 ) {
      return;
    }
    $body = wp_remote_retrieve_body($response);
    if (!empty($body)) {
      $this->htmlParse($body);
    }
  }

  /**
   * @param string $html
   * @return void
   */
  private function htmlParse($html)
  {
    $urlParse = parse_url($this->url);
    $this->values['site_url'] = $urlParse['scheme'] . "://" . $urlParse['host'] . "/";

    $old_libxml_error = libxml_use_internal_errors(true);

    $doc = new DOMDocument();
    $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    libxml_use_internal_errors($old_libxml_error);

    $tags = $doc->getElementsByTagName('meta');
    if (!$tags || $tags->length === 0) {
      return false;
    }

    foreach ($tags as $tag) {
      if ($tag->hasAttribute('property') &&
          strpos($tag->getAttribute('property'), 'og:') === 0
      ) {
        $key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
        $this->values[$key] = $tag->getAttribute('content');
      }

      if ($tag ->hasAttribute('value') &&
          $tag->hasAttribute('property') &&
          strpos($tag->getAttribute('property'), 'og:') === 0
      ) {
        $key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
        $this->values[$key] = $tag->getAttribute('value');
      }

      if ($tag->hasAttribute('name') &&
          $tag->getAttribute('name') === 'description'
      ) {
        $nonOgDescription = $tag->getAttribute('content');
      }
    }

    if (!isset($this->values['title'])) {
      $titles = $doc->getElementsByTagName('title');
      if ($titles->length > 0) {
        $this->values['title'] = $titles->item(0)->textContent;
      }
    }

    if (!isset($this->values['description']) && $nonOgDescription) {
      $this->values['description'] = $nonOgDescription;
    }

    // text width
    if (isset($this->values['title'])) {
      $this->values['titlw'] = mb_strimwidth($this->values['description'], 0, 60, "…");
    }

    if (isset($this->values['description'])) {
      $this->values['description'] = mb_strimwidth($this->values['description'], 0, 100, "…");
    }

    $domxpath = new DOMXPath($doc);

    $favicons = $domxpath->query("//link[@rel='shortcut icon']");
    foreach($favicons as $favicon) {
      $domattr = $favicon->attributes->getNamedItem('href');
      if (!$domattr) {
        continue;
      }
      $faviconUrl = $domattr->value;
      if (!filter_var($faviconUrl, FILTER_VALIDATE_URL)) {
        $faviconUrl = $this->values['site_url'] . ltrim($faviconUrl, '/');
      }
      $this->values['favicon'] = $faviconUrl;
    }

    if (!isset($this->values['image'])) {
      $images = $domxpath->query("//link[@rel='image_src']");
      if ($images->length > 0) {
        $domattr = $images->item(0)->attributes->getNamedItem('href');
        if ($domattr) {
          $this->values['image'] = $domattr->value;
          $this->values['image_src'] = $domattr->value;
        }
      }
    }
  }
}
