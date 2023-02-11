<?php

namespace Drupal\feed_import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "attribute_xml"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field:
 *   plugin: attribute_xml
 *   source: value
 * @endcode
 *
 */
class AttributeXML extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $value = (array) $value;
    $paths = [];
    $filePath = $value['@attributes']['url'];
    $fileInfo = pathinfo($filePath);
    $paths[] = $filePath;
    $paths[] = 'public://feed_media/' . $fileInfo['filename'];
    return $paths;
  }
}
