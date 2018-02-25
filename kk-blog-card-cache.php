<?php

class KK_Blog_Caed_Cache {

  const CACHE_TIME = "-1 days";

  /**
   * @var string
   */
  private $fileDir;
  
  public function __construct() {
    $this->fileDir = __DIR__ . "/tmp/cache/";
  }

  /**
   * @param string $id
   * @return string
   */
  private function filePath($id) {
    return $this->fileDir . sha1($id) . ".json";
  }

  /**
   * @param string $url
   * @return string
   */
  public function get($url) {
    return file_get_contents($this->filePath($url));
  }

  /**
   * @param string $url
   * @param string $val
   * @return bool
   */
  public function put($url, $val) {
    return (bool) file_put_contents(
      $this->filePath($url),
      $val,
      FILE_APPEND | LOCK_EX
    );
  }

  /**
   * @param string $url
   * @return bool
   */
  public function has($url) {
    $exists = file_exists($this->filePath($url));
    if (!$exists) {
      return false;
    }
    $fileTime  = filemtime($this->filePath($url));
    $cacheTime = new DateTime(self::CACHE_TIME);
    if ($fileTime < $cacheTime->getTimestamp()) {
      // delete cache
      unlink($this->filePath($url));
      return false;
    }
    return true;
  }

}
