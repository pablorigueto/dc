<?php

namespace Drupal\pagseguro\Form;

use Drupal\Component\Utility\EmailValidatorInterface as UtilityEmailValidatorInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\user\Entity\User;
use Psr\Log\LoggerInterface;

/**
 * Provides a form to sendo offer to owner.
 */
class SubscriberForm extends FormBase {

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The Entity Type Manager Interface.
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Language Manager Interface.
   *
   * @var Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The Request Stack service.
   *
   * @var Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The render image service.
   *
   * @var Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The email validator service.
   *
   * @var Drupal\Core\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * The Drupal database.
   *
   * @var Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The Drupal database.
   *
   * @var Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    MessengerInterface $messenger,
    EntityTypeManagerInterface $entityTypeManager,
    LanguageManagerInterface $languageManager,
    RequestStack $requestStack,
    RendererInterface $renderer,
    UtilityEmailValidatorInterface $emailValidator,
    Connection $database,
    LoggerInterface $logger,
  ) {
    $this->messenger = $messenger;
    $this->entityTypeManager = $entityTypeManager;
    $this->languageManager = $languageManager;
    $this->requestStack = $requestStack;
    $this->renderer = $renderer;
    $this->emailValidator = $emailValidator;
    $this->database = $database;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('messenger'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('request_stack'),
      $container->get('renderer'),
      $container->get('email.validator'),
      $container->get('database'),
      $container->get('logger.factory')->get('pagseguro')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'subscriber_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    // $currentRequest = $this->requestStack->getCurrentRequest();
    // $nodeId = $currentRequest->query->get('nid');
    // $currentLanguage = $this->currentLanguage()->getId();

    $userData = $this->getUserInfos();

    $form['offer_to_car_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['selected-car-wrapper-inside']],
      'offer_to_car' => [
        '#type' => 'textfield',
        '#title' => $this->t('Proposta para o carro'),
        '#prefix' => '<div class="car-details-to-offer">',
        '#suffix' => '</div>',
        '#ajax' => [
          'callback' => [$this, 'updateAddressField'],
          'event' => 'change',
          'wrapper' => 'offer_to_car_image', // Use the ID of the textfield as the wrapper
          'progress' => [
            'type' => 'throbber',
            'message' => NULL,
          ],
        ],
      ],
      'offer_to_car_image' => [
        '#type' => 'textfield',
        '#title' => $this->t('Offer to car image'),
        '#prefix' => '<div class="new-offer-image">',
        '#suffix' => '</div>',
        '#attributes' => [
          'id' => 'offer_to_car_image',
        ],
      ],
      'offer_to_car_image2' => [
        '#type' => 'textfield',
        '#title' => $this->t('Offer to car image'),
        '#prefix' => '<div class="new-offer-image">',
        '#suffix' => '</div>',
        '#attributes' => [
          'id' => 'offer_to_car_image2',
        ],
      ],  
      '#prefix' => '<div class="selected-car-wrapper">',
      '#suffix' => '</div>',
    ];
  
    $form['contact_info_wrapper_parent'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['contact-info-wrapper-inside']],
    
      'contact_info_wrapper' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['contact-info-wrapper']],
    
        'full_name' => [
          '#type' => 'textfield',
          '#id' => 'name',
          '#title' => $this->t('Full name'),
          '#required' => 'true',
          '#prefix' => '<div class="full-name">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['full_name']) ? $userData['full_name'] : '',
        ],
        'email' => [
          '#type' => 'hidden',
          // '#type' => 'email',
          '#id' => 'email',
          '#title' => 'E-mail',
          '#required' => TRUE,
          '#prefix' => '<div class="email">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'readonly' => 'readonly', // Set the readonly attribute
          ],
          '#default_value' => !empty($userData['email']) ? $userData['email'] : ''
        ],

        // Add the new fields here
        'birthday' => [
          '#type' => 'textfield',
          '#title' => $this->t('Birthday'),
          '#required' => TRUE,
          '#prefix' => '<div class="birthday">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['birthday']) ? $userData['birthday'] : '',
        ],
        'phone' => [
          '#type' => 'tel',
          '#title' => $this->t('Phone number'),
          '#required' => TRUE,
          '#prefix' => '<div class="phone">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['phone']) ? $userData['phone'] : '',
        ],
        'id_personal' => [
          '#type' => 'textfield',
          '#title' => $this->t('ID or Passport'),
          '#required' => TRUE,
          '#prefix' => '<div class="id-personal">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['id_personal']) ? $userData['id_personal'] : '',
        ],
        'zip_code' => [
          '#type' => 'textfield',
          '#title' => $this->t('Zip code'),
          '#required' => TRUE,
          '#prefix' => '<div class="zip-code">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['zip_code']) ? $userData['zip_code'] : '',
        ],
        'postcode_btn' => [
          '#type' => 'button',
          '#value' => $this->t('Find your address through postcode'),
          '#prefix' => '<div class="postcode-btn">',
          '#suffix' => '</div>',
          '#ajax' => [
            'callback' => [$this, 'updateAddressField'],
            'event' => 'click', //'change',
            'progress' => [
              'type' => 'throbber',
              'message' => NULL
            ],
          ],
        ],
        'address' => [
          '#type' => 'textfield',
          '#title' => $this->t('Address'),
          '#required' => TRUE,
          '#prefix' => '<div class="address">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['address']) ? $userData['address'] : '',
          '#attributes' => [
            'id' => 'address',
          ],
        ],
        'number_address' => [
          '#type' => 'textfield',
          '#title' => $this->t('Number address'),
          '#required' => TRUE,
          '#prefix' => '<div class="number-address">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['number_address']) ? $userData['number_address'] : '',
        ],
        'district' => [
          '#type' => 'textfield',
          '#title' => $this->t('District'),
          '#required' => TRUE,
          '#prefix' => '<div class="district">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['district']) ? $userData['district'] : '',
        ],
        'city' => [
          '#type' => 'textfield',
          '#title' => $this->t('City'),
          '#required' => TRUE,
          '#prefix' => '<div class="city">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['city']) ? $userData['city'] : '',
        ],
        'region' => [
          '#type' => 'textfield',
          '#title' => $this->t('State/Province'),
          '#required' => TRUE,
          '#prefix' => '<div class="region">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['region']) ? $userData['region'] : '',
        ],
        'country' => [
          '#type' => 'textfield',
          '#title' => $this->t('Country'),
          '#required' => TRUE,
          '#prefix' => '<div class="country">',
          '#suffix' => '</div>',
          '#attributes' => ['class' => ['input-cadastro']],
          '#default_value' => !empty($userData['country']) ? $userData['country'] : '',
        ],
    
        '#prefix' => '<div class="contact-info-wrapper">',
        '#suffix' => '</div>',
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Enviar Proposta'),
    ];

    return $form;
  }

  /**
   * Ajax callback to update the player options list.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function updateAddressField(array $form, FormStateInterface $form_state): AjaxResponse|bool {

    //$response->addCommand(new HtmlCommand('#updatedField', 'removeEntireStructure'));

    $post_code = $form_state->getValue('zip_code');

    // if (empty($post_code)) {
    //   return FALSE;
    //   //$form_state->setErrorByName('zip_code', $this->t('Please, fill with a valid postcode.'));
    // }
    $response = new AjaxResponse();
    $url = 'https://viacep.com.br/ws/'.$post_code.'/json';
    $response_data = file_get_contents($url);
    // Parse the JSON response
    $data = json_decode($response_data);

    // "cep": "13825-000",
    // "logradouro": "",
    // "complemento": "",
    // "bairro": "",
    // "localidade": "Holambra",
    // "uf": "SP",
    // "ibge": "3519055",
    // "gia": "7470",
    // "ddd": "19",
    // "siafi": "2953"

    $response->addCommand(new InvokeCommand('#address', 'val', [$data->localidade]));

    $response->addCommand(new InvokeCommand('#offer_to_car_image', 'val', [$data->cep]));

  
    return $response;
  }

  
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {

    $post_code = $form_state->getValue('zip_code');

    if (empty($post_code)) {
      $form_state->setErrorByName('zip_code', $this->t('Please, fill with a valid postcode.'));
    }

    // $currentLanguage = $this->currentLanguage();
    // // // Check if the email address is valid.
    // // if (!$this->emailValidator->isValid($email)) {
    // //   $form_state->setErrorByName('email', $this->t('Please enter a valid email address.'));
    // // }

    // // // Check if the email address is from a banned domain.
    // // $banned_domains = ['example.com', 'test.com'];
    // // $domain = substr(strrchr($email, "@"), 1);
    // // if (in_array($domain, $banned_domains)) {
    // //   $form_state->setErrorByName('email', $this->t('Email inválido, ex: example@example.com.'));
    // // }

    // $telephone = $form_state->getValue('phone');
    // // Remove all non-numeric characters from the telephone number.
    // $telephone = preg_replace('/\D/', '', $telephone);

    // if ($currentLanguage === 'pt-br') {
    //   // Check if the telephone number is valid.
    //   if (strlen($telephone) <= 7 || strlen($telephone) >= 12 || !preg_match('/^([0-9]{2})([0-9]{8,9})$/', $telephone)) {
    //     $form_state->setErrorByName('telephone', $this->t('Nro de telefone inválido, ex: (19) 99999-9999.'));
    //   }
    // }
    // else {
    //   if (strlen($telephone) <= 7 || strlen($telephone) >= 20) {
    //     $form_state->setErrorByName('telephone', $this->t('Please fill using at least 8 numbers.'));
    //   }
    // }

    // $birthdate = $form_state->getValue('bday_offer');

    // // Check if the birthdate is valid.
    // if (!\DateTime::createFromFormat('d/m/Y', $birthdate)) {
    //   $form_state->setErrorByName('birthdate', $this->t('Data de Nascimento inválida, ex: 01/01/2000.'));
    // }

    // $nameOffer = $form_state->getValue('name_offer');
    // if (strlen($nameOffer) < 5) {
    //   $form_state->setErrorByName('nameOffer', $this->t('Insira nome com pelo menos 4 caracteres'));
    // }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {

    $updateAcc = $this->updateFieldsOnDB($form_state);

    // Get the current time in the default timezone.
    // $current_time = new DrupalDateTime();
    // $formattedDate = $current_time->format('d-m-Y H:i:s');

    $this->messenger->addMessage($this->t('Cadastro realizado com sucesso!'));

    // Redirect to sell page after form submission.
    $response = new RedirectResponse('/');
    $response->send();

  }

  /**
   * {@inheritdoc}
   */
  protected function updateFieldsOnDB($form_state) {
    $user = \Drupal::currentUser();
  
    // Get the values entered in the form fields.
    $full_name = $form_state->getValue('full_name');
    $birthday = $form_state->getValue('birthday');
    $phone = $form_state->getValue('phone');
    $id_personal = $form_state->getValue('id_personal');
    $email = $form_state->getValue('email');
    $address = $form_state->getValue('address');
    $number_address = $form_state->getValue('number_address');
    $zip_code = $form_state->getValue('zip_code');
    $district = $form_state->getValue('district');
    $city = $form_state->getValue('city');
    $region = $form_state->getValue('region');
    $country = $form_state->getValue('country');

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
        'zip_code' => $zip_code,
        'district' => $district,
        'city' => $city,
        'region' => $region,
        'country' => $country,
      ]);
      $query->execute();

      return TRUE;
 
    } catch (\Exception $e) {
      // Handle the exception.
      $this->logger->error('Error updating/inserting record: @error', ['@error' => $e->getMessage()]);
      // You can also display an error message to the user if needed.
      return FALSE;
    }
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

}