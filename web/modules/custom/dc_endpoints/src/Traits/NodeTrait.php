<?php

namespace Drupal\dc_endpoints\Traits;

use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

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
  public function getStorageNode($storage_type): object {
    return $this->entityTypeManager->getStorage($storage_type);
  }

  /**
   * {@inheritdoc}
   */
  public function getAllNodes($bundleFirst, $bundleSec = NULL, $status): array {
    $query = $this->entityTypeManager->getStorage('node')->getQuery();

    $orCondition = $query->orConditionGroup()
      ->condition('type', $bundleFirst);

    if ($bundleSec !== NULL) {
      $orCondition->condition('type', $bundleSec);
    }

    $result = $query
      ->condition($orCondition)
      ->condition('status', $status)
      ->accessCheck(FALSE)
      ->execute();

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($result);

    return $nodes;
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

  /**
   * Get the path alias through the taxonomy id.
   */
  public function getTaxonomyTermAlias(int $term_id): string {
    // Load the taxonomy term.
    $term = Term::load($term_id);

    // Get the URL object for the taxonomy term.
    $url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $term->id()])->toString();

    if (substr_count($url, '/') === 2) {
      $taxonomy = explode('/', $url);
    }

    $alias = $taxonomy[2];

    if (strpos($alias, '-') !== FALSE) {
      $alias = str_replace('-', ' ', $alias);
    }

    // Get the path alias from the URL object.
    return ucwords($alias);
  }

}
