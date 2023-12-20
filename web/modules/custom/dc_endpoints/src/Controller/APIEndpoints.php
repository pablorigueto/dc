<?php

namespace Drupal\dc_endpoints\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\dc_endpoints\Traits\NodeTrait;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for fetching all nodes.
 */
class APIEndpoints extends ControllerBase {
  use NodeTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * The Language Manager Interface.
   *
   * @var Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new CustomModuleController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file system service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The file system service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    FileUrlGeneratorInterface $file_url_generator,
    LanguageManagerInterface $languageManager,
    ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileUrlGenerator = $file_url_generator;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('file_url_generator'),
      $container->get('language_manager'),
    );
  }

  /**
   * Returns JSON response with all nodes.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function pageAndArticle() {

    $langcode = $this->currentLanguage();

    $all_nodes = $this->getAllNodes('article', 'page', 1);

    $nodes = [];
    foreach ($all_nodes as $node) {
      $node_storage = $this->entityTypeManager->getStorage('node');
      $single_node = $node_storage->load($node->id());

      // If the node didn't have translation move to the next one.
      $single_node = $this->getTranslationField($node, $langcode);
      if ($single_node === FALSE) {
        $single_node = $node;
      }

      $image_base = $single_node->get('field_image')[0];
      $file_path = $image_base->entity->getFileUri() ?? '';
      $url = $this->fileUrlGenerator->generate($file_path);
      $relative_url = $url->toString();

      $tags = $this->tagsNode($single_node);

      $nodes[] = [
        'id' => $single_node->id(),
        'title' => $single_node->label(),
        'url' => $relative_url,
        'alt' => $image_base->alt,
        'tags' => $tags,
      ];
    }

    return new JsonResponse($nodes);

  }

  /**
   * Returns all tags from field.
   *
   */
  protected function tagsNode($node) {
    $field_items = $node->get('field_tags');

    $tags = [];
    foreach ($field_items as $item) {

      $toxonomy_alias = $this->getTaxonomyTermAlias($item->target_id);

      $tags[] = [
        'id' => $item->target_id,
        'alias' => $toxonomy_alias,
      ];
    }

    return $tags;
  }

}
