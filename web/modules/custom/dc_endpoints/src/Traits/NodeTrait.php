<?php

namespace Drupal\dc_endpoints\Traits;

use Drupal\Core\Datetime\DrupalDateTime;
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
  public function getSpecificsNodes($status, $bundleFirst, $bundleSec = NULL): array {
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
   * {@inheritdoc}
   */
  public function getThreeRecentNodes(): array {
    $query = $this->entityTypeManager->getStorage('node')->getQuery();

    $result = $query
      ->condition('type', 'ckeditor', '<>')
      ->condition('status', 1)
      ->accessCheck(FALSE)
      ->sort('created', 'DESC')
      ->execute();

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($result);

    // Get the last three nodes more recent nodes.
    return array_slice($nodes, 0, 3);
  }

  /**
   * Langcode from cookie or current.
   */
  public function langcode() {

    $cookie_language = \Drupal::request()->cookies->get('selectedLanguage');

    if ($cookie_language !== NULL) {
      return str_replace('/', '', $cookie_language);
    }

    return $this->currentLanguage();
  }

  /**
   * Get the path alias through the node id.
   */
  public function getPathAlias(int $node_id, string $langcode): string|bool {

    // // This code doesn't worked. It always return the default language.
    // $language = \Drupal::languageManager()->getCurrentLanguage();

    // Get the language manager service.
    $language_manager = \Drupal::languageManager();

    // Get an array of all languages.
    $languages = $language_manager->getLanguages();

    // Generate the canonical URL for the node with the specified language.
    $url = Url::fromRoute('entity.node.canonical', ['node' => $node_id], ['language' => $languages[$langcode]]);

    // Retrieve the path from the URL object.
    $path = $url->toString();
    // If I wont the language on return .e.g en-us/alias.
    // $path = '/' . $url->getInternalPath();

    $pathAlias = \Drupal::service('path_alias.manager')->getAliasByPath($path);

    if (strpos($pathAlias, '/node/') !== FALSE) {
      return FALSE;
    }

    // Get the translated alias for the path.
    //$translated_alias = \Drupal::service('path_alias.manager')->getPathAlias($path);
    return \Drupal::service('path_alias.manager')->getAliasByPath($path);

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

  protected function ownerNameNImageFromNode($node) {

    $uid = $node->getOwnerId();
    $user_storage = $this->entityTypeManager->getStorage('user');
    $user = $user_storage->load($uid);

    // Check if the user exists
    if ($user) {
      // Get the user profile image field
      $profile_image = $user->get('user_picture')->entity;

      // Check if the profile image exists
      if ($profile_image) {
        // Get the URI of the profile image
        $file_path = $profile_image->getFileUri();
        $url = $this->fileUrlGenerator->generate($file_path);
        $relative_url = $url->toString();
      }
    }

    $user_name = $user_storage->load($uid)->getAccountName();

    return [
      'user_name' => $user_name,
      'user_img_profile' => $relative_url,
    ];

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

  /**
   * Returns all tags from field.
   *
   */
  protected function nodeViewCount($node) {

    $viewCount = \Drupal::database()->select('node_counter', 'h')
      ->condition('h.nid', $node->id())
      ->fields('h', ['totalcount'])
      ->execute()
      ->fetchField();

    $node_view_count = 0;

    if (!empty($viewCount)) {
      // Add the view count to the template variables.
      $node_view_count = $viewCount;
    }

    return $node_view_count;

  }

  /**
   * Returns all tags from field.
   *
   */
  protected function commentsCount($node) {
    $commentsCount = 0;
    $comments = $node->get('comment')->getValue();
    if (!empty($comments)) {
      $commentsCount = $node->get('comment')[0]->getValue()['comment_count'] ?? 0;
    }

    return $commentsCount;
  }

  /**
   * Returns all tags from field.
   *
   */
  protected function likesCount($node) {

    $likes = $node->get('field_like')->getValue();
    if (!empty($likes)) {
      $likesCount = $node->get('field_like')[0]->getValue()['likes'];
    }

    if (empty($likesCount)) {
      $likesCount = 0;
    }

    return $likesCount;
  }

  /**
   * Returns all tags from field.
   *
   */
  protected function nodeCreated($node) {
    $timestamp = $node->created->getValue()[0]['value'];
    //return $this->formateDate($timestamp);
    return $this->formatDateAgo($timestamp);
  }

  /**
   * Returns all tags from field.
   *
   */
  protected function nodeTimeStamp($node) {
    return $node->created->getValue()[0]['value'];
  }

  /**
   * Returns all tags from field.
   *
   */
  protected function formateDate($timestamp) {
    $datetime = DrupalDateTime::createFromTimestamp($timestamp);
    return $datetime->format('M d, Y');
  }

  /**
   * Returns all tags from field.
   *
   */
  protected function strpTagAndSplitTitle($node) {

    $titleNSubTitle = $node->get('body')[0]->getValue()['value'];

    // Split into two parts.
    $parts = explode('</h1>', $titleNSubTitle, 2);

    // Remove HTML tags.
    $title = isset($parts[0]) ? strip_tags($parts[0]) : '';
    $subTitle = isset($parts[1]) ? strip_tags($parts[1]) : '';

    return [
      'title' => $title,
      'sub_title' => $subTitle,
    ];

  }

  public function formatDateAgo($timestamp) {
    // $time = DrupalDateTime::createFromTimestamp($timestamp);
    // // Format the created and change time into the desired pattern.
    // return $time->format('D, M/d/Y - H:i');
  
    // Create a DrupalDateTime object for the provided timestamp.
    $time = DrupalDateTime::createFromTimestamp($timestamp);
    $current_time = new DrupalDateTime();
  
    $interval = $current_time->diff($time);
    $formatted_date = '';
  
    // Calculate the total number of days and remaining hours.
    $days = $interval->days;
    $hours = $interval->h;
    $minutes = $interval->i;
  
    // Format the output.
    if ($days > 0) {
      $formatted_date .= $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }
  
    // if ($hours > 0) {
    //   if ($formatted_date !== '') {
    //     $formatted_date .= ' and ';
    //   }
    //   $formatted_date .= $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    // }
  
    // If the time difference is less than 24 hours, use a different format.
    if ($interval->days === 0 && $interval->h < 24) {
      $formatted_date = $hours . ' hours ago';
      if ($interval->h === 0) {
        $formatted_date = $minutes . ' min ago';
      }
    }
  
    return $formatted_date;
  
  }

}
