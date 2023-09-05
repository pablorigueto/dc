<?php

namespace Drupal\dc_init_form\Form;

use Drupal\Component\Utility\EmailValidatorInterface as UtilityEmailValidatorInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
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
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a form to sendo offer to owner.
 */
class InitialForm extends FormBase {

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
   * {@inheritdoc}
   */
  public function __construct(
    MessengerInterface $messenger,
    EntityTypeManagerInterface $entityTypeManager,
    LanguageManagerInterface $languageManager,
    RequestStack $requestStack,
    RendererInterface $renderer,
    UtilityEmailValidatorInterface $emailValidator,
    Connection $database
  ) {
    $this->messenger = $messenger;
    $this->entityTypeManager = $entityTypeManager;
    $this->languageManager = $languageManager;
    $this->requestStack = $requestStack;
    $this->renderer = $renderer;
    $this->emailValidator = $emailValidator;
    $this->database = $database;
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
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'clt_init_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['clt_init_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['selected-inside']],
      'clt_proposal' => [
        '#type' => 'select',
        '#title' => $this->t('Proposta'),
        '#required' => TRUE,
        '#options' => [
          'leve' => 'Leve',
          'media' => 'Media',
        ],
        '#prefix' => '<div class="clt_proposal_class">',
        '#suffix' => '</div>',
        '#ajax' => [
          'callback' => [$this, 'updateOfferToImageField'],
          'event' => 'change',
          'wrapper' => 'offer-image-wrapper',
          'progress' => [
            'type' => 'throbber',
            'message' => NULL,
          ],
        ],
      ],
      'rf_image' => [
        '#type' => 'container',
        '#prefix' => '<div class="new-offer-image">',
        '#suffix' => '</div>',
        '#attributes' => [
          'id' => 'offer-image-wrapper',
        ],
      ],
      '#prefix' => '<div class="selected-car-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Enviar Formulário'),
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
  public function updateOfferToImageField(array $form, FormStateInterface $form_state): AjaxResponse {

    dump($form);

    $image_id = $form_state->getValue('clt_proposal');

    if ($image_id == 'leve') {
      $uri = 'leve-icon.jpeg';
    }
    // $car_uri = $this->singleThumb($nid);

    // $siteUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();

    // $car_uri = '<a href="' . $siteUrl . $this->getPathAlias($nid) .
    //   '" target="_blank" class="offer-class-btn"><div class="new-offer-image">' .
    //   $car_uri . '</div></a>';

    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('#offer-image-wrapper', $uri));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // $email = $form_state->getValue('email_offer');

    // // Check if the email address is valid.
    // if (!$this->emailValidator->isValid($email)) {
    //   $form_state->setErrorByName('email', $this->t('Please enter a valid email address.'));
    // }

    // // Check if the email address is from a banned domain.
    // $banned_domains = ['example.com', 'test.com'];
    // $domain = substr(strrchr($email, "@"), 1);
    // if (in_array($domain, $banned_domains)) {
    //   $form_state->setErrorByName('email', $this->t('Email inválido, ex: example@example.com.'));
    // }

    // $telephone = $form_state->getValue('phone_offer');

    // // Remove all non-numeric characters from the telephone number.
    // $telephone = preg_replace('/\D/', '', $telephone);

    // // Check if the telephone number is valid.
    // if (strlen($telephone) <= 7 || strlen($telephone) >= 12 || !preg_match('/^([0-9]{2})([0-9]{8,9})$/', $telephone)) {
    //   $form_state->setErrorByName('telephone', $this->t('Nro de telefone inválido, ex: (19) 99999-9999.'));
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

    // Get the current time in the default timezone.
    $current_time = new DrupalDateTime();
    $formattedDate = $current_time->format('d-m-Y H:i:s');

    // This info comes from 2 different ways and need to be trait.
    $offer_to_car = $form_state->getValue('offer_to_car');
    // If the offer car is not numeric, trait it.
    if (!ctype_digit($offer_to_car)) {
      $parts = explode(" ", $offer_to_car);
      $offer_to_car = trim($parts[1]);
    }

    $name_offer = $form_state->getValue('name_offer');
    $email_offer = $form_state->getValue('email_offer');
    $phone_offer = $form_state->getValue('phone_offer');
    $bday_offer = $form_state->getValue('bday_offer');
    $down_offer = $form_state->getValue('down_offer');
    $clt_id = $form_state->getValue('clt_id');
    $checkbox_with_car_offer = $form_state->getValue('checkbox_with_car_offer');
    $clt_debits_offer = $form_state->getValue('clt_debits_offer');
    $clt_extra_info = $form_state->getValue('clt_extra_info');
    $clt_car_brand_offer = $form_state->getValue('clt_car_brand_offer');
    $clt_car_model_offer = $form_state->getValue('clt_car_model_offer');
    $clt_car_gearshift_offer = $form_state->getValue('clt_car_gearshift_offer');
    $clt_car_fuel_offer = $form_state->getValue('clt_car_fuel_offer');
    $clt_car_kmormiles_offer = $form_state->getValue('clt_car_kmormiles_offer');
    $clt_car_color_offer = $form_state->getValue('clt_car_color_offer');
    $clt_car_sttyear_offer = $form_state->getValue('clt_car_sttyear_offer');
    $clt_car_endyear_offer = $form_state->getValue('clt_car_endyear_offer');
    $clt_car_id_offer = $form_state->getValue('clt_car_id_offer');

    // Save the form data to the database.
    $this->database->insert('clt_offer_table')
      ->fields([
        'nodeId' => $offer_to_car,
        'create_time' => $formattedDate,
        'name_offer' => $name_offer,
        'email_offer' => $email_offer,
        'phone_offer' => $phone_offer,
        'bday_offer' => $bday_offer,
        'down_offer' => $down_offer,
        'clt_id' => $clt_id,
      ])
      ->execute();

    $this->messenger->addMessage($this->t('Proposta enviada com sucesso!'));

    // Redirect to sell page after form submission.
    $response = new RedirectResponse($this->getPathAlias($offer_to_car));
    $response->send();

  }

  /**
   * {@inheritdoc}
   */
  public function currentLanguage(): mixed {
    return $this->languageManager->getCurrentLanguage();
  }

}
