<?php

namespace Drupal\concerto_assinatura\Controller;

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
use Drupal\concerto_cadastro\CurlRequestTrait;

class AssinaturaController extends ControllerBase
{
    /**
     * Trait to avoid a lot of repeated code.
     * To'do.
     */
    use CurlRequestTrait;

    function assinePage(Request $request)
    {
        $destination = '/assinatura';
        return new RedirectResponse($destination);
    }

    /**
     * Lista Opções de Assinaturas Cadastradas Pelo Painel
     *
     * @var Request $request
     */
    function listOptions(Request $request)
    {
        $_SESSION['node_assinatura'] = '';

        $query = \Drupal::entityQuery('node')->condition('type', 'produto');
        $ids = $query->execute();
        $nodes = Node::loadMultiple($ids);

        $data = array();

        foreach ($nodes as $node) :

            if (
                $node->get('field_exibir_este_plano')->getValue()[0]['value'] == '1'
                &&
                $node->get('field_plano_esta_ativo')->getValue()[0]['value'] == '1'
                &&
                $node->get('field_produto_de_renovacao')->getValue()[0]['value'] != '1'
            ) :

                $resumo_imagem = null;
                if ($node->get('field_resumo_imagem')->getValue()[0]['value'] != null) :
                    $resumo_imagem = '/sites/default/files' . $node->get('field_resumo_imagem')->getValue()[0]['value'];
                endif;

                $content_image = null;
                if ($node->get('field_plano_imagem')->getValue()[0]['value'] != null) :
                    $content_image = '/sites/default/files' . $node->get('field_plano_imagem')->getValue()[0]['value'];
                endif;

                $item = [
                    'id'
                        => $node->id(),
                    'codigo'
                        => $node->get('field_codigo_do_plano')->getValue()[0]['value']
                    ,'title'
                        => $node->get('title')->getValue()[0]['value']
                    ,'exibicao'
                        => $node->get('field_nome_exibicao_site')->getValue()[0]['value']
                    ,'resumo'
                        => $node->get('field_descricao_do_plano_resumo')->getValue()[0]['value']
                    ,'resumo_image'
                        => $resumo_imagem
                    ,'content'
                        => $node->get('field_descricao_do_plano_saiba')->getValue()[0]['value']
                    ,'content_image'
                        => $content_image
                    ,'edicao_avulsa'
                        => $node->get('field_edicao_avulsa_habilitar')->getValue()[0]['value']
                    ,'edicao_avulsa_texto'
                        => $node->get('field_edicao_avulsa_texto')->getValue()[0]['value']
                    ,'edicao_avulsa_valor'
                        => number_format($node->get('field_edicao_avulsa_valor')->getValue()[0]['value'], 2, ",", ".")
                    ,'avulsa_destaque'
                        => $node->get('field_edicao_avulsa_destaque')->getValue()[0]['value']
                    ,'avulsa_destaque_texto'
                        => $node->get('field_avulso_destaque_texto')->getValue()[0]['value']

                    ,'valor_mensal'
                        => $node->get('field_valor_mensal_habilitar')->getValue()[0]['value']
                    ,'valor_mensal_texto'
                        => $node->get('field_valor_mensal_texto')->getValue()[0]['value']
                    ,'valor_mensal_valor'
                        => number_format($node->get('field_valor_mensal_valor')->getValue()[0]['value'], 2, ",", ".")
                    ,'mensal_destaque'
                        => $node->get('field_edicao_mensal_destaque')->getValue()[0]['value']
                    ,'mensal_destaque_texto'
                        => $node->get('field_mensal_destaque_texto')->getValue()[0]['value']

                    ,'valor_anual'
                        => $node->get('field_valor_anual_habilitar')->getValue()[0]['value']
                    ,'valor_anual_texto'
                        => $node->get('field_valor_anual_texto')->getValue()[0]['value']
                    ,'valor_anual_valor'
                        => number_format($node->get('field_valor_anual_valor')->getValue()[0]['value'], 2, ",", ".")
                    ,'anual_destaque'
                        => $node->get('field_edicao_anual_destaque')->getValue()[0]['value']
                    ,'anual_destaque_texto'
                        => $node->get('field_anual_destaque_texto')->getValue()[0]['value']
                ];

                $data['planos'][] = $item;
                $data['count'] = count($data['planos']);

            endif;

        endforeach;

        return [
            '#theme' => 'opcoes_assinatura',
            '#data' => $data,
            '#cache' => [
                'contexts' => [
                    'url.path',
                ],
            ]
        ];
    }

    function listOptionsRenovacao(Request $request)
    {
        $_SESSION['node_assinatura'] = '';

        $query = \Drupal::entityQuery('node')->condition('type', 'produto');
        $ids = $query->execute();
        $nodes = Node::loadMultiple($ids);

        $data = array();

        foreach ($nodes as $node) :
            if ($node->get('field_produto_de_renovacao')->getValue()[0]['value'] == '1') :

                $resumo_imagem = null;
                if ($node->get('field_resumo_imagem')->getValue()[0]['value'] != null) :
                    $resumo_imagem = '/sites/default/files' . $node->get('field_resumo_imagem')->getValue()[0]['value'];
                endif;

                $content_image = null;
                if ($node->get('field_plano_imagem')->getValue()[0]['value'] != null) :
                    $content_image = '/sites/default/files' . $node->get('field_plano_imagem')->getValue()[0]['value'];
                endif;

                $item = [
                    'id'
                    => $node->id(),
                    'codigo'
                    => $node->get('field_codigo_do_plano')->getValue()[0]['value']
                    ,'title'
                    => $node->get('title')->getValue()[0]['value']
                    ,'exibicao'
                    => $node->get('field_nome_exibicao_site')->getValue()[0]['value']
                    ,'resumo'
                    => $node->get('field_descricao_do_plano_resumo')->getValue()[0]['value']
                    ,'resumo_image'
                    => $resumo_imagem
                    ,'content'
                    => $node->get('field_descricao_do_plano_saiba')->getValue()[0]['value']
                    ,'content_image'
                    => $content_image
                    ,'edicao_avulsa'
                    => $node->get('field_edicao_avulsa_habilitar')->getValue()[0]['value']
                    ,'edicao_avulsa_texto'
                    => $node->get('field_edicao_avulsa_texto')->getValue()[0]['value']
                    ,'edicao_avulsa_valor'
                    => number_format($node->get('field_edicao_avulsa_valor')->getValue()[0]['value'], 2, ",", ".")
                    ,'avulsa_destaque'
                    => $node->get('field_edicao_avulsa_destaque')->getValue()[0]['value']
                    ,'avulsa_destaque_texto'
                    => $node->get('field_avulso_destaque_texto')->getValue()[0]['value']

                    ,'valor_mensal'
                    => $node->get('field_valor_mensal_habilitar')->getValue()[0]['value']
                    ,'valor_mensal_texto'
                    => $node->get('field_valor_mensal_texto')->getValue()[0]['value']
                    ,'valor_mensal_valor'
                    => number_format($node->get('field_valor_mensal_valor')->getValue()[0]['value'], 2, ",", ".")
                    ,'mensal_destaque'
                    => $node->get('field_edicao_mensal_destaque')->getValue()[0]['value']
                    ,'mensal_destaque_texto'
                    => $node->get('field_mensal_destaque_texto')->getValue()[0]['value']

                    ,'valor_anual'
                    => $node->get('field_valor_anual_habilitar')->getValue()[0]['value']
                    ,'valor_anual_texto'
                    => $node->get('field_valor_anual_texto')->getValue()[0]['value']
                    ,'valor_anual_valor'
                    => number_format($node->get('field_valor_anual_valor')->getValue()[0]['value'], 2, ",", ".")
                    ,'anual_destaque'
                    => $node->get('field_edicao_anual_destaque')->getValue()[0]['value']
                    ,'anual_destaque_texto'
                    => $node->get('field_anual_destaque_texto')->getValue()[0]['value']
                ];

                $data['planos'][] = $item;
                $data['count'] = count($data['planos']);
            endif;
        endforeach;

        return [
            '#theme' => 'renova',
            '#data' => $data,
            '#cache' => [
                'contexts' => [
                    'url.path',
                ],
            ]
        ];
    }

    /**
     * Tela Inicial de Assinatura de Planos (Resumo do Plano Selecionado)
     */
    function assinatura()
    {
        $plano_parameter = \Drupal::routeMatch()->getParameter('plano');
        $assinatura_parameter = \Drupal::routeMatch()->getParameter('assinatura');

        if ($plano_parameter) :
            $_SESSION['plano'] = $plano_parameter;
        endif;

        if ($assinatura_parameter) :
            $_SESSION['assinatura'] = $assinatura_parameter;
        endif;

        $plano = (isset($_SESSION['plano'])) ? $_SESSION['plano'] : '';
        $assinatura = (isset($_SESSION['assinatura'])) ? $_SESSION['assinatura'] : '';

        $data['plano'] = $plano;
        $data['assinatura'] = $assinatura;

        $query = \Drupal::entityQuery('node')
            ->condition('type', 'produto')
            ->condition('field_codigo_do_plano', $plano)
            ->range(0,1);

        $id = $query->execute();
        $node = Node::loadMultiple($id);

        foreach ($node as $n) :
            if ($assinatura == 'avulso') :
                $valor = number_format($n->get('field_edicao_avulsa_valor')->getValue()[0]['value'], 2, ",", ".");
                $tempo = 'avulso';
            endif;

            if ($assinatura == 'mensal') :
                $valor = number_format($n->get('field_valor_mensal_valor')->getValue()[0]['value'], 2, ",", ".");
                $tempo = 'mês';
            endif;

            if ($assinatura == 'anual') :
                $valor = number_format($n->get('field_valor_anual_valor')->getValue()[0]['value'], 2, ",", ".");
                $tempo = 'ano';
            endif;

            $resumo = $n->get('field_descricao_do_plano_resumo')->getValue()[0]['value'];
            $resumo = preg_replace('/<img[^>]+\>/', '', $resumo);

            $qtde = 0;

            if ($n->get('field_edicao_avulsa_habilitar')->getValue()[0]['value'] == '1') :
                $qtde++;
            endif;

            if ($n->get('field_valor_mensal_habilitar')->getValue()[0]['value'] == '1') :
                $qtde++;
            endif;

            if ($n->get('field_valor_anual_habilitar')->getValue()[0]['value'] == '1') :
                $qtde++;
            endif;

            $content_image = null;
            if ($n->get('field_plano_imagem')->getValue()[0]['value'] != null) :
                $content_image = '/sites/default/files' . $n->get('field_plano_imagem')->getValue()[0]['value'];
            endif;

            $item = [
                'id' => $n->id()
                ,'qtde' => $qtde
                ,'codigo' => $n->get('field_codigo_do_plano')->getValue()[0]['value']
                ,'title' => $n->get('title')->getValue()[0]['value']
                ,'resumo' => $resumo
                ,'valor' => $valor
                ,'tempo' => $tempo
                ,'anual' => number_format($n->get('field_valor_anual_valor')->getValue()[0]['value'], 2, ",", ".")
                ,'desconto' => number_format($n->get('field_valor_anual_valor')->getValue()[0]['value'], 2, ",", ".")
                ,'content' => $n->get('field_descricao_do_plano_saiba')->getValue()[0]['value']
                ,'content_image' => $content_image
                ,'edicao_avulsa' => $n->get('field_edicao_avulsa_habilitar')->getValue()[0]['value']
                ,'edicao_avulsa_texto'  => $n->get('field_edicao_avulsa_texto')->getValue()[0]['value']
                ,'edicao_avulsa_valor' => number_format($n->get('field_edicao_avulsa_valor')->getValue()[0]['value'], 2, ",", ".")
                ,'informacao_avulsa' => $n->get('field_avulso_informacao')->getValue()[0]['value']
                ,'avulsa_destaque' => $n->get('field_edicao_avulsa_destaque')->getValue()[0]['value']
                ,'avulsa_destaque_texto' => $n->get('field_avulso_destaque_texto')->getValue()[0]['value']
                ,'valor_mensal' => $n->get('field_valor_mensal_habilitar')->getValue()[0]['value']
                ,'valor_mensal_texto' => $n->get('field_valor_mensal_texto')->getValue()[0]['value']
                ,'valor_mensal_valor' => number_format($n->get('field_valor_mensal_valor')->getValue()[0]['value'], 2, ",", ".")
                ,'mensal_destaque' => $n->get('field_edicao_mensal_destaque')->getValue()[0]['value']
                ,'mensal_destaque_texto' => $n->get('field_mensal_destaque_texto')->getValue()[0]['value']
                ,'informacao_mensal' => $n->get('field_mensal_informacao')->getValue()[0]['value']
                ,'valor_anual' => $n->get('field_valor_anual_habilitar')->getValue()[0]['value']
                ,'valor_anual_texto' => $n->get('field_valor_anual_texto')->getValue()[0]['value']
                ,'valor_anual_valor' => number_format($n->get('field_valor_anual_valor')->getValue()[0]['value'], 2, ",", ".")
                ,'anual_destaque' => $n->get('field_edicao_anual_destaque')->getValue()[0]['value']
                ,'anual_destaque_texto' => $n->get('field_anual_destaque_texto')->getValue()[0]['value']
                ,'informacao_anual' => $n->get('field_anual_informacao')->getValue()[0]['value']
            ];

            $data['info'] = $item;
        endforeach;

        return [
            '#theme' => 'assinar',
            '#data' => $data,
            '#cache' => [
                'contexts' => [
                    'url.path',
                ],
            ]
        ];
    }

    /**
     * Tela de Login
     */
    function loginCliente()
    {
        return [
            '#theme' => 'login_cliente',
            '#data' => '',
            '#cache' => [
                'contexts' => [
                    'url.path',
                ],
            ]
        ];
    }

    /**
     * Tela de Confirmação
     */
    function confirmacao()
    {
        $user = User::load(\Drupal::currentUser()->id());
        $link_pagamento = $user->get('field_link_pagamento_debito')->value;

        $plano = (isset($_SESSION['plano'])) ? $_SESSION['plano'] : '';
        $assinatura = (isset($_SESSION['assinatura'])) ? $_SESSION['assinatura'] : '';
        $tipo_pagamento = (isset($_SESSION['tipo_pagamento'])) ? $_SESSION['tipo_pagamento'] : '';

        // Limpando Sessão
        $_SESSION['plano'] = '';
        $_SESSION['assinatura'] = '';
        $_SESSION['link_pagamento'] = '';
        $_SESSION['tipo_pagamento'] = '';

        $query = \Drupal::entityQuery('node')
            ->condition('type', 'produto')
            ->condition('field_codigo_do_plano', $plano)
            ->range(0,1);

        $id = $query->execute();
        $node = Node::loadMultiple($id);

        foreach ($node as $n) :
            if ($assinatura == 'avulso') :
                $valor = number_format($n->get('field_edicao_avulsa_valor')->getValue()[0]['value'], 2, ",", ".");
                $tempo = 'avulso';
            endif;

            if ($assinatura == 'mensal') :
                $valor = number_format($n->get('field_valor_mensal_valor')->getValue()[0]['value'], 2, ",", ".");
                $tempo = 'mês';
            endif;

            if ($assinatura == 'anual') :
                $valor = number_format($n->get('field_valor_anual_valor')->getValue()[0]['value'], 2, ",", ".");
                $tempo = 'ano';
            endif;

            $item = [
                'codigo' => $n->get('field_codigo_do_plano')->getValue()[0]['value']
                ,'title' => $n->get('title')->getValue()[0]['value']
                ,'assinatura' => $assinatura
                ,'plano' => $plano
                ,'valor' => $valor
                ,'tempo' => $tempo
                ,'data_compra' => date('d/m/Y')
                ,'dia_renovacao' => date('d')
                ,'link_pagamento' => $link_pagamento
                ,'tipo_pagamento' => $tipo_pagamento
            ];

            $data['info'] = $item;
        endforeach;

        return [
            '#theme' => 'confirmacao',
            '#data' => $data,
            '#cache' => [
                'contexts' => [
                    'url.path',
                ],
            ]
        ];
    }

    /**
     * Função para Tratativas de Pagamento Recorrente com a API do PagSeguro
     */
    function ajaxPagamentoRecorrente()
    {
        // Dados da Compra
        $valor = \Drupal::request()->request->get('valor');
        $valor = number_format($valor, 2, ".", "");
        $plano = \Drupal::request()->request->get('plano');
        $assinatura = \Drupal::request()->request->get('assinatura');

        // Buscando Informações do Usuário Logado
        $user = User::load(\Drupal::currentUser()->id());

        $mail = $user->get('mail')->value;
        $rua = $user->get('field_member_address')->value;
        $cep = $user->get('field_postal_code')->value;
        $numero = $user->get('field_numero_cobranca')->value;
        $bairro = $user->get('field_bairro_cobranca')->value;
        $cidade = $user->get('field_member_city')->value;
        $estado = $user->get('field_member_region')->value;

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
        $period = \Drupal::request()->request->get('period');

        // Buscando Credenciais do PagSeguro Salvas no Banco de Dados
        $query = db_select('pagseguro_credenciais', 'pag');
        $query->addField('pag', 'email');
        $query->addField('pag', 'token');
        $query->addField('pag', 'link');
        $query->range(0,1);

        $pagseguroCredenciais = $query->execute()->fetchAll();

        $linkPag = $pagseguroCredenciais[0]->link;
        $email = $pagseguroCredenciais[0]->email;
        $token = $pagseguroCredenciais[0]->token;

        // Criando Plano de Adesão
        $url = $linkPag . '/pre-approvals/request?email=' . $email . '&token=' . $token;

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

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, Array('Accept: application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1', 'Content-type: application/json;charset=ISO-8859-1'));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($json));

        $json = curl_exec($curl);
        curl_close($curl);

        $json_return = json_decode($json, TRUE);
        $plan = $json_return['code'];

        if (isset($plan) && $plan != '') :
            // Salva PLANO do Usuário
            $user->set('field_plano_pagseguro', $plan);
            $user->save();

            // Gera SESSION ID do PagSeguro
            $urlSession = $linkPag . '/v2/sessions';
            $dadosSession = array('email' => $email, 'token' => $token);
            $dadosSession = http_build_query($dadosSession);

            $chSession = curl_init($urlSession);
            curl_setopt($chSession, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($chSession, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chSession, CURLOPT_POST, true);
            curl_setopt($chSession, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($chSession, CURLOPT_POSTFIELDS, $dadosSession);

            $result = curl_exec($chSession);
            curl_close($chSession);

            $xml_retorno_session = simplexml_load_string($result);
            $xml_session = json_encode($xml_retorno_session);
            $session = json_decode($xml_session, TRUE);
            $sessionID = $session['id'];

            $data = array('plan' => $plan, 'sessionID' => $sessionID);

            $response = new Response();
            $response->setContent(json_encode($data));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        else :
            $response = new Response();
            $response->setContent(json_encode('erro'));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        endif;
    }

    /**
     * Função para Finalizar Pagamento Recorrente com a API do PagSeguro
     */
    function ajaxPagamentoAssinatura()
    {
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
        $assinatura = \Drupal::request()->request->get('assinatura');

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
        } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        // Buscando Credenciais do PagSeguro Salvas no Banco de Dados
        $query = db_select('pagseguro_credenciais', 'pag');
        $query->addField('pag', 'email');
        $query->addField('pag', 'token');
        $query->addField('pag', 'link');
        $query->range(0,1);

        $pagseguroCredenciais = $query->execute()->fetchAll();

        $linkPag = $pagseguroCredenciais[0]->link;
        $email = $pagseguroCredenciais[0]->email;
        $token = $pagseguroCredenciais[0]->token;

        // Criando Plano de Assinatura
        $url = $linkPag . '/pre-approvals?email=' . $email . '&token=' . $token;

        $json = [
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

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, Array('Accept: application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1', 'Content-type: application/json;charset=ISO-8859-1'));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($json));

        $json = curl_exec($curl);
        curl_close($curl);

        $json_return = json_decode($json, TRUE);

        $codigo = $json_return['code'];
        $_SESSION['tipo_pagamento'] = 'recorrente';

        if (isset($codigo) && $codigo != '') :
            // Atualizando Status da Assinatura após o Pagamento.
            $node_assinatura = (isset($_SESSION['node_assinatura'])) ? $_SESSION['node_assinatura'] : '';

            $load_node = Node::load($node_assinatura);
            $load_node->set('field_assinatura_pagseguro', $codigo);
            $load_node->set('field_ass_situacao', 'ativo');
            $load_node->save();

            $_SESSION['node_assinatura'] = '';

            $user->set('field_assinatura_recorrente_plan', $codigo);
            $user->set('field_tipo_assinatura', $assinatura);
            // $user->set('field_nome_cobranca', $nome);
            $user->set('field_cpf_cobranca', $cpf);
            // $user->set('field_cartao_cobranca', str_pad(substr($cardNumber, -4), strlen($cardNumber), '*', STR_PAD_LEFT));
            $user->save();

            $response = new Response();
            $response->setContent(json_encode($codigo));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        else :
            if ($json_return['errors']['11014'] !== '') :
                $response = new Response();
                $response->setContent(json_encode('erroPhone'));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            else :
                $response = new Response();
                $response->setContent(json_encode('erro'));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            endif;
        endif;
    }

    /**
     * Inicia Sessão de Pagamento do PagSeguro
     */
    function ajaxPagamentoSessao()
    {
        $query = db_select('pagseguro_credenciais', 'pag');
        $query->addField('pag', 'email');
        $query->addField('pag', 'token');
        $query->addField('pag', 'link');
        $query->range(0,1);

        $pagseguroCredenciais = $query->execute()->fetchAll();

        $linkPag = $pagseguroCredenciais[0]->link;
        $emailPag = $pagseguroCredenciais[0]->email;
        $tokenPag = $pagseguroCredenciais[0]->token;

        // Gera SESSION ID do PagSeguro
        $urlSession = $linkPag . '/v2/sessions';
        $dadosSession = array('email' => $emailPag, 'token' => $tokenPag);
        $dadosSession = http_build_query($dadosSession);

        $chSession = curl_init($urlSession);
        curl_setopt($chSession, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chSession, CURLOPT_POST, true);
        curl_setopt($chSession, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($chSession, CURLOPT_POSTFIELDS, $dadosSession);

        $result = curl_exec($chSession);
        curl_close($chSession);

        $xml_retorno_session = simplexml_load_string($result);
        $xml_session = json_encode($xml_retorno_session);
        $session = json_decode($xml_session, TRUE);
        $sessionID = $session['id'];

        $data = array('sessionID' => $sessionID);

        $response = new Response();
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Tratando Pagamento Avulso com Cartão de Crédito
     */
    function ajaxPagamentoAvulso()
    {
        $user = User::load(\Drupal::currentUser()->id());
        $mail = $user->get('mail')->value; // E-mail conta Pagseguro

        $rua = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $user->get('field_member_address')->value);

        $cep = $user->get('field_postal_code')->value;
        $cep = str_replace('.', '', str_replace('-', '', $cep));

        $numero = $user->get('field_numero_cobranca')->value;
        $bairro = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $user->get('field_bairro_cobranca')->value);
        $cidade = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $user->get('field_member_city')->value);
        $estado = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $user->get('field_member_region')->value);

        $dt_nascimento = $user->get('field_birthday')->value;
        $telefone = $user->get('field_phone')->value;
        $telefone = str_replace('(', '', str_replace(')', '', $telefone));
        $telefone = str_replace('-', '', $telefone);
        $dadosFone = explode(' ', $telefone);

        // Dados da Compra
        $valor = \Drupal::request()->request->get('valor');
        $valor = number_format($valor, 2, ".", "");
        $plano = \Drupal::request()->request->get('plano');
        $assinatura = \Drupal::request()->request->get('assinatura');

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

        // Dados para Finalizar a Compra Recorrente
        $hash = \Drupal::request()->request->get('hash');
        $cardToken = \Drupal::request()->request->get('cardToken');

        // Buscando Credenciais do PagSeguro Salvas no Banco de Dados
        $query = db_select('pagseguro_credenciais', 'pag');
        $query->addField('pag', 'email');
        $query->addField('pag', 'token');
        $query->addField('pag', 'link');
        $query->range(0,1);

        $pagseguroCredenciais = $query->execute()->fetchAll();

        $linkPag = $pagseguroCredenciais[0]->link;
        $email = $pagseguroCredenciais[0]->email;
        $token = $pagseguroCredenciais[0]->token;

        $url = $linkPag . '/v2/transactions/?email=' . $email . '&token=' . $token;

        $dadosT = array(
            'paymentMode' => 'default'
            ,'paymentMethod' => 'creditCard'
            ,'currency' => 'BRL'
            ,'extraAmount' => '0.00'
            ,'itemId1' => $plano . '-' . $cpf . '-' . rand()
            ,'itemDescription1' => 'Compra de Revista Avulsa - Credito'
            ,'itemAmount1' => $valor
            ,'itemQuantity1' => '1'
            ,'reference' => $plano . '-' . $cpf
            ,'senderName' => $nome
            ,'senderCPF' => $cpf
            ,'senderAreaCode' => $dadosFone[0]
            ,'senderPhone' => $dadosFone[1]
            ,'senderEmail' => $mail
            ,'senderHash' => $hash
            ,'shippingAddressStreet' => $rua
            ,'shippingAddressNumber' => $numero
            ,'shippingAddressComplement' => '0'
            ,'shippingAddressDistrict' => $bairro
            ,'shippingAddressPostalCode' => $cep
            ,'shippingAddressCity' => $cidade
            ,'shippingAddressState' => $estado
            ,'shippingAddressCountry' => 'BRA'
            ,'shippingType' => '3'
            ,'shippingCost' => '00.00'
            ,'creditCardToken' => $cardToken
            ,'installmentQuantity' => '1'
            ,'installmentValue' => $valor
            ,'creditCardHolderName' => $nome
            ,'creditCardHolderCPF' => $cpf
            ,'creditCardHolderBirthDate' => $dt_nascimento
            ,'creditCardHolderAreaCode' => $dadosFone[0]
            ,'creditCardHolderPhone' => $dadosFone[1]
            ,'billingAddressStreet' => $rua
            ,'billingAddressNumber' => $numero
            ,'billingAddressComplement' => '0'
            ,'billingAddressDistrict' => $bairro
            ,'billingAddressPostalCode' => $cep
            ,'billingAddressCity' => $cidade
            ,'billingAddressState' => $estado
            ,'billingAddressCountry' => 'BRA'
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dadosT));

        $result = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($result);
        $codigo = $xml->code[0];

        $_SESSION['tipo_pagamento'] = 'credito';

        if (isset($codigo) && $codigo != '') :
            // Atualizando Status da Assinatura após o Pagamento
            $node_assinatura = (isset($_SESSION['node_assinatura'])) ? $_SESSION['node_assinatura'] : '';

            $load_node = Node::load($node_assinatura);
            $load_node->set('field_assinatura_pagseguro', $codigo);
            $load_node->set('field_ass_situacao', 'ativo');
            $load_node->save();

            $_SESSION['node_assinatura'] = '';

            $user->set('field_assinatura_avulsa_cred', $codigo);
            $user->set('field_tipo_assinatura', $assinatura);
            // $user->set('field_nome_cobranca', $nome);
            $user->set('field_cpf_cobranca', $cpf);
            // $user->set('field_cartao_cobranca', str_pad(substr($cardNumber, -4), strlen($cardNumber), '*', STR_PAD_LEFT));
            $user->save();

            $response = new Response();
            $response->setContent(json_encode($codigo));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        else :
            if (isset($xml->error->code[0]) && $xml->error->code[0] == '53141') :
                $response = new Response();
                $response->setContent(json_encode('erroBlock'));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            else :
                $response = new Response();
                $response->setContent(json_encode('erro'));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            endif;
        endif;
    }

    /**
     * Tratando Pagamento Avulso via Débito Online
     */
    function ajaxPagamentoAvulsoVista()
    {
        $user = User::load(\Drupal::currentUser()->id());
        $mail = $user->get('mail')->value; // VALOR PADRÃO

        $rua = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $user->get('field_member_address')->value);

        $cep = $user->get('field_postal_code')->value;
        $cep = str_replace('.', '', str_replace('-', '', $cep));

        $numero = $user->get('field_numero_cobranca')->value;
        $bairro = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $user->get('field_bairro_cobranca')->value);
        $cidade = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $user->get('field_member_city')->value);
        $estado = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $user->get('field_member_region')->value);

        $telefoneOriginal = \Drupal::request()->request->get('fone');
        $telefone = str_replace('(', '', str_replace(')', '', $telefoneOriginal));
        $telefone = str_replace('-', '', $telefone);
        $dadosFone = explode(' ', $telefone);

        // Dados da Compra
        $valor = \Drupal::request()->request->get('valor');
        $valor = number_format($valor, 2, ".", "");
        $plano = \Drupal::request()->request->get('plano');
        $assinatura = \Drupal::request()->request->get('assinatura');

        // Dados do Usuário Comprador
        $cpf = \Drupal::request()->request->get('cpf');
        $cpf = str_replace('.', '', str_replace('-', '', $cpf));
        $banco = \Drupal::request()->request->get('banco');
        $nome = \Drupal::request()->request->get('nome');
        $hash = \Drupal::request()->request->get('hash');

        // Buscando Credenciais do PagSeguro Salvas no Banco de Dados
        $query = db_select('pagseguro_credenciais', 'pag');
        $query->addField('pag', 'email');
        $query->addField('pag', 'token');
        $query->addField('pag', 'link');
        $query->range(0,1);

        $pagseguroCredenciais = $query->execute()->fetchAll();

        $linkPag = $pagseguroCredenciais[0]->link;
        $emailPag = $pagseguroCredenciais[0]->email;
        $tokenPag = $pagseguroCredenciais[0]->token;

        $url = $linkPag . '/v2/transactions/?email=' . $emailPag . '&token=' . $tokenPag;

        $dadosT = array(
            'paymentMode' => 'default'
            ,'paymentMethod' => 'eft'
            ,'bankName' => $banco
            ,'currency' => 'BRL'
            ,'extraAmount' => '0.00'
            ,'itemId1' => $plano . '-' . $cpf . '-' . rand()
            ,'itemDescription1' => 'Compra de Revista Avulsa - Debito Online'
            ,'itemAmount1' => $valor
            ,'itemQuantity1' => '1'
            ,'reference' => $plano . '-' . $cpf
            ,'senderName' => $nome
            ,'senderCPF' => $cpf
            ,'senderAreaCode' => $dadosFone[0]
            ,'senderPhone' => $dadosFone[1]
            ,'senderEmail' => $mail
            ,'senderHash' => $hash
            ,'shippingAddressStreet' => $rua
            ,'shippingAddressNumber' => $numero
            ,'shippingAddressComplement' => '0'
            ,'shippingAddressDistrict' => $bairro
            ,'shippingAddressPostalCode' => $cep
            ,'shippingAddressCity' => $cidade
            ,'shippingAddressState' => $estado
            ,'shippingAddressCountry' => 'BRA'
            ,'shippingType' => '3'
            ,'shippingCost' => '00.00'
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, Array('Content-Type: application/x-www-form-urlencoded; charset=ISO-8859-1'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dadosT));

        $result = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($result);

        $codigo = $xml->code[0];
        $link_pagamento = $xml->paymentLink[0];
        $_SESSION['tipo_pagamento'] = 'debito';

        if (isset($codigo[0]) && $codigo[0] != '') :
            // Atualizando Status da Assinatura após o Pagamento
            $node_assinatura = (isset($_SESSION['node_assinatura'])) ? $_SESSION['node_assinatura'] : '';

            $load_node = Node::load($node_assinatura);
            $load_node->set('field_assinatura_pagseguro', $codigo[0]);
            $load_node->set('field_ass_situacao', 'ativo');
            $load_node->save();

            $user->set('field_assinatura_avulsa_deb', $codigo[0]);
            $user->set('field_link_pagamento_debito', $link_pagamento[0]);
            $user->set('field_tipo_assinatura', $assinatura);
            $user->set('field_nome_cobranca', $nome);
            $user->set('field_cpf_cobranca', $cpf);
            $user->set('field_phone', $telefoneOriginal);
            $user->save();

            $_SESSION['node_assinatura'] = '';

            $response = new Response();
            $response->setContent(json_encode($codigo));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        else :
            if (isset($xml->error->code[0]) && $xml->error->code[0] == '53141') :
                $response = new Response();
                $response->setContent(json_encode('erroBlock'));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            else :
                $response = new Response();
                $response->setContent(json_encode('erro'));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            endif;
        endif;
    }

    function notificacaoRecorrencia()
    {
        $queryU = \Drupal::entityQuery('user');

        $idsU = $queryU
            ->condition('field_cadastro_via', 'site')
            ->execute();

        $connection = \Drupal::database();

        foreach ($idsU as $uid) :
            $user = user_load($uid);

            $query = $connection->query("
                SELECT
                fd.uid as drupal_id,
                faa.ass_nid as node_id,
                fci.field_concerto_id_value as numero_assinante,
                fn.field_full_name_value as nome_completo,
                fd.mail as email,
                fd.status,
                fp.field_phone_value as telefone,
                fc.field_celular_value as celular,
                fcpf.field_cpf_value as cpf,
                fma.field_member_address_value as enredeco,
                fpc.field_postal_code_value as cep,
                fmc.field_member_city_value as cidade,
                fmr.field_member_region_value as estado,
                fe.field_empresa_value as empresa,
                fap.field_ass_produto_nome_value as assinatura,
                fbairro.field_bairro_entrega_value as bairro_entrega,
                fcep.field_cep_entrega_value as cep_entrega,
                frua.field_rua_entrega_value as rua_entrega,
                fcidade.field_cidade_entrega_value as cidade_entrega,
                festado.field_estado_entrega_value as estado_entrega,
                fnumero.field_numero_entrega_value as numero_entrega,
                fcomplemento.field_complemento_entrega_assina_value as complemento_entrega,
                fas.field_ass_situacao_value as situacao,
                fatp.field_ass_tipo_de_pagamento_value as plano_assinatura,
                pagToken.field_assinatura_pagseguro_value as pagseguroToken,
                DATE_FORMAT(favd.field_ass_validade_value,'%d/%m/%Y') as validade,
                favl.field_ass_valor_value as valor
                FROM users_field_data fd
                JOIN user__roles ur ON fd.uid = ur.entity_id
                LEFT JOIN user__field_phone fp ON fd.uid = fp.entity_id
                LEFT JOIN user__field_full_name fn ON fd.uid = fn.entity_id
                LEFT JOIN user__field_celular fc ON fd.uid = fc.entity_id
                LEFT JOIN user__field_cpf fcpf ON fd.uid = fcpf.entity_id
                LEFT JOIN user__field_empresa fe ON fd.uid = fe.entity_id
                LEFT JOIN user__field_member_address fma ON fd.uid = fma.entity_id
                LEFT JOIN user__field_postal_code fpc ON fd.uid = fpc.entity_id
                LEFT JOIN user__field_member_city fmc ON fd.uid = fmc.entity_id
                LEFT JOIN user__field_member_region fmr ON fd.uid = fmr.entity_id
                LEFT JOIN user__field_concerto_id fci ON fd.uid = fci.entity_id
                LEFT JOIN (SELECT max(nid) as ass_nid, field_ass_assinante_target_id FROM node subn
                JOIN node__field_ass_assinante subfass ON subfass.entity_id = subn.nid
                WHERE subn.type = 'assinaturas'
                GROUP BY field_ass_assinante_target_id) faa ON faa.field_ass_assinante_target_id = fd.uid
                LEFT JOIN node__field_ass_produto_nome fap ON fap.entity_id = faa.ass_nid
                LEFT JOIN node__field_ass_situacao fas ON fas.entity_id = faa.ass_nid
                LEFT JOIN node__field_ass_tipo_de_pagamento fatp ON fatp.entity_id = faa.ass_nid
                LEFT JOIN node__field_ass_validade favd ON favd.entity_id = faa.ass_nid
                LEFT JOIN node__field_ass_valor favl ON favl.entity_id = faa.ass_nid
                LEFT JOIN node__field_ass_produto_id fapi ON fapi.entity_id = faa.ass_nid
                LEFT JOIN node__field_receber_revista frr ON frr.entity_id = fapi.field_ass_produto_id_value
                LEFT JOIN node__field_bairro_entrega fbairro ON fbairro.entity_id = faa.ass_nid
                LEFT JOIN node__field_cep_entrega fcep ON fcep.entity_id = faa.ass_nid
                LEFT JOIN node__field_rua_entrega frua ON frua.entity_id = faa.ass_nid
                LEFT JOIN node__field_cidade_entrega fcidade ON fcidade.entity_id = faa.ass_nid
                LEFT JOIN node__field_estado_entrega festado ON festado.entity_id = faa.ass_nid
                LEFT JOIN node__field_numero_entrega fnumero ON fnumero.entity_id = faa.ass_nid
                LEFT JOIN node__field_complemento_entrega_assina fcomplemento ON fcomplemento.entity_id = faa.ass_nid
                LEFT JOIN node__field_assinatura_pagseguro pagToken ON pagToken.entity_id = faa.ass_nid
                WHERE fd.uid = '" . $uid . "' AND fas.field_ass_situacao_value = 'ativo'
                ",
                [],
                [
                    'target' => 'replica',
                    'fetch' => PDO::FETCH_ASSOC,
                ]
            );

            $result = $query->fetchAssoc();

            if ($result['situacao'] !== 'ativo') {
                continue;
            }

            $plano = $result['plano_assinatura'];
            if (strcasecmp($plano, 'anual') !== 0 && strcasecmp($plano, 'mensal') !== 0) {
                continue;
            }            

            if (!isset($result['validade']) && empty($result['validade'])) {
                continue;
            }

            $nodeDate = date('Y-m-d', strtotime(str_replace('/', '-', $result['validade'])));

            $dateNow = date_create(date('Y-m-d'));
            $validade = date_create($nodeDate);

            $diff = date_diff($dateNow, $validade);

            if ($diff->days !== 15) {
                continue;
            }

            // Enviando e-mail faltando 15 dias para plano anual ou mensal ser renovado.
            $this->sendEmailEndIn15Days($user, $result, $diff->days);

        endforeach;

        // Salva LOG do Crawler
        $crawler = \Drupal::service('concerto.util')->saveCrawler('NotificacaoDaRecorrencia');

        exit;
    }

    public function fromEmail() {
        return 'assinaturas@concerto.com.br';
    }

    // Envia o email para os clientes, faltando 15 dias para o vencimento.
    public function sendEmailEndIn15Days($user, $result, $days) {

        $send_mail = new \Drupal\Core\Mail\Plugin\Mail\PhpMail();
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';

        $from = $this->fromEmail();
        $to = $user->get('mail')->value;

        $message['headers'] = array(
            'content-type' => 'text/html',
            'MIME-Version' => '1.0',
            'reply-to' => $from,
            'from' => 'Revista CONCERTO - Assinaturas <'.$from.'>'
        );

        $message['to'] = $to;
        $message['subject'] = 'Renovação automática de assinatura - Revista CONCERTO';

        $message['body'] = 'Olá ' . $user->get('field_full_name')->value . ', <br><br>

        Este é um lembrete de que sua assinatura ' . $result['assinatura'] . ' (' . $result['plano_assinatura'] . ') da Revista CONCERTO será renovada automaticamente dentro de ' . $days . ' dias. <br>

        <br><br>

        Assim, sua assinatura terá validade por mais um ano.

        <br><br>

        Agradecemos por sua fidelidade.

        <br><br>

        Se precisar de ajuda ou tiver dúvidas, por favor entre em contato com nosso Serviço de Assinaturas.

        <br><br>

        Saudações cordiais, <br>
        Revista CONCERTO <br>
        (11) 3539-0048';

        $send = $send_mail->mail($message);

        if ($send) {
            echo 'Enviado';
            return TRUE;
        }

        echo 'Erro' . $send;
        return FALSE;

    }

    function cancelamentoRecorrencia($token, $nid)
    {
        if ($token != '' && $nid != '') :
            $n = Node::load($nid);
            $validationToken = $n->get('field_assinatura_pagseguro')->getValue()[0]['value'];

            if ($validationToken == $token) :
                $userToken = $token;

                // Buscando Credenciais do PagSeguro Salvas no Banco de Dados
                $queryPag = db_select('pagseguro_credenciais', 'pag');
                $queryPag->addField('pag', 'email');
                $queryPag->addField('pag', 'token');
                $queryPag->addField('pag', 'link');
                $queryPag->range(0,1);

                $pagseguroCredenciais = $queryPag->execute()->fetchAll();

                $linkPag = $pagseguroCredenciais[0]->link;
                $emailPag = $pagseguroCredenciais[0]->email;
                $tokenPag = $pagseguroCredenciais[0]->token;

                $url = $linkPag . '/pre-approvals/' . $userToken . '/status/?email=' . $emailPag . '&token=' . $tokenPag;

                if ($userToken != null) :
                    $data = array('status' => 'CANCELLED_BY_RECEIVER');
                    $data_json = json_encode($data);

                    $headers = array('Content-Type: application/json', 'Accept: application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $data = curl_exec($ch);
                    curl_close($ch);

                    $node = Node::load($nid);
                    $node->set('field_ass_situacao', 'inativo');
                    $node->save();

                    $user = User::load($node->getOwnerId());
                    $user->addRole('cortesia');
                    $user->removeRole('membro');
                    $user->save();

                    return [
                        '#theme' => 'cancelamento_recorrencia',
                        '#data' => $data,
                        '#cache' => [
                            'contexts' => [
                                'url.path',
                            ],
                        ]
                    ];
                else :
                    $data = ['status' => 400, 'message' => 'erro'];

                    return [
                        '#theme' => 'cancelamento_recorrencia',
                        '#data' => $data,
                        '#cache' => [
                            'contexts' => [
                                'url.path',
                            ],
                        ]
                    ];
                endif;
            else :
                $data = ['status' => 400, 'message' => 'erro'];

                return [
                    '#theme' => 'cancelamento_recorrencia',
                    '#data' => $data,
                    '#cache' => [
                        'contexts' => [
                            'url.path',
                        ],
                    ]
                ];
            endif;
        else :
            $data = ['status' => 400, 'message' => 'erro'];

            return [
                '#theme' => 'cancelamento_recorrencia',
                '#data' => $data,
                '#cache' => [
                    'contexts' => [
                        'url.path',
                    ],
                ]
            ];
        endif;
    }

    function validacaoDasAssinaturas()
    {
        $query = db_select('pagseguro_credenciais', 'pag');
        $query->addField('pag', 'email');
        $query->addField('pag', 'token');
        $query->addField('pag', 'link');
        $query->range(0,1);

        $pagseguroCredenciais = $query->execute()->fetchAll();

        $linkPag = $pagseguroCredenciais[0]->link;
        $emailPag = $pagseguroCredenciais[0]->email;
        $tokenPag = $pagseguroCredenciais[0]->token;

        $connection = \Drupal::database();

        $query = $connection->query("
            SELECT
            fd.uid as user_id,
            faa.ass_nid as node_id,
            fci.field_concerto_id_value as concerto_id,
            fn.field_full_name_value as nome_completo,
            fd.mail as email,
            fd.status,
            fp.field_phone_value as telefone,
            fc.field_celular_value as celular,
            fcpf.field_cpf_value as cpf,
            fma.field_member_address_value as enredeco,
            fpc.field_postal_code_value as cep,
            fmc.field_member_city_value as cidade,
            fmr.field_member_region_value as estado,
            fe.field_empresa_value as empresa,
            fap.field_ass_produto_nome_value as assinatura,
            fas.field_ass_situacao_value as situacao,
            pagToken.field_assinatura_pagseguro_value as pagseguroToken,
            DATE_FORMAT(from_unixtime(faa.ass_created), '%d/%m/%Y') as data_da_assinatura,
            fatp.field_ass_tipo_de_pagamento_value as plano,
            favd.field_ass_validade_value as validade,
            favl.field_ass_valor_value as valor,
            DATE_FORMAT(facon.contato_data,'%d/%m/%Y') as entrega_cd
            FROM users_field_data fd
            JOIN user__roles ur ON fd.uid = ur.entity_id
            LEFT JOIN user__field_phone fp ON fd.uid = fp.entity_id
            LEFT JOIN user__field_full_name fn ON fd.uid = fn.entity_id
            LEFT JOIN user__field_celular fc ON fd.uid = fc.entity_id
            LEFT JOIN user__field_cpf fcpf ON fd.uid = fcpf.entity_id
            LEFT JOIN user__field_empresa fe ON fd.uid = fe.entity_id
            LEFT JOIN user__field_member_address fma ON fd.uid = fma.entity_id
            LEFT JOIN user__field_postal_code fpc ON fd.uid = fpc.entity_id
            LEFT JOIN user__field_member_city fmc ON fd.uid = fmc.entity_id
            LEFT JOIN user__field_member_region fmr ON fd.uid = fmr.entity_id
            LEFT JOIN user__field_concerto_id fci ON fd.uid = fci.entity_id
            LEFT JOIN (SELECT max(nid) as ass_nid, field_ass_assinante_target_id, max(created) as ass_created FROM node_field_data subn
            JOIN node__field_ass_assinante subfass ON subfass.entity_id = subn.nid
            WHERE subn.type = 'assinaturas'
            GROUP BY field_ass_assinante_target_id) faa ON faa.field_ass_assinante_target_id = fd.uid
            LEFT JOIN (SELECT max(field_contato_data_value) as contato_data, field_cliente_value FROM node subc
            LEFT JOIN node__field_cliente subccon ON subccon.entity_id = subc.nid
            LEFT JOIN node__field_contato_data subdcon ON subdcon.entity_id = subc.nid
            WHERE subc.type = 'historico_de_contato'
            GROUP BY field_cliente_value) facon ON facon.field_cliente_value = fd.uid
            LEFT JOIN node__field_ass_produto_nome fap ON fap.entity_id = faa.ass_nid
            LEFT JOIN node__field_ass_situacao fas ON fas.entity_id = faa.ass_nid
            LEFT JOIN node__field_ass_tipo_de_pagamento fatp ON fatp.entity_id = faa.ass_nid
            LEFT JOIN node__field_ass_validade favd ON favd.entity_id = faa.ass_nid
            LEFT JOIN node__field_ass_valor favl ON favl.entity_id = faa.ass_nid
            LEFT JOIN node__field_assinatura_pagseguro pagToken ON pagToken.entity_id = faa.ass_nid
            WHERE fas.field_ass_situacao_value = 'inativo' AND CURRENT_DATE() <= favd.field_ass_validade_value
            ",
            [],
            [
                'target' => 'replica',
                'fetch' => PDO::FETCH_ASSOC,
            ]
        );

        $result = $query->fetchAll();

        /****************************************
         1	Aguardando pagamento: o comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento.
        2	Em análise: o comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.
        3	Paga: a transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.
        4	Disponível: a transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.
        5	Em disputa: o comprador, dentro do prazo de liberação da transação, abriu uma disputa.
        6	Devolvida: o valor da transação foi devolvido para o comprador.
        7	Cancelada: a transação foi cancelada sem ter sido finalizada.
        8	Debitado: o valor da transação foi devolvido para o comprador.
        9	Retenção temporária: o comprador abriu uma solicitação de chargeback junto à operadora do cartão de crédito.
        ****************************************/

        foreach ($result as $r) :
        if ($r['pagseguroToken'] != null) :

            $url = $linkPag . '/v2/transactions/' . $r['pagseguroToken'] . '?email=' . $emailPag . '&token=' . $tokenPag;
            // Call the performCurlRequest method from the trait.
            $transaction = $this->performCurlRequest($url);

            $urlRet = $linkPag . '/v2/pre-approvals/' . $r['pagseguroToken'] . '?email=' . $emailPag . '&token=' . $tokenPag . '';

            $output_r = $this->performCurlRequest($urlRet);

            if ($output_r != false) {
                // Call the performCurlRequest method from the trait.
                $retorno = $output_r;
            }

            if ($transaction->status[0] == '3' || (isset($retorno) && $retorno->status[0] == 'ACTIVE')) :
                // Atualizando Status da Assinatura após o Pagamento
                $load_node = Node::load($r['node_id']);
                $load_node->set('field_ass_situacao', 'ativo');
                $load_node->save();

                $user = User::load($r['user_id']);
                $user->addRole('membro');
                $user->save();

                $mailMarketingRemove = \Drupal::service('concerto.util')->contatoEmailMarketing([$r['email']], [13], 'remover');
                $mailMarketingImportar = \Drupal::service('concerto.util')->contatoEmailMarketing([$r['email']], [14], 'importar');
            endif;

        endif;
        endforeach;

        // Salva LOG do Crawler
        $crawler = \Drupal::service('concerto.util')->saveCrawler('ValidacaoDasAssinaturas');

        echo 'atualizado';
        exit;
    }

    function renovaAssinaturas()
    {
        $query = db_select('pagseguro_credenciais', 'pag');
        $query->addField('pag', 'email');
        $query->addField('pag', 'token');
        $query->addField('pag', 'link');
        $query->range(0,1);

        $pagseguroCredenciais = $query->execute()->fetchAll();

        $linkPag = $pagseguroCredenciais[0]->link;
        $emailPag = $pagseguroCredenciais[0]->email;
        $tokenPag = $pagseguroCredenciais[0]->token;

        $connection = \Drupal::database();

        $query = $connection->query("
            SELECT
            fd.uid as user_id,
            faa.ass_nid as node_id,
            fci.field_concerto_id_value as concerto_id,
            fn.field_full_name_value as nome_completo,
            fd.mail as email,
            fd.status,
            fp.field_phone_value as telefone,
            fc.field_celular_value as celular,
            fcpf.field_cpf_value as cpf,
            fma.field_member_address_value as enredeco,
            fpc.field_postal_code_value as cep,
            fmc.field_member_city_value as cidade,
            fmr.field_member_region_value as estado,
            fe.field_empresa_value as empresa,
            fap.field_ass_produto_nome_value as assinatura,
            fas.field_ass_situacao_value as situacao,
            fcv.field_cadastro_via_value as cadastro_via,
            pagToken.field_assinatura_pagseguro_value as pagseguroToken,
            DATE_FORMAT(from_unixtime(faa.ass_created), '%d/%m/%Y') as data_da_assinatura,
            fatp.field_ass_tipo_de_pagamento_value as plano,
            favd.field_ass_validade_value as validade,
            favl.field_ass_valor_value as valor
            FROM users_field_data fd
            JOIN user__roles ur ON fd.uid = ur.entity_id
            LEFT JOIN user__field_phone fp ON fd.uid = fp.entity_id
            LEFT JOIN user__field_full_name fn ON fd.uid = fn.entity_id
            LEFT JOIN user__field_celular fc ON fd.uid = fc.entity_id
            LEFT JOIN user__field_cpf fcpf ON fd.uid = fcpf.entity_id
            LEFT JOIN user__field_empresa fe ON fd.uid = fe.entity_id
            LEFT JOIN user__field_member_address fma ON fd.uid = fma.entity_id
            LEFT JOIN user__field_postal_code fpc ON fd.uid = fpc.entity_id
            LEFT JOIN user__field_member_city fmc ON fd.uid = fmc.entity_id
            LEFT JOIN user__field_member_region fmr ON fd.uid = fmr.entity_id
            LEFT JOIN user__field_concerto_id fci ON fd.uid = fci.entity_id
            LEFT JOIN user__field_cadastro_via fcv ON fd.uid = fcv.entity_id
            LEFT JOIN (SELECT max(nid) as ass_nid, field_ass_assinante_target_id, max(created) as ass_created FROM node_field_data subn
            JOIN node__field_ass_assinante subfass ON subfass.entity_id = subn.nid
            WHERE subn.type = 'assinaturas'
            GROUP BY field_ass_assinante_target_id) faa ON faa.field_ass_assinante_target_id = fd.uid
            LEFT JOIN node__field_ass_produto_nome fap ON fap.entity_id = faa.ass_nid
            JOIN node__field_ass_situacao fas ON fas.entity_id = faa.ass_nid
            LEFT JOIN node__field_ass_tipo_de_pagamento fatp ON fatp.entity_id = faa.ass_nid
            JOIN node__field_ass_validade favd ON favd.entity_id = faa.ass_nid
            LEFT JOIN node__field_ass_valor favl ON favl.entity_id = faa.ass_nid
            JOIN node__field_assinatura_pagseguro pagToken ON pagToken.entity_id = faa.ass_nid
            WHERE fas.field_ass_situacao_value = 'ativo' AND CURRENT_DATE() = favd.field_ass_validade_value AND fcv.field_cadastro_via_value = 'site'
            ",
            [],
            [
                'target' => 'replica',
                'fetch' => PDO::FETCH_ASSOC,
            ]
        );

        $results = $query->fetchAll();

        $result = $this->removeDuplicatedArrays($results);

        /****************************************
        1	Aguardando pagamento: o comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento.
        2	Em análise: o comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.
        3	Paga: a transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.
        4	Disponível: a transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.
        5	Em disputa: o comprador, dentro do prazo de liberação da transação, abriu uma disputa.
        6	Devolvida: o valor da transação foi devolvido para o comprador.
        7	Cancelada: a transação foi cancelada sem ter sido finalizada.
        8	Debitado: o valor da transação foi devolvido para o comprador.
        9	Retenção temporária: o comprador abriu uma solicitação de chargeback junto à operadora do cartão de crédito.
        ****************************************/

        foreach ($result as $r) :
            if ($r['pagseguroToken'] != null) :
                if ($r['plano'] == 'anual' || $r['plano'] == 'Anual' || $r['plano'] == 'mensal' || $r['plano'] == 'Mensal') :
                    $url = $linkPag . '/v2/transactions/' . $r['pagseguroToken'] . '?email=' . $emailPag . '&token=' . $tokenPag;
                    // Call the performCurlRequest method from the trait.
                    $transaction = $this->performCurlRequest($url);

                    $urlRet = $linkPag . '/v2/pre-approvals/' . $r['pagseguroToken'] . '?email=' . $emailPag . '&token=' . $tokenPag . '';
                    // Call the performCurlRequest method from the trait.
                    $retorno = $this->performCurlRequest($url);

                    if ($transaction->status[0] == '3' || $retorno->status[0] == 'ACTIVE') :
                        // Atualizando Status da Assinatura após o Pagamento
                        $load_node = Node::load($r['node_id']);

                        $today = date('Y-m-d');
                        $oneyear = date('Y-m-d', strtotime($today. ' + 1 year'));

                        $load_node->set('field_ass_validade', $oneyear);
                        $load_node->save();
                    endif;
                endif;
            endif;
        endforeach;

        // Salva LOG do Crawler
        $crawler = \Drupal::service('concerto.util')->saveCrawler('RenovaAssinatura');

        echo 'atualizado';
        exit;
    }

    // Custom comparison function for arrays.
    public function compareArrays($a, $b) {
        return ($a == $b) ? 0 : 1;
    }

    // Remove duplicated arrays.
    public function removeDuplicatedArrays($result) {
        $uniqueArray = array();

        foreach ($result as $subarray) {
            $found = false;
            foreach ($uniqueArray as $uniqueSubarray) {
                if ($this->compareArrays($subarray, $uniqueSubarray) === 0) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $uniqueArray[] = $subarray;
            }
        }

        return $uniqueArray;
    }

}
