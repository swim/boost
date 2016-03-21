<?php

/**
 * @file
 * Contains Drupal\boost\BoostCacheRoute
 */

namespace Drupal\boost;

use Drupal\Core\Path\CurrentPathStack;

/**
 * BoostCacheRoute class.
 */
class BoostCacheRoute {

  /**
   * File system schema.
   */
  protected $schema;

  /**
   * File extension.
   */
  protected $extension;

  /**
   * The normalized current path.
   */
  protected $filePath;

  /**
   * The current path.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *    The current path.
   */
  public function __construct(CurrentPathStack $current_path) {
    $this->currentPath = $current_path;
    $this->schema = 'public://boost';
    $this->extension = '.html';
  }

  /**
   * Return the current file uri.
   */
  public function getUri() {
    return $this->schema . $this->getPath() . $this->extension;
  }

  /**
   * Return the current path.
   */
  public function getPath() {
    return $this->currentPath->getPath();
  }

  /**
   * Return the file path.
   */
  public function getFilePath() {
    if (!$this->filePath) {
      $this->filePath = array_filter($this->convertPath());
    }

    return $this->filePath;
  }

  /**
   * Convert the path into a file path.
   */
  private function convertPath() {
    return explode('/', $this->getPath());
  }

}
