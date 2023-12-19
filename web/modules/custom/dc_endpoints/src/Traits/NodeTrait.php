<?php

namespace Drupal\dc_endpoints\Traits;

use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * @ xablau.
 *
 */
trait NodeTrait {

  /**
   * {@inheritdoc}
   */
  public function currentLanguage(): string {
    return $this->languageManager->getCurrentLanguage()->getId();
  }

  /**
   * {@inheritdoc}
   */
  public function getTranslationField(Node $node, string $langcode): Node|bool {
    if ($node->hasTranslation($langcode)) {
      return $node->getTranslation($langcode);
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getStorageNodeOrVote($storage_type): object {
    return $this->entityTypeManager->getStorage($storage_type);
  }

  /**
   * {@inheritdoc}
   */
  public function getAllNodes($bundle, $status): array {
    // Load the nodes.
    $node_storage = $this->getStorageNodeOrVote('node');
    return $node_storage->loadByProperties(
      [
      'type' => $bundle,
      'status' => $status,
      ]
    );
  }

  /**
   * Get the path alias through the node id.
   */
  public function getPathAlias(int $node_id): string {
    // Get the URL object for the node using its ID.
    $url = Url::fromRoute('entity.node.canonical', ['node' => $node_id]);
    // Get the path alias from the URL object.
    return $url->toString();
  }


}
