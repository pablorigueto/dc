<?php

namespace Drupal\dc_endpoints\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
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
   * Constructs a new CustomModuleController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns JSON response with all nodes.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function getAllNodes() {

    // $node_storage = $this->entityTypeManager->getStorage('node');
    // $query = $node_storage->getQuery();
    // $nids = $query->execute();

    // $nodes = [];
    // foreach ($nids as $nid) {
    //   $node = $node_storage->load($nid);
    //   $nodes[] = [
    //     'id' => $node->id(),
    //     'title' => $node->getTitle(),
    //     // Add more fields as needed.
    //   ];
    // }

    // return new JsonResponse($nodes);

    $node_storage = $this->entityTypeManager->getStorage('node');
    $query = $node_storage->getQuery()
      ->accessCheck(FALSE);
    $nids = $query->execute();

    $nodes = [];
    foreach ($nids as $nid) {
      $node = $node_storage->load($nid);
      $nodes[] = [
        'id' => $node->id(),
        'title' => $node->label(),
      ];
    }

    $test = new JsonResponse($nodes);

    return new JsonResponse($nodes);

  }
}
