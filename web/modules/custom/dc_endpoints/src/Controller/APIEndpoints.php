<?php

namespace Drupal\dc_endpoints\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for fetching all nodes.
 */
class APIEndpoints extends ControllerBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface;
   */
  protected $fileUrlGenerator;

  
  /**
   * Constructs a new CustomModuleController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file system service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FileUrlGeneratorInterface $file_url_generator) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('file_url_generator')
    );
  }

  /**
   * Returns JSON response with all nodes.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function pageAndArticle() {

    $node_storage = $this->entityTypeManager->getStorage('node');
    $query = $node_storage->getQuery()
      ->accessCheck(FALSE);
    $nids = $query->execute();

    $nodes = [];
    foreach ($nids as $nid) {
      $node = $node_storage->load($nid);

      if ($node->bundle() != 'page' && $node->bundle() != 'article') {
        continue;
      }

      $image_base = $node->get('field_image');

      $file_path = $image_base->entity->getFileUri() ?? '';

      $url = $this->fileUrlGenerator->generate($file_path);

      $relative_url = $url->toString();

      $nodes[] = [
        'id' => $node->id(),
        'title' => $node->label(),
        'url' => $relative_url,
        'alt' => $image_base->alt,
      ];
    }

    return new JsonResponse($nodes);

  }

}