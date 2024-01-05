<?php

namespace Drupal\pagseguro;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormStateInterface;
use Normalizer;

trait PagSeguroTrait {

  protected function performCurlRequest($url) {
    $_h = curl_init();
    curl_setopt($_h, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=ISO-8859-1"));
    curl_setopt($_h, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($_h, CURLOPT_HTTPGET, 1);
    curl_setopt($_h, CURLOPT_URL, $url);
    curl_setopt($_h, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($_h, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($_h, CURLOPT_DNS_USE_GLOBAL_CACHE, FALSE);
    curl_setopt($_h, CURLOPT_DNS_CACHE_TIMEOUT, 2);
    $output = curl_exec($_h);
    curl_close($_h);

    return simplexml_load_string($output);

  }

  /**
   * {@inheritdoc}
   */
  protected function updateFieldsOnDB($form_state) {
    $user = \Drupal::currentUser();

    $full_name = $this->encondingAccent($form_state->getValue('full_name'));
    $birthday = $this->encondingAccent($form_state->getValue('birthday'));
    $phone = $this->encondingAccent($form_state->getValue('phone'));
    $id_personal = $this->encondingAccent($form_state->getValue('id_personal'));
    $email = $this->encondingAccent($form_state->getValue('email'));
    $address = $this->encondingAccent($form_state->getValue('address'));
    $number_address = $this->encondingAccent($form_state->getValue('number_address'));
    $complement_address = $this->encondingAccent($form_state->getValue('complement_address'));
    $zip_code = $this->encondingAccent($form_state->getValue('zip_code'));
    $district = $this->encondingAccent($form_state->getValue('district'));
    $city = $this->encondingAccent($form_state->getValue('city'));
    $region = $this->encondingAccent($form_state->getValue('region'));
    $country = $this->encondingAccent($form_state->getValue('country'));

    // Create a database connection.
    $connection = $this->database;

    // Check if a record with the user's UID already exists.
    $existing_record = $connection->select('pagseguro_users_data', 'p')
      ->fields('p')
      ->condition('p.user_id', $user->id())
      ->execute()
      ->fetchAssoc();

    try {
      if ($existing_record) {
        // If a record exists, update it.
        $query = $connection->update('pagseguro_users_data');
        $query->fields([
          'full_name' => $full_name,
          'birthday' => $birthday,
          'phone' => $phone,
          'id_personal' => $id_personal,
          'email' => $email,
          'address' => $address,
          'number_address' => $number_address,
          'complement_address' => $complement_address,
          'zip_code' => $zip_code,
          'district' => $district,
          'city' => $city,
          'region' => $region,
          'country' => $country,
        ]);
        $query->condition('user_id', $user->id());
        $query->execute();

        return TRUE;
      }

      // Insert a new record if it doesn't exist.
      $query = $connection->insert('pagseguro_users_data');
      $query->fields([
        'user_id' => $user->id(),
        'full_name' => $full_name,
        'birthday' => $birthday,
        'phone' => $phone,
        'id_personal' => $id_personal,
        'email' => $email,
        'address' => $address,
        'number_address' => $number_address,
        'complement_address' => $complement_address,
        'zip_code' => $zip_code,
        'district' => $district,
        'city' => $city,
        'region' => $region,
        'country' => $country,
      ]);
      $query->execute();

      return TRUE;

    }
    catch (\Exception $e) {
      // Handle the exception.
      $this->logger->error('Error updating/inserting record: @error', ['@error' => $e->getMessage()]);
      // You can also display an error message to the user if needed.
      return FALSE;
    }
  }

  /**
   * Function to remove accents from a string.
   */
  protected function encondingAccent($str) {
    // Normalize the string to remove accents.
    $normalizedStr = Normalizer::normalize($str, Normalizer::FORM_KD);

    return preg_replace('/[^\x20-\x7E]/u', '', $normalizedStr);
  }

  /**
   * Get the path alias through the node id.
   */
  protected function getPathAlias(int $node_id): string {
    // Get the URL object for the node using its ID.
    $url = Url::fromRoute('entity.node.canonical', ['node' => $node_id]);
    // Get the path alias from the URL object.
    return $url->toString();
  }

  /**
   * {@inheritdoc}
   */
  protected function getUserInfos(): mixed {
    // Assuming you have a $user object for the current user.
    $user = \Drupal::currentUser();

    // Create a database connection.
    $connection = $this->database;

    $pagSeguroTable = $this->getUserInfoFromPagSeguroTable($connection, $user->id());

    if (isset($pagSeguroTable['email'])) {

      $resultUserMail = $this->getUserEmailFromUsersTable($connection, $user->id());

      if ($pagSeguroTable['email'] !== $resultUserMail) {

        // Update the email from pagseguro table if the user change it on users data table.
        $query = $connection->update('pagseguro_users_data');
        $query->fields([
          'email' => $resultUserMail,
        ]);
        $query->condition('user_id', $user->id());
        $query->execute();

        return $this->getUserInfoFromPagSeguroTable($connection, $user->id());

      }

      return $pagSeguroTable;

    }
  
    $resultUserMail = $this->getUserEmailFromUsersTable($connection, $user->id());

    // Insert a new record using the email from initial subscriber.
    $query = $connection->insert('pagseguro_users_data');
    $query->fields([
      'user_id' => $user->id(),
      'email' => $resultUserMail,
    ]);
    $query->execute();

    return $this->getUserInfoFromPagSeguroTable($connection, $user->id());
  
  }

  /**
   * {@inheritdoc}
   */
  protected function getUserInfoFromPagSeguroTable($connection, $userId): mixed {
    // Fetch the user's data from the database.
    $query = $connection->select('pagseguro_users_data', 'p')
      ->fields('p')
      ->condition('p.user_id', $userId);
    $result = $query->execute();
    return $result->fetchAssoc();
  }

  /**
   * {@inheritdoc}
   */
  protected function getUserEmailFromUsersTable($connection, $userId): mixed {
    // Fetch the user's email from the database.
    $query = $connection->select('users_field_data', 'ud')
      ->fields('ud')
      ->condition('ud.uid', $userId);
    $resultUserTable = $query->execute();
    $resultUserMail = $resultUserTable->fetchAssoc();
    return $resultUserMail['mail'];
  }

  /**
   * {@inheritdoc}
   */
  protected function currentLanguage(): string {
    return $this->languageManager->getCurrentLanguage()->getId();
  }


  /**
   * Ajax callback to update the user address data.
   * Keep public to allow to make external call to API. 
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function updateAddressField(array $form, FormStateInterface $form_state): AjaxResponse {

    $response = new AjaxResponse();

    $post_code = $form_state->getValue('zip_code');

    if (empty($post_code)) {
      return $response;
    }

    $url = "https://viacep.com.br/ws/{$post_code}/json";
    $response_data = file_get_contents($url);
    // Parse the JSON response.
    $data = json_decode($response_data);

    if (!$data) {
      return $response;
    }

    $response->addCommand(new InvokeCommand('#country', 'val', ['Brasil']));
    $response->addCommand(new InvokeCommand('#address', 'val', [$data->logradouro]));
    $response->addCommand(new InvokeCommand('#district', 'val', [$data->bairro]));
    $response->addCommand(new InvokeCommand('#city', 'val', [$data->localidade]));
    $response->addCommand(new InvokeCommand('#region', 'val', [$data->uf]));

    return $response;

  }

}
