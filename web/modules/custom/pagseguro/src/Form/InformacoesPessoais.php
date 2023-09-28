<?php

namespace Drupal\concerto_assinatura\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InformacoesPessoais extends FormBase {

	public function getFormId() {
		return 'concerto_assinatura.informacoes_pessoais';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		/* Buscando Informações do Usuário Logado */
		$user = User::load(\Drupal::currentUser()->id());

		$uid = $user->get('uid')->value;
		$email = $user->get('mail')->value;
		$pais = '';

		if (!isset($email)) :
			$email = '';
		endif;

		/* Buscando Estados cadastrados no Banco de Dados */
		$queryE = db_select('estados', 'e');
    $queryE->addField('e', 'uf');
    $queryE->addField('e', 'nome');

    $resultsE = $queryE->execute()->fetchAll();

    $estados = array();
    foreach ($resultsE as $resultE) :
    	$estados[$resultE->uf] = $resultE->nome;
		endforeach;

		/* Buscando Cidades cadastrados no Banco de Dados */
		$queryC = db_select('cidades', 'c');
    $queryC->addField('c', 'nome');
    $queryC->orderBy('nome', 'ASC');

    $resultsC = $queryC->execute()->fetchAll();

    $cidades = array();
    foreach ($resultsC as $resultC) :
    	$cidades[$resultC->nome] = $resultC->nome;
		endforeach;

		$plano = (isset($_SESSION['plano'])) ? $_SESSION['plano'] : '';
    $assinatura = (isset($_SESSION['assinatura'])) ? $_SESSION['assinatura'] : '';

    $queryNode = \Drupal::entityQuery('node')
        ->condition('type', 'produto')
        ->condition('field_codigo_do_plano', $plano)
        ->range(0,1)
        ->sort('nid', 'ASC');

    $id = $queryNode->execute();
    $node = Node::loadMultiple($id);

    foreach ($node as $n) :
        if ($assinatura == 'avulso') :
            $valor = number_format($n->get('field_edicao_avulsa_valor')->getValue()[0]['value'], 2, ",", ".");
            $tempo = 'avulso';
            $validade = date('Y-m-d',strtotime(date('m/d/Y', mktime()) . " + 365 day"));
        endif;

        if ($assinatura == 'mensal') :
            $valor = number_format($n->get('field_valor_mensal_valor')->getValue()[0]['value'], 2, ",", ".");
            $tempo = 'mês';
            $validade = date('Y-m-d',strtotime(date('m/d/Y', mktime()) . " + 365 day"));
        endif;

        if ($assinatura == 'anual') :
            $valor = number_format($n->get('field_valor_anual_valor')->getValue()[0]['value'], 2, ",", ".");
            $tempo = 'ano';
            $validade = date('Y-m-d',strtotime(date('m/d/Y', mktime()) . " + 365 day"));
        endif;

				if ($n->get('field_receber_revista')->getValue()[0] != null) :
					$receber_revista = $n->get('field_receber_revista')->getValue()[0]['value'];
				else :
					$receber_revista = 0;
				endif;

        $item = [
        		'produto' => $n->id()
            ,'codigo' => $n->get('field_codigo_do_plano')->getValue()[0]['value']
            ,'title' => $n->get('title')->getValue()[0]['value']
            ,'assinatura' => $assinatura
            ,'plano' => $plano
            ,'valor' => $valor
            ,'tempo' => $tempo
						,'receber_revista' => $receber_revista
        ];

        $form['info'] = $item;
    endforeach;

		if (isset($_SESSION['node_assinatura']) && $_SESSION['node_assinatura'] != '') :
	    $assinaturaDados = Node::loadMultiple([$_SESSION['node_assinatura']]);
	    $dadosAssinatura = array_shift(array_slice($assinaturaDados, 0, 1));
		else :
			$queryAssinatura = \Drupal::entityQuery('node')
	        ->condition('type', 'assinaturas')
	        ->condition('field_ass_assinante', $user->id())
	        ->range(0,1)
	        ->sort('nid', 'DESC');

	    $idAssinatura = $queryAssinatura->execute();
	    $assinaturaDados = Node::loadMultiple($idAssinatura);
	    $dadosAssinatura = array_shift(array_slice($assinaturaDados, 0, 1));
		endif;

    /* Input Hidden */
    $form['produto'] = array(
			'#type' => 'hidden',
			'#value' => $form['info']['produto']
		);

    $form['valor'] = array(
			'#type' => 'hidden',
			'#default_value' => $valor
		);

		$form['plano'] = array(
			'#type' => 'hidden',
			'#default_value' => $plano
		);

		$form['validade'] = array(
			'#type' => 'hidden',
			'#default_value' => $validade
		);

		$form['pais'] = array(
			'#type' => 'hidden',
			'#default_value' => $pais
		);

		// $form['tempo'] = array(
		// 	'#type' => 'hidden',
		// 	'#default_value' => $item['tempo']
		// );

		$form['assinatura'] = array(
			'#type' => 'hidden',
			'#default_value' => $assinatura
		);

		/* Dados Cadastrais (Formulário) */
		$form['nome_completo'] = array(
			'#type' => 'textfield',
			'#id' => 'nome',
			'#title' => 'Nome completo',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => ($user->get('field_full_name')->getValue()[0] != null) ? $user->get('field_full_name')->getValue()[0]['value'] : ''
		);

		// $form['nome'] = array(
		// 	'#type' => 'textfield',
		// 	'#id' => 'nome',
		// 	'#title' => 'Nome',
		// 	'#required' => 'true',
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' => array('class' => array('input-cadastro')),
		// 	'#default_value' => $name
		// );
		//
		// $form['sobrenome'] = array(
		// 	'#type' => 'textfield',
		// 	'#id' => 'sobrenome',
		// 	'#title' => 'Sobrenome',
		// 	'#required' => 'true',
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' =>array('class' => array('input-cadastro')),
		// 	'#default_value' => $sobrenome
		// );

		$form['email'] = array(
			'#type' => 'email',
			'#id' => 'email_usuario',
			'#title' => 'E-mail',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => $email
		);

		$form['cpf'] = array(
			'#type' => 'textfield',
			'#id' => 'cpf',
			'#title' => 'CPF',
			'#placeholder' => '000.000.000-00',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro-cpf')),
			'#default_value' => ($user->get('field_cpf')->getValue()[0] != null) ? $user->get('field_cpf')->getValue()[0]['value'] : ''
		);

		$form['passaporte'] = array(
			'#type' => 'textfield',
			'#id' => 'passaporte',
			'#title' => 'Passaporte',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => (isset($user->get('field_passaporte')->getValue()[0])) ? $user->get('field_passaporte')->getValue()[0]['value'] : ''
		);

		$form['data_nascimento'] = array(
			'#type' => 'textfield',
			'#id' => 'data_nascimento',
			'#title' => 'Data de Nascimento',
			'#placeholder' => '00/00/0000',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => ($user->get('field_birthday')->getValue()[0] != null) ? $user->get('field_birthday')->getValue()[0]['value'] : ''
		);

		// field_phone -> Telefone
		// field_telefone_2 -> Celular

		$form['telefone'] = array(
			'#type' => 'textfield',
			'#id' => 'telefone',
			'#title' => 'Telefone',
			'#placeholder' => '(00) 0000-0000',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => ($user->get('field_phone')->getValue()[0] != null) ? $user->get('field_phone')->getValue()[0]['value'] : ''
		);

		$form['telefone_unmask'] = array(
			'#type' => 'textfield',
			'#id' => 'telefone_unmask',
			'#title' => 'Telefone',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => ($user->get('field_phone')->getValue()[0] != null) ? $user->get('field_phone')->getValue()[0]['value'] : ''
		);

		$form['telefone_2'] = array(
			'#type' => 'textfield',
			'#id' => 'telefone2',
			'#title' => 'Celular',
			'#placeholder' => '(00) 00000-0000',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => ($user->get('field_telefone_2')->getValue()[0] != null) ? $user->get('field_telefone_2')->getValue()[0]['value'] : ''
		);

		$form['telefone_unmask_2'] = array(
			'#type' => 'textfield',
			'#id' => 'telefone_unmask2',
			'#title' => 'Celular',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => ($user->get('field_telefone_2')->getValue()[0] != null) ? $user->get('field_telefone_2')->getValue()[0]['value'] : ''
		);

		/* Endereço da Cobrança (Formulário) */
		$form['cep_cobranca'] = array(
			'#type' => 'textfield',
			'#id' => 'cep_cobranca',
			'#title' => 'CEP',
			'#placeholder' => '00.000-000',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-endereco-cep')),
			'#default_value' => ($user->get('field_postal_code')->getValue()[0] != null) ? $user->get('field_postal_code')->getValue()[0]['value'] : ''
		);

		$form['rua_cobranca'] = array(
			'#type' => 'textfield',
			'#id' => 'rua_cobranca',
			'#title' => 'RUA',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-endereco')),
			'#default_value' => ($user->get('field_member_address')->getValue()[0] != null) ? $user->get('field_member_address')->getValue()[0]['value'] : ''
		);

		$form['numero_cobranca'] = array(
			'#type' => 'textfield',
			'#id' => 'numero_cobranca',
			'#title' => 'NÚMERO',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-endereco-numero')),
			'#default_value' => ($user->get('field_numero_cobranca')->getValue()[0] != null) ? $user->get('field_numero_cobranca')->getValue()[0]['value'] : ''
		);

		$form['complemento_cobranca'] = array(
			'#type' => 'textfield',
			'#id' => 'complemento_cobranca',
			'#title' => 'Complemento',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-endereco')),
			'#default_value' => ($user->get('field_complemento_cobranca')->getValue()[0] != null) ? $user->get('field_complemento_cobranca')->getValue()[0]['value'] : ''
		);

		$form['bairro_cobranca'] = array(
			'#type' => 'textfield',
			'#id' => 'bairro_cobranca',
			'#title' => 'BAIRRO',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-endereco')),
			'#default_value' => ($user->get('field_bairro_cobranca')->getValue()[0] != null) ? $user->get('field_bairro_cobranca')->getValue()[0]['value'] : ''
		);

		$form['estado_cobranca'] = array(
			'#type' => 'select',
			'#id' => 'estado_cobranca',
			'#title' => 'ESTADO',
			'#options' => $estados,
			'#required' => 'true',
			'#field_prefix' => '<br>',
			// '#ajax' => array(
			// 	'callback' => array($this, '\Drupal\concerto_assinatura\Form\InformacoesPessoais::getCidadesCobranca'),
			// 	'event' => 'change',
			// 	'wrapper' => 'cidade_cobranca',
			// 	'progress' => array(
			// 		'type' => 'throbber',
			// 		'message' => 'Carregando',
			// 	),
			// ),
			'#attributes' => array('class' => array('input-endereco')),
			'#default_value' => ($user->get('field_member_region')->getValue()[0] != null) ? $user->get('field_member_region')->getValue()[0]['value'] : ''
		);

		$form['cidade_cobranca'] = array(
			'#type' => 'select',
			'#id' => 'cidade_cobranca',
			'#title' => 'CIDADE',
			'#options' => $cidades,
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-endereco')),
			'#default_value' => ($user->get('field_member_city')->getValue()[0] != null) ? $user->get('field_member_city')->getValue()[0]['value'] : ''
		);

		// $form['entrega_cobranca'] = array(
		// 	'#type' => 'select',
		// 	'#id' => 'entrega_cobranca',
		// 	'#title' => 'O ENDEREÇO DE COBRANÇA E ENTREGA SÃO IGUAIS?',
		// 	'#options' => [
		// 	    'cadastro' => 'Sim',
		// 	    'novo' => 'Não'
		//   	],
		//   	'#required' => 'true',
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' => array('class' => array('input-endereco-cobranca')),
		// 	'#default_value' => isset($dadosAssinatura) && isset($dadosAssinatura->get('field_endereco_cobranca_iguais')->getValue()[0]['value']) ? $dadosAssinatura->get('field_endereco_cobranca_iguais')->getValue()[0]['value'] : 'cadastro',
		// );

		/* Endereço da Entrega (Formulário) */
		// $form['cep_entrega'] = array(
		// 	'#type' => 'textfield',
		// 	'#id' => 'cep_entrega',
		// 	'#title' => 'CEP',
		// 	'#placeholder' => '00.000-000',
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' => array('class' => array('input-endereco-cep')),
		// 	'#default_value' => isset($dadosAssinatura) && isset($dadosAssinatura->get('field_cep_entrega')->getValue()[0]['value']) ? $dadosAssinatura->get('field_cep_entrega')->getValue()[0]['value'] : ''
		// );
		//
		// $form['rua_entrega'] = array(
		// 	'#type' => 'textfield',
		// 	'#id' => 'rua_entrega',
		// 	'#title' => 'RUA',
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' => array('class' => array('input-endereco')),
		// 	'#default_value' => isset($dadosAssinatura) && isset($dadosAssinatura->get('field_rua_entrega')->getValue()[0]['value']) ? $dadosAssinatura->get('field_rua_entrega')->getValue()[0]['value'] : ''
		// );
		//
		// $form['numero_entrega'] = array(
		// 	'#type' => 'textfield',
		// 	'#id' => 'numero_entrega',
		// 	'#title' => 'NÚMERO',
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' => array('class' => array('input-endereco-numero')),
		// 	'#default_value' => isset($dadosAssinatura) && isset($dadosAssinatura->get('field_numero_entrega')->getValue()[0]['value']) ? $dadosAssinatura->get('field_numero_entrega')->getValue()[0]['value'] : ''
		// );
		//
		// $form['complemento_entrega'] = array(
		// 	'#type' => 'textfield',
		// 	'#id' => 'complemento_entrega',
		// 	'#title' => 'Complemento',
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' => array('class' => array('input-endereco')),
		// 	'#default_value' => isset($dadosAssinatura) && isset($dadosAssinatura->get('field_complemento_entrega_assina')->getValue()[0]['value']) ? $dadosAssinatura->get('field_complemento_entrega_assina')->getValue()[0]['value'] : ''
		// );
		//
		// $form['bairro_entrega'] = array(
		// 	'#type' => 'textfield',
		// 	'#id' => 'bairro_entrega',
		// 	'#title' => 'BAIRRO',
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' => array('class' => array('input-endereco')),
		// 	'#default_value' => isset($dadosAssinatura) && isset($dadosAssinatura->get('field_bairro_entrega')->getValue()[0]['value']) ? $dadosAssinatura->get('field_bairro_entrega')->getValue()[0]['value'] : ''
		// );
		//
		// $form['estado_entrega'] = array(
		// 	'#type' => 'select',
		// 	'#id' => 'estado_entrega',
		// 	'#title' => 'ESTADO',
		// 	'#options' => $estados,
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' => array('class' => array('input-endereco')),
		// 	'#default_value' => isset($dadosAssinatura) && isset($dadosAssinatura->get('field_estado_entrega')->getValue()[0]['value']) ? $dadosAssinatura->get('field_estado_entrega')->getValue()[0]['value'] : ''
		// );
		//
		// $form['cidade_entrega'] = array(
		// 	'#type' => 'select',
		// 	'#id' => 'cidade_entrega',
		// 	'#title' => 'CIDADE',
		// 	'#options' => $cidades,
		// 	'#field_prefix' => '<br>',
		// 	'#attributes' => array('class' => array('input-endereco')),
		// 	'#default_value' => isset($dadosAssinatura) && isset($dadosAssinatura->get('field_cidade_entrega')->getValue()[0]['value']) ? $dadosAssinatura->get('field_cidade_entrega')->getValue()[0]['value'] : ''
		// );

		$form['submit_informacoes'] = [
			'#type' => 'submit',
			'#value' => $this->t('PROSSEGUIR'),
			'#attributes' => array('class' => array('input-submit'))
		];

		$form['#theme'] = 'informacoes_pessoais';

		return $form;
	}

	public function validateForm(array &$form, FormStateInterface $form_state) {
		$user = User::load(\Drupal::currentUser()->id());
		$email = $user->get('mail')->value;
		$cpf = $user->get('field_cpf')->value;
		$pais = $form_state->getValue('pais');

		if ($pais == '' || !isset($pais)) :
			$form_state->setErrorByName('pais', $this->t('O país é obrigatório.'));
		endif;

		if (isset($pais) && $pais == 'BR') :

			if (isset($cpf) && $cpf != '') :
				if ($cpf != $form_state->getValue('cpf')) :
					$queryCPF = \Drupal::entityQuery('user');
					$idsCPF = $queryCPF
					->condition('field_cpf', $form_state->getValue('cpf'))
					->range(0,1)
					->execute();

					if (count($idsCPF) > 0) :
						$form_state->setErrorByName('cpf', $this->t('Esse CPF já está em uso.'));
					endif;
				endif;
			else :
				$queryCPF = \Drupal::entityQuery('user');
				$idsCPF = $queryCPF
				->condition('field_cpf', $form_state->getValue('cpf'))
				->range(0,1)
				->execute();

				if (count($idsCPF) > 0) :
					$form_state->setErrorByName('cpf', $this->t('Esse CPF já está em uso.'));
				endif;
			endif;

			if ($form_state->getValue('telefone') == '') :
				$form_state->setErrorByName('telefone', $this->t('O telefone é obrigatório.'));
			endif;

		endif;

		if ($form_state->getValue('email') != $email) {
			$query = \Drupal::entityQuery('user');
			$ids = $query
			->condition('mail', $form_state->getValue('email'))
			->range(0,1)
			->execute();

			if (count($ids) > 0) :
				$form_state->setErrorByName('email', $this->t('Esse email já está em uso.'));
			endif;
		}

		if ($form_state->getValue('data_nascimento') == '') :
			$form_state->setErrorByName('data_nascimento', $this->t('A data de nascimento é obrigatória.'));
		endif;
	}

	public function submitForm(array &$form, FormStateInterface $form_state) {
		$user = User::load(\Drupal::currentUser()->id());

		if ($user) {
			$field_concerto_id = $user->get(field_concerto_id)->getValue()[0]['value'];

			if (!isset($field_concerto_id) || $field_concerto_id == '' || $field_concerto_id == null) :
				$uids = \Drupal::entityQuery('user')
					->sort('field_concerto_id', 'DESC')
					->range(0, 1)
					->execute();

				$uid = reset($uids);
				$last_user = User::load($uid);
				$last_concerto_id = $last_user->get(field_concerto_id)->getValue()[0]['value'];

				$user->set('field_concerto_id', $last_concerto_id + 1);
			endif;

			/* Dados Pessoais */
			$user->set('field_full_name', $form_state->getValue('nome_completo'));

			$name = explode(' ', $form_state->getValue('nome_completo'));
			$user->set('field_nome', $name[0]);
			$user->set('field_sobrenome', $name[1]);

			$user->setEmail($form_state->getValue('email'));
			$user->set('name', $form_state->getValue('email'));
			$user->set('field_email', $form_state->getValue('email'));
			$user->set('field_cpf', $form_state->getValue('cpf'));
			$user->set('field_pais', $form_state->getValue('pais'));
			$user->set('field_passaporte', $form_state->getValue('passaporte'));
			$user->set('field_birthday', $form_state->getValue('data_nascimento'));

			if ($form_state->getValue('telefone') != '') {
				$user->set('field_phone', $form_state->getValue('telefone'));
				$user->set('field_telefone_2', $form_state->getValue('telefone_2'));
			} elseif ($form_state->getValue('telefone_unmask') != '') {
				$user->set('field_phone', $form_state->getValue('telefone_unmask'));
				$user->set('field_telefone_2', $form_state->getValue('telefone_unmask_2'));
			}

			/* Endereço */
			$user->set('field_postal_code', $form_state->getValue('cep_cobranca'));
			// $user->set('field_cep_cobranca', $form_state->getValue('cep_cobranca'));
			// $user->set('field_rua_cobranca', $form_state->getValue('rua_cobranca'));
			$user->set('field_member_address', $form_state->getValue('rua_cobranca'));
			$user->set('field_numero_cobranca', $form_state->getValue('numero_cobranca'));
			$user->set('field_complemento_cobranca', $form_state->getValue('complemento_cobranca'));
			// $user->set('field_bairro_cobranca', $form_state->getValue('bairro_cobranca'));
			// $user->set('field_estado_cobranca', $form_state->getValue('estado_cobranca'));
			$user->set('field_member_region', $form_state->getValue('estado_cobranca'));
			// $user->set('field_cidade_cobranca', $form_state->getValue('cidade_cobranca'));
			$user->set('field_member_city', $form_state->getValue('cidade_cobranca'));
			$user->set('field_endereco_igual', 1);
			$user->set('field_celular', ' ');
			$user->set('field_empresa', ' ');
			$user->set('field_cadastro_via', 'site');
			$user->set('field_observacoes', ' ');
			$user->set('changed', time());
			$user->activate();

			/* Cadastro da nova assinatura */
			if (isset($_SESSION['node_assinatura']) && $_SESSION['node_assinatura'] != '') :
		    $assinaturaDados = Node::loadMultiple([$_SESSION['node_assinatura']]);
		    $node = array_shift(array_slice($assinaturaDados, 0, 1));
			else :
				$node = Node::create(['type' => 'assinaturas']);
			endif;

			$produto = Node::load($form_state->getValue('produto'));

      $node->set('field_ass_assinante', $user->id());
      $node->set('field_ass_compra_via', 'Site');
      $node->set('title', $user->id() . ' - ' . $produto->get('title')->getValue()[0]['value']);

      $node->set('field_ass_produto_id', $form_state->getValue('produto'));
      $node->set('field_ass_produto_nome', $produto->get('title')->getValue()[0]['value']);
      $node->set('field_ass_situacao', 'inativo');
      $node->set('field_ass_tipo_de_pagamento', $form_state->getValue('assinatura'));
      $node->set('field_ass_validade', date('Y-m-d', strtotime($form_state->getValue('validade'))));
      $node->set('field_ass_valor', $form_state->getValue('valor'));
      $node->set('field_ass_codigo_interno', $node->id());
			$node->set('field_ass_observacoes', ' ');
			$node->set('field_endereco_cobranca_iguais', 'cadastro');

			$user->save();
			$node->save();

			if ($_SESSION['node_assinatura'] == '') :
				$_SESSION['node_assinatura'] = $node->id();
			endif;

			$url = Url::fromRoute('concerto_assinatura.pagamento');
			$form_state->setRedirectUrl($url);
		}
	}

	/* Funções Auxiliares */
	function getCidadesCobranca(array &$form, FormStateInterface $form_state) {
		$element = $form_state->getTriggeringElement();
		$estado = $element['#value'];

		if (!$estado) :
			$data[''] = '-- Selecione o Estado --';
		else :
			$query = db_select('cidades', 'c');
			$query->addField('c', 'nome');
			$query->condition('uf', $estado);

			$results = $query->execute()->fetchAll();

			foreach ($results as $result) :
				$data[$result->nome] = $result->nome;
			endforeach;
		endif;

		$elem = [
			'#type' => 'select',
			'#id' => 'cidade_cobranca',
			'#options' => $data,
			'#required' => 'true',
			'#attributes' => array('class' => array('input-endereco'))
		];

		return $elem;
	}

	function getCidadesEntrega(array &$form, FormStateInterface $form_state) {
		$element = $form_state->getTriggeringElement();
		$estado = $element['#value'];

		if (!$estado) :
			$data[''] = '-- Selecione o Estado --';
		else :
			$query = db_select('cidades', 'c');
			$query->addField('c', 'nome');
			$query->condition('uf', $estado);

			$results = $query->execute()->fetchAll();

			foreach ($results as $result) :
				$data[$result->nome] = $result->nome;
			endforeach;
		endif;

		$elem = [
			'#type' => 'select',
			'#id' => 'cidade_entrega',
			'#options' => $data,
			'#required' => 'true',
			'#attributes' => array('class' => array('input-endereco'))
		];

		return $elem;
	}

}
