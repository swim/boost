<?php

/**
 * @file
 * Contains Drupal\boost\EventSubscriber\BoostResponseSubscriber
 */

namespace Drupal\boost\EventSubscriber;

use Drupal\boost\BoostCache;
use Drupal\Core\Render\HtmlResponse;
use Drupal\Core\Render\AttachmentsResponseProcessorInterface;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BoostResponseSubscriber.
 */
class BoostResponseSubscriber implements EventSubscriberInterface {

  /**
   * The HTML response attachments processor service.
   *
   * @var \Drupal\Core\Render\AttachmentsResponseProcessorInterface
   */
  protected $htmlResponseAttachmentsProcessor;

  /**
   * The current account.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $account;

  /**
   * Constructs a HtmlResponseSubscriber object.
   *
   * @param \Drupal\Core\Render\AttachmentsResponseProcessorInterface $html_response_attachments_processor
   *   The HTML response attachments processor service.
   * @param \Drupal\Core\Session\AccountProxy $account
   */
  public function __construct(AttachmentsResponseProcessorInterface $html_response_attachments_processor, AccountProxy $account) {
    $this->boostCache = new BoostCache();
    $this->htmlResponseAttachmentsProcessor = $html_response_attachments_processor;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return array(
      KernelEvents::REQUEST => array(
        array('onRequest', 100)
      ),
      KernelEvents::RESPONSE => array(
        array('onRespond', -100)
      )
    );
  }

  /**
   * Process a GetResponseEvent instance.
   */
  public function onRequest(GetResponseEvent $event) {
    if ($this->account->isAuthenticated()) {
      return;
    }

    // Invalidate on POST to rebuild page response.
    $method = $event->getRequest()->getMethod();
    if ($method == 'POST') {
      $this->boostCache->delete();
    }

    // Only cache GET requests.
    if ($method != 'GET') {
      return;
    }

    // Check for existing cached response.
    if ($content = $this->boostCache->retrieve()) {  
      $response = new Response();
      $response->setContent($content);
      $response->setStatusCode(Response::HTTP_OK);
      $response->headers->set('X-Boost-Cache', 'partial');

      $event->setResponse($response);
    }
  }

  /**
   * Processes HtmlResponse event.
   *
   * @todo, split things off into the class.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The event to process.
   */
  public function onRespond(FilterResponseEvent $event) {
    if ($this->account->isAuthenticated()) {
      return;
    }

    $response = $event->getResponse();
    if (!$response instanceof HtmlResponse) {
      return;
    }

    if ($response->isRedirect() || $response->isForbidden() ||
        $response->isNotFound()) {
      return;
    }

    // Process response attachments.
    $this->htmlResponseAttachmentsProcessor->processAttachments($response);
    $content = $response->getContent();

    // Create a cached response on the local file system.
    $this->boostCache->index($content);
  }

}
