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

    $all_nodes = $this->getSpecificsNodes(1, 'article', 'page',);

    $nodes = [];
    foreach ($all_nodes as $node) {
      $node_storage = $this->entityTypeManager->getStorage('node');
      $single_node = $node_storage->load($node->id());
      $url_node_path = $this->getPathAlias($node->id());

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

      $ownerFromNodNameNImage = $this->ownerNameNImageFromNode($single_node);

      $node_view_count = $this->nodeViewCount($single_node);

      $commentsCount = $this->commentsCount($single_node);

      $likesCount = $this->likesCount($single_node);

      $nodeCreated = $this->nodeCreated($single_node);

      $nodes[] = [
        'node_path' => $url_node_path,
        'node_owner_name' => $ownerFromNodNameNImage['user_name'],
        'node_owner_image_profile' => $ownerFromNodNameNImage['user_img_profile'],
        'id' => $single_node->id(),
        'title' => $single_node->label(),
        'url' => $relative_url,
        'alt' => $image_base->alt,
        'tags' => $tags,
        'node_view_count' => $node_view_count,
        'comments_count' => $commentsCount,
        'likes_count' => $likesCount,
        'node_created' => $nodeCreated,
      ];
    }

    return new JsonResponse($nodes);

  }


  /**
   * Returns JSON response with all nodes.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function newContentHighlight() {

    $langcode = $this->currentLanguage();

    $all_nodes = $this->getThreeRecentNodes();

    $nodes = [];
    foreach ($all_nodes as $node) {
      $node_storage = $this->entityTypeManager->getStorage('node');
      $single_node = $node_storage->load($node->id());
      $url_node_path = $this->getPathAlias($node->id());

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

      $ownerFromNodNameNImage = $this->ownerNameNImageFromNode($single_node);

      $node_view_count = $this->nodeViewCount($single_node);

      $commentsCount = $this->commentsCount($single_node);

      $likesCount = $this->likesCount($single_node);

      $nodeCreated = $this->nodeCreated($single_node);

      $titleNSubTitle = $this->strpTagAndSplitTitle($single_node);

      $nodes[] = [
        'button_new_content' => $this->t('New Content'),
        'title' => $titleNSubTitle['title'],
        'sub_title' => $titleNSubTitle['sub_title'],
        'node_path' => $url_node_path,
        'node_owner_name' => $ownerFromNodNameNImage['user_name'],
        'node_owner_image_profile' => $ownerFromNodNameNImage['user_img_profile'],
        'id' => $single_node->id(),
        'title' => $single_node->label(),
        'url' => $relative_url,
        'alt' => $image_base->alt,
        'tags' => $tags,
        'node_view_count' => $node_view_count,
        'comments_count' => $commentsCount,
        'likes_count' => $likesCount,
        'node_created' => $nodeCreated,
      ];
    }

    return new JsonResponse($nodes);

  }

}
