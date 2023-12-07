<?php

namespace Drupal\pagseguro\Controller;

use PDO;
use DateInterval;
use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\pagseguro\CurlRequestTrait;

class PaymentSubscribe extends ControllerBase {
  /**
   * Trait to use the curl.
   */
  use CurlRequestTrait;

  /**
   * Create the plan specific to this order.
   * Each order has your own plan, it's an recommendation
   * of PagSeguro to make easier the control of plans e acc.
   */
  public function createPlan() {

    // Dados da Compra.
    $valor = \Drupal::request()->request->get('valor');
    $valor = number_format($valor, 2, ".", "");
    $assinatura = \Drupal::request()->request->get('assinatura');

    // Buscando Informações do Usuário Logado.
    $user = User::load(\Drupal::currentUser()->id());

    $period = \Drupal::request()->request->get('period');

    $credentials = $this->getCrendentialsToPagSeguro(TRUE);

    // Criando Plano de Adesão.
    $url = $credentials['linkPag'] . '/pre-approvals/request?email=' . $credentials['emailPag'] . '&token=' . $credentials['tokenPag'];

    $json = [
      'preApproval' => [
        'name' => 'Assinatura ' . ucfirst($assinatura),
        'details' => 'Assinatura Recorrente - ' . ucfirst($assinatura),
        'charge' => 'AUTO',
        'period' => $period,
        'amountPerPayment' => $valor,
        'expiration' => [
          'value' => '99',
          'unit' => 'YEARS'
        ]
      ]
    ];

    // Call the createPlanOrPayment method from the trait.
    $plan = $this->createPlanOrPayment($url, $json);

    if (!isset($plan) && $plan == '') {
      return $this->newResponse('error');
    }

    // Salva PLANO do Usuário.
    $user->set('field_plano_pagseguro', $plan);
    $user->save();

    // Get the ID Plan from PagSeguro.
    $data = $this->getTheIdPlan($credentials['linkPag'], $credentials['emailPag'], $credentials['tokenPag'], $plan);

    return $this->newResponse($data);
  }

  /**
   * Função para Finalizar Pagamento Recorrente com a API do PagSeguro
   */
  public function payThePlan() {
  
    $credentials = $this->getCrendentialsToPagSeguro(TRUE);

    // Montando link para pagamento.
    $url = $credentials['linkPag'] . '/pre-approvals?email=' . $credentials['emailPag'] . '&token=' . $credentials['tokenPag'];

    // Build JSON with request data from AJAX.
    $json = $this->buildPayment();

    // Call the createPlanOrPayment method from the trait.
    $codePlan = $this->createPlanOrPayment($url, $json);

    $_SESSION['tipo_pagamento'] = 'recorrente';

    if (!isset($codePlan) && $codePlan == '') {
      if ($json_return['errors']['11014'] !== '') {
        return $this->newResponse(json_encode('erroPhone'));
      }
      return $this->newResponse(json_encode('erro'));
    }

    // Atualizando Status da Assinatura após o Pagamento.
    $node_assinatura = (isset($_SESSION['node_assinatura'])) ? $_SESSION['node_assinatura'] : '';

    $load_node = Node::load($node_assinatura);

    $load_node->set('field_assinatura_pagseguro', $codePlan);
    $load_node->set('field_ass_situacao', 'ativo');
    $load_node->save();

    $_SESSION['node_assinatura'] = '';

    $user = User::load(\Drupal::currentUser()->id());
    $assinatura = \Drupal::request()->request->get('assinatura');
    $user->set('field_assinatura_recorrente_plan', $codePlan);
    $user->set('field_tipo_assinatura', $assinatura);
    $user->set('field_cpf_cobranca', $cpf);
    $user->save();

    return $this->newResponse($codePlan);

  }

  protected function buildPayment() {
  
    $ip = '';

    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) :
      $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
      $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    endif;

    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP)) {
      $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
      $ip = $forward;
    }
    else {
      $ip = $remote;
    }
    $user = User::load(\Drupal::currentUser()->id());
    $mail = $user->get('mail')->value; // E-mail conta Pagseguro
    $rua = $user->get('field_member_address')->value;
    $cep = $user->get('field_postal_code')->value;
    $cep = str_replace('.', '', str_replace('-', '', $cep));
    $numero = $user->get('field_numero_cobranca')->value;
    $bairro = $user->get('field_bairro_cobranca')->value;
    $cidade = $user->get('field_member_city')->value;
    $estado = $user->get('field_member_region')->value;
    $dt_nascimento = $user->get('field_birthday')->value;
    $telefone = $user->get('field_phone')->value;
    $telefone = str_replace('(', '', str_replace(')', '', $telefone));
    $telefone = str_replace('-', '', $telefone);
    $dadosFone = explode(' ', $telefone);

    /**
     * Ajustando campo DISTRICT.
     * Campo não pode ser null ao ser enviado para API do Pagseguro.
     */
    if ($bairro == null) :
      $bairro = 'DISTRICT';
    endif;

    // Dados da Compra
    $valor = \Drupal::request()->request->get('valor');
    $valor = number_format($valor, 2, ".", "");
    $plano = \Drupal::request()->request->get('plano');

    // Dados do Usuário Comprador
    $cpf = \Drupal::request()->request->get('cpf');
    $cpf = str_replace('.', '', str_replace('-', '', $cpf));
    $nome = \Drupal::request()->request->get('nome');

    // Dados do Cartão
    $cardNumber = \Drupal::request()->request->get('cardNumber');
    $cardNumber = str_replace(' ', '', str_replace('-', '', $cardNumber));
    $cvv = \Drupal::request()->request->get('cvv');
    $expirationMonth = \Drupal::request()->request->get('expirationMonth');
    $expirationYear = \Drupal::request()->request->get('expirationYear');

    // Dados para Finalizar a Compra Recorrente.
    // This plan it was create to pagseguro.
    $plan = \Drupal::request()->request->get('plan');

    $hash = \Drupal::request()->request->get('hash');
    $cardToken = \Drupal::request()->request->get('cardToken');

    return [
      'plan' => $plan,
      'reference' => $plano . '-' . $cpf,
      'sender' => [
        'name' => $nome,
        'email' => $mail,
        'ip' => $ip,
        'hash' => $hash,
        'phone' => [
          'areaCode' => $dadosFone[0],
          'number' => $dadosFone[1],
        ],
        'documents' => [
          [
            'type' => 'CPF',
            'value' => $cpf
          ]
        ],
        'address' => [
          'street' => $rua,
          'number' => $numero,
          'complement' => '0',
          'district' => $bairro,
          'city' => $cidade,
          'state' => $estado,
          'country' => 'BRA',
          'postalCode' => $cep,
        ]
      ],
      'paymentMethod' => [
        'type' => 'CREDITCARD',
        'creditCard' => [
          'token' => $cardToken,
          'holder' => [
            'name' => $nome,
            'birthDate' => $dt_nascimento,
            'billingAddress' => [
              'street' => $rua,
              'number' => $numero,
              'complement' => '0',
              'district' => $bairro,
              'city' => $cidade,
              'state' => $estado,
              'country' => 'BRA',
              'postalCode' => $cep,
            ],
            'documents' => [
              [
                'type' => 'CPF',
                'value' => $cpf
              ]
            ],
            'phone' => [
              'areaCode' => $dadosFone[0],
              'number' => $dadosFone[1],
            ]
          ]
        ]
      ]
    ];
  
  }

  protected function newResponse($result) {
    $response = new Response();
    $response->setContent(json_encode($result));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  protected function getCrendentialsToPagSeguro($isDevOnIt = FALSE) {
    // Buscando Credenciais do PagSeguro Salvas no Banco de Dados
    // $query = db_select('pagseguro_credenciais', 'pag');
    // $query->addField('pag', 'email');
    // $query->addField('pag', 'token');
    // $query->addField('pag', 'link');
    // $query->range(0,1);

    // $pagseguroCredenciais = $query->execute()->fetchAll();

    // Set the PagSeguro if the sandbox is FALSE.
    if ($isDevOnIt) {
      //API Sandbox
      //$linkPag = 'https://ws.sandbox.pagseguro.uol.com.br';
      $linkPag = 'https://ws.pagseguro.uol.com.br';
      $email = 'pabloedurigueto@outlook.com';
      //$token = '50A402506BD14FC39C424C203A68E7B4';
      $token = "40ca886d-5757-4730-9e77-a968bb3bfb0707a9e4b64e19915a03944348f96be2485826-e7a9-49d3-803c-4144cb48f886";
    }

    //$linkPag = $pagseguroCredenciais[0]->link;

    // Set the PagSeguro if the sandbox is FALSE.
    if (!$isDevOnIt) {
      $email = $pagseguroCredenciais[0]->email;
      $token = $pagseguroCredenciais[0]->token;
    }

    return [
      'linkPag' => $linkPag,
      'emailPag' => $email,
      'tokenPag' => $token,
    ];

  }

}
