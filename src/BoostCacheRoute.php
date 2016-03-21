<?php

/**
 * @file
 * Contains Drupal\boost\BoostCacheRoute
 */

namespace Drupal\boost;

use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\AliasManager;

/**
 * BoostCacheRoute class.
 *
 * @todo, allow admin to set uri scheme location.
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
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;
  
  /**
   * The alias manager.
   * @var \Drupal\Core\Path\AliasManager
   */
  protected $aliasManager;

  /**
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *    The current path.
   * @param \Drupal\Core\Path\AliasManager $aliasManager
   *    The alias manager.
   */
  public function __construct(CurrentPathStack $current_path, AliasManager $alias_manager) {
    $this->currentPath = $current_path;
    $this->aliasManager = $alias_manager;
    $this->schema = file_default_scheme() . '://boost';
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
    return $this->aliasManager->getAliasByPath($this->currentPath->getPath());
  }

  /**
   * Return the converted file path.
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
