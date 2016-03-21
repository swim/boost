<?php

/**
 * @file
 * Contains Drupal\boost\BoostCacheFile
 *
 * @todo, managed files...
 */

namespace Drupal\boost;

use Drupal\Core\File\FileSystem;

/**
 * BoostCacheFile class.
 */
class BoostCacheFile {

  /**
   * Default mode for new directories.
   */
  const CHMOD_DIRECTORY = 0775;

  /**
   * The file uri.
   */
  protected $uri;

  /**
   * @var \Drupal\Core\File\FileSystem
   */
  protected $filesystem;

  /**
   * @param \Drupal\Core\File\FileSystem $filesystem
   *    Provides helpers to operate on files and stream wrappers.
   */
  public function __construct(FileSystem $filesystem) {
    $this->filesystem = $filesystem;
  }

  /**
   * Load file contents by uri.
   * @param $uri string
   */
  public function load($uri) {
    return file_get_contents($uri);  
  }

  /**
   * Create new cache file.
   * @param string $uri
   * @param string $content
   */
  public function save($uri, $content) {
    $this->directory($uri);
    $this->modify($uri, $content);
  }

  /**
   * Delete cache file by uri.
   * @param string $uri
   */
  public function delete($uri) {
    return $this->filesystem->unlink($uri);
  }

  /**
   * Make directory.
   * @param string $uri
   */
  private function directory($uri) {
    $dir = $this->filesystem->dirname($uri);

    if (!file_exists($dir)) {
      $this->filesystem->mkdir($dir, static::CHMOD_DIRECTORY, TRUE);
    }
  }

  /**
   * Overrite existing cache file.
   * @param string $uri
   * @param string $content
   */
  private function modify($uri, $content) {
    return file_unmanaged_save_data($content, $uri, FILE_EXISTS_REPLACE);
  }

}
