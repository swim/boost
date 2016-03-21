<?php

/**
 * @file
 * Contains Drupal\boost\BoostCacheHandler.
 */

namespace Drupal\boost;

use Drupal\boost\BoostCacheFile;

/**
 * BoostCacheHandler class.
 */
class BoostCacheHandler {

  /**
   * @var \Drupal\boost\BoostCacheFile
   */
  protected $file;

  /**
   * Constructs a new BoostCacheHandler.
   */
  public function __construct() {
    $this->file = new BoostCacheFile(\Drupal::service('file_system'));
  }

  /**
   * Load a file by uri.
   * @param string $uri
   */
  public function getCache($uri) {
    return $this->file->load($uri);
  }

  /**
   * Delete cache file.
   * @param string $path
   */
  public function deleteCache($uri) {
    return $this->file->delete($uri);
  }

  /**
   * Save boost cache file.
   */
  public function setCache($uri, $content) {
    $this->file->save($uri, $content);
  }

}