<?php

namespace Drupal\publish\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a resource for uuid content.
 *
 * @RestResource(
 *   id = "get_content",
 *   label = @Translation("Get content test"),
 *   uri_paths = {
 *     "canonical" = "/get-content/{type}/{uuid}"
 *   }
 * )
 */
class GetContent extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   * Returns a watchdog log entry for the specified ID.
   *
   * @param string $type
   *   The type of entity.
   * @param string $uuid
   *   The uuid of entity.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the log entry.
   */
  public function get($type = NULL, $uuid = NULL) {
    $result = [];
    if ($type && $uuid) {
      $entity = \Drupal::service('entity.repository')->loadEntityByUuid($type, $uuid);
      if ($entity instanceof EntityInterface) {
        $result['id'] = $entity->id();
      }
    }
    return new ResourceResponse($result);
  }

}
