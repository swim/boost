<?php

/**
 * @file
 * Contains Drupal\boost\BoostCache
 */

namespace Drupal\boost;

use Drupal\boost\BoostCacheRoute;
use Drupal\boost\BoostCacheHandler;

/**
 * The BoostCache class.
 *
 * @todo, only fire notices if debug (mode) enabled.
 * @todo, batch to generate entire cache.
 * @todo, cron to re-generate/ invalidate cache.
 * @todo, exclude route list, don't cache specific paths.
 */
class BoostCache {

  /**
   * @var \Drupal\boost\BoostCacheHandler
   */
  protected $handler;

  /**
   * @var \Drupal\boost\BoostCacheRoute
   */
  protected $route;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a new BoostCache.
   */
  public function __construct() {
    $this->handler = new BoostCacheHandler();
    $this->route = new BoostCacheRoute(
      \Drupal::service('path.current'), 
      \Drupal::service('path.alias_manager')
    );
    $this->logger = \Drupal::logger('boost');
  }

  /**
   * Save page to local filesystem.
   * @param string $response
   *
   *  @todo, _toString if Symfony Response.
   */
  public function index($response) {
    if (!is_string($response)) {
      $this->logger->notice('Response for route @uri can not be cached.',
        array(
            '@uri' => $this->route->getPath()
        )
      );

      return;
    }

    $this->handler->setCache($this->route->getUri(), $response);
  }

  /**
   * Retrieve Boost cache file.
   */
  public function retrieve() {
    $uri = $this->route->getUri();

    if (!file_exists($uri)) {
      $this->logger->notice('Route @uri not cached.',
        array(
            '@uri' => $uri
        )
      );

      return false;
    }

    return $this->handler->getCache($uri);
  }

  /**
   * Delete individual cache file.
   */
  public function delete() {
    $uri = $this->route->getUri();

    if (!file_exists($uri)) {
      return false;
    }

    return $this->handler->deleteCache($uri);
  }

  /**
   * Delete entire boost cache.
   */
  public function purge() {
    
  }

}
