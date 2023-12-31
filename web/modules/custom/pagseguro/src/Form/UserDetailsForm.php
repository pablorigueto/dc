<?php

namespace Drupal\pagseguro\Form;

use Drupal\Component\Utility\EmailValidatorInterface as UtilityEmailValidatorInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\pagseguro\PagSeguroTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a form to sendo offer to owner.
 */
class UserDetailsForm extends FormBase {
  /**
   * Trait to use the methods/helpers.
   */
  use PagSeguroTrait;

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
    return 'user_details_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    // $currentRequest = $this->requestStack->getCurrentRequest();
    // $nodeId = $currentRequest->query->get('nid');
    // $currentLanguage = $this->currentLanguage()->getId();

    $userData = $this->getUserInfos();

    $form['order_info_wrapper_parent'] = $this->orderForm($userData);

    $form['contact_info_wrapper_parent'] = $this->contactForm($userData);

    $form['address_info_wrapper_parent'] = $this->addressForm($userData);

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Enviar Proposta'),
    ];

    return $form;
  }

  protected function orderForm($userData): array {

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['order-info-wrapper-inside']],

      'order_info_wrapper' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['order-info-wrapper']],

        'full_name' => [
          '#type' => 'textfield',
          '#id' => 'name',
          '#title' => $this->t('Full name'),
          '#required' => 'true',
          '#prefix' => '<div class="full-name">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'full-name',
          ],
          '#default_value' => !empty($userData['full_name']) ? $userData['full_name'] : '',
        ],
        'birthday' => [
          '#type' => 'textfield',
          '#title' => $this->t('Birthday'),
          '#required' => TRUE,
          '#prefix' => '<div class="birthday">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'birthday',
          ],
          '#default_value' => !empty($userData['birthday']) ? $userData['birthday'] : '',
        ],
        'phone' => [
          '#type' => 'tel',
          '#title' => $this->t('Phone number'),
          '#required' => TRUE,
          '#prefix' => '<div class="phone">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'phone',
          ],
          '#default_value' => !empty($userData['phone']) ? $userData['phone'] : '',
        ],
        'id_personal' => [
          '#type' => 'textfield',
          '#title' => $this->t('ID or Passport'),
          '#required' => TRUE,
          '#prefix' => '<div class="id-personal">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'id-personal',
          ],
          '#default_value' => !empty($userData['id_personal']) ? $userData['id_personal'] : '',
        ],
        'id_personal' => [
          '#type' => 'textfield',
          '#title' => $this->t('ID or Passport'),
          '#required' => TRUE,
          '#prefix' => '<div class="id-personal">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'id-personal',
          ],
          '#default_value' => !empty($userData['id_personal']) ? $userData['id_personal'] : '',
        ],

        '#prefix' => '<div class="order-info-wrapper">',
        '#suffix' => '</div>',
      ],
    ];
  }

  protected function contactForm(array $userData): array {
    return [
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
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'full-name',
          ],
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
            'id' => 'email',
          ],
          '#default_value' => !empty($userData['email']) ? $userData['email'] : '',
        ],
        'birthday' => [
          '#type' => 'textfield',
          '#title' => $this->t('Birthday'),
          '#required' => TRUE,
          '#prefix' => '<div class="birthday">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'birthday',
          ],
          '#default_value' => !empty($userData['birthday']) ? $userData['birthday'] : '',
        ],
        'phone' => [
          '#type' => 'tel',
          '#title' => $this->t('Phone number'),
          '#required' => TRUE,
          '#prefix' => '<div class="phone">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'phone',
          ],
          '#default_value' => !empty($userData['phone']) ? $userData['phone'] : '',
        ],
        'id_personal' => [
          '#type' => 'textfield',
          '#title' => $this->t('ID or Passport'),
          '#required' => TRUE,
          '#prefix' => '<div class="id-personal">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'id-personal',
          ],
          '#default_value' => !empty($userData['id_personal']) ? $userData['id_personal'] : '',
        ],

        '#prefix' => '<div class="contact-info-wrapper">',
        '#suffix' => '</div>',
      ],
    ];
  }

  public function addressForm(array $userData): array {
    return  [
      '#type' => 'container',
      '#attributes' => ['class' => ['address-info-wrapper-inside']],

      'address_info_wrapper' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['address-info-wrapper']],
        'zip_code' => [
          '#type' => 'textfield',
          '#title' => $this->t('Zip code'),
          '#required' => TRUE,
          '#prefix' => '<div class="zip-code">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'zip-code',
          ],
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
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'address',
          ],
          '#default_value' => !empty($userData['address']) ? $userData['address'] : '',
        ],
        'number_address' => [
          '#type' => 'textfield',
          '#title' => $this->t('Number address'),
          '#required' => TRUE,
          '#prefix' => '<div class="number-address">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'number-address',
          ],
          '#default_value' => !empty($userData['number_address']) ? $userData['number_address'] : '',
        ],
        'complement_address' => [
          '#type' => 'textfield',
          '#title' => $this->t('Complement address'),
          '#required' => TRUE,
          '#prefix' => '<div class="complement-address">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'complement-address',
          ],
          '#default_value' => !empty($userData['complement_address']) ? $userData['complement_address'] : '',
        ],
        'district' => [
          '#type' => 'textfield',
          '#title' => $this->t('District'),
          '#required' => TRUE,
          '#prefix' => '<div class="district">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'district',
          ],
          '#default_value' => !empty($userData['district']) ? $userData['district'] : '',
        ],
        'city' => [
          '#type' => 'textfield',
          '#title' => $this->t('City'),
          '#required' => TRUE,
          '#prefix' => '<div class="city">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'city',
          ],
          '#default_value' => !empty($userData['city']) ? $userData['city'] : '',
        ],
        'region' => [
          '#type' => 'textfield',
          '#title' => $this->t('State/Province'),
          '#required' => TRUE,
          '#prefix' => '<div class="region">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'region',
          ],
          '#default_value' => !empty($userData['region']) ? $userData['region'] : '',
        ],
        'country' => [
          '#type' => 'textfield',
          '#title' => $this->t('Country'),
          '#required' => TRUE,
          '#prefix' => '<div class="country">',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => ['input-cadastro'],
            'id' => 'country',
          ],
          '#default_value' => !empty($userData['country']) ? $userData['country'] : '',
        ],

        '#prefix' => '<div class="address-info-wrapper">',
        '#suffix' => '</div>',
      ],
    ];

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



}
