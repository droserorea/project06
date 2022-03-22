<?php

namespace Drupal\jsonapi_test_field_aliasing\ResourceType;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\jsonapi\ResourceType\ResourceTypeRepository;

/**
 * Provides a repository of JSON:API resource types with aliasable field names.
 */
class AliasingResourceTypeRepository extends ResourceTypeRepository
{

  /**
   * {@inheritdoc}
   */
  protected function getFields(array $field_names, EntityTypeInterface $entity_type, $bundle)
  {
    $fields = parent::getFields($field_names, $entity_type, $bundle);
    foreach ($fields as $field_name => $field) {
      if (strpos($field_name, 'field_test_alias_') === 0) {
        $fields[$field_name] = $fields[$field_name]->withPublicName('field_test_alias');
      }
    }
    return $fields;
  }

}
