<?php

namespace Drupal\Publish;

use Drupal\Core\Field\EntityReferenceFieldItemList;


Class Utility {
  public function get_url($type, $action, $entity_id) {
    switch ($type) {
      case 'node':
        $url = '/node';
        if ($action == 'patch') {
          $url = '/node/' . $entity_id;
        }
      break;
      case 'taxonomy_term':
        $url = '/taxonomy/term';
        if ($action == 'patch') {
          $url = '/taxonomy/term/' . $entity_id;
        }
        break;
      case 'file':
        $url = '/entity/file';
        if ($action == 'patch') {
          $url = '/entity/file/' . $entity_id;
        }
        break;
      case 'user':
        $url = '/entity/user';
        if ($action == 'patch') {
          $url = '/user/' . $entity_id;
        }
        break;
      case 'media':
        $url = '/entity/media';
        if ($action == 'patch') {
          $url = '/media/' . $entity_id . '/edit';
        }
    }
    return $url;
  }

  public function check_exists($type, $uuid) {
    $url = '/get-content/' . $type . '/' . $uuid;
    $response = $this->api_call($url);
    return $response['id'];
  }

  public function api_call($url, $options = [], $action = 'get') {
    $config = \Drupal::config('publish.admin_settings');
    $api_url = rtrim($config->get('url'), '/') . $url;
    $api_url = rtrim($api_url, '/') . '?_format=json';
    $options['headers'] = [
      'Content-Type' => 'application/json'
    ];
    // Add auth if needed.
    if ($config->get('username') && $config->get('password')) {
      $options['auth'] = [
        $config->get('username'),
        $config->get('password')
      ];
    }

    try {
      if ($action == 'patch') {
        $request =  \Drupal::httpClient()->patch($api_url, $options);
      }
      elseif($action == 'post') {
        $request =  \Drupal::httpClient()->post($api_url, $options);
      }
      elseif($action == 'get') {
        $request =  \Drupal::httpClient()->get($api_url, $options);
      }
      $response = json_decode($request->getBody(), TRUE);
    }
    catch (\Exception $e) {
      \Drupal::logger('publish')->error($e->getMessage());
    }
    return $response;
  }

  public function sync_entity($entity) {
    $type = $entity->getEntityTypeId();
    $bundle = $entity->bundle();
    $uuid = $entity->uuid->value;

    // Handle dependency Entities first.
    $fields = [
      'uid',
      'field_tags'
    ];
    foreach ($fields as $field) {
      if ($entity->hasField($field)) {
        if ($entity->get($field) && $entity->get($field) instanceof EntityReferenceFieldItemList) {
          if ($dp_entities = $entity->get($field)->referencedEntities()) {
            foreach ($dp_entities as $dp_entity) {
              $this->sync_entity($dp_entity);
            }
          }
        }
      }
    }

    $action = 'post';
    // check existance
    $entity_id = $this->check_exists($type, $uuid);
    if (!empty($entity_id)) {
      $action = 'patch';
    }
    $url = $this->get_url($type, $action, $entity_id);
    
    // Content post create/patch.
    //serialize and unset.
    $serialized_entity = $this->structure_post_entity($entity, $type);
    // Create Options.
    $options['body'] = $serialized_entity;
    //push
    $response = $this->api_call($url, $options, $action);
    //TODO DB log once synced.

  }

  public function structure_post_entity($entity, $type) {
    // Create DATA to post.
    $serializer = \Drupal::service('serializer');
    $serialized_entity = $serializer->serialize($entity, 'json', ['plugin_id' => 'entity']);
  
    $json = json_decode($serialized_entity);
    // For all types.
    unset($json->changed);
    switch ($type) {
      case 'taxonomy_term';
        unset($json->tid);
      break;
      case 'node';
        unset($json->nid);
        unset($json->revision_timestamp);
        unset($json->revision_uid);
        unset($json->revision_translation_affected);
        unset($json->revision_log);
        unset($json->moderation_state);
        unset($json->body[0]->processed);
        unset($json->vid);
      break;
      case 'user';
       unset($json->uid);
      break;
    }
    unset($json->moderation_state);
    $serialized_entity = json_encode($json);
    return $serialized_entity;
  }


  public function publish_presave($entity) {
    if ($entity->getEntityTypeId() == 'node') {
      $push_states = [];
      $config = \Drupal::config('publish.admin_settings');
      // States to push.
      $push_states= [
        $config->get('state'),
        $config->get('archive_state'),
      ];
      if (in_array($entity->moderation_state->value, $push_states)) {
        $this->sync_entity($entity);
      }
    }
  }
}