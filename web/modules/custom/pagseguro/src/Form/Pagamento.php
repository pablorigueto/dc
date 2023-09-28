<?php

namespace Drupal\concerto_assinatura\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Pagamento extends FormBase {

	public function getFormId() {
		return 'concerto_assinatura.pagamento';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		/* Buscando Informações do Usuário Logado */
		$user = User::load(\Drupal::currentUser()->id());
		$cpf = $user->get('field_cpf')->value;

		/* Validando tipo de Plano e Assinatura */
    $plano = (isset($_SESSION['plano'])) ? $_SESSION['plano'] : '';
    $assinatura = (isset($_SESSION['assinatura'])) ? $_SESSION['assinatura'] : '';

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

        $item = [
            'codigo' => $n->get('field_codigo_do_plano')->getValue()[0]['value']
            ,'title' => $n->get('title')->getValue()[0]['value']
            ,'assinatura' => $assinatura
            ,'plano' => $plano
            ,'valor' => $valor
            ,'tempo' => $tempo
        ];

        $form['info'] = $item;
    endforeach;

    $opt = '';

    if ($item['tempo'] == 'avulso') :
    	$opt = [
	    'vista' => 'A vista (Débito Online)',
	    'parcelado' => 'Cartão de Crédito'
  	];
    endif;

    if ($item['tempo'] == 'mês') :
    	$opt = ['parcelado' => 'Assinatura Mensal (Cartão de Crédito)'];
    endif;

    if ($item['tempo'] == 'ano') :
    	$opt = ['parcelado' => 'Assinatura Anual (Cartão de Crédito)'];
    endif;

		$form['forma_pagamento'] = array(
			'#type' => 'select',
			'#id' => 'forma_pagamento',
			'#title' => 'Selecione uma forma de pagamento:',
			'#options' => $opt,
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro'))
		);

		/* A vista */
		$form['vista_nome'] = array(
			'#type' => 'textfield',
			'#id' => 'vista_nome',
			'#title' => 'NOME',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => ($user->get('field_full_name')->getValue()[0] != null) ? $user->get('field_full_name')->getValue()[0]['value'] : ''
		);

		$form['vista_cpf'] = array(
			'#type' => 'textfield',
			'#id' => 'vista_cpf',
			'#title' => 'CPF',
			'#value' => $cpf,
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro-cpf')),
			'#default_value' => ($user->get('field_cpf')->getValue()[0] != null) ? $user->get('field_cpf')->getValue()[0]['value'] : ''
		);

		$form['vista_fone'] = array(
			'#type' => 'textfield',
			'#id' => 'vista_fone',
			'#title' => 'TELEFONE',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro')),
			'#default_value' => ($user->get('field_phone')->getValue()[0] != null) ? $user->get('field_phone')->getValue()[0]['value'] : ''
		);

		/* Parcelado */
		$form['parcelado_nome_cartao'] = array(
			'#type' => 'textfield',
			'#id' => 'parcelado_nome_cartao',
			'#title' => 'NOME <span class="span-cartao">(IGUAL AO CARTÃO)</span>',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro', 'input-parcelado-nome-cartao'))
		);

		$form['parcelado_cpf'] = array(
			'#type' => 'textfield',
			'#id' => 'parcelado_cpf',
			'#title' => 'CPF',
			'#placeholder' => '000.000.000-00',
			'#value' => $cpf,
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro-cpf', 'input-parcelado'))
		);

		$form['parcelado_numero_cartao'] = array(
			'#type' => 'textfield',
			'#id' => 'parcelado_numero_cartao',
			'#title' => 'NÚMERO DO CARTÃO',
			'#placeholder' => '0000 - 0000 - 0000 - 0000',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro', 'input-parcelado'))
		);

		$form['parcelado_codigo_seguranca'] = array(
			'#type' => 'textfield',
			'#id' => 'parcelado_codigo_seguranca',
			'#title' => 'CÓDIGO DE SEGURANÇA',
			'#maxlength' => 3,
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#field_suffix' => '<span class="span-credit-card"><i class="fa fa-credit-card" aria-hidden="true"></i> <span class="info-card">Código de 3 números no verso do cartão.</span></span>',
			'#attributes' => array('class' => array('input-cadastro', 'input-parcelado-cod'))
		);

		$form['parcelado_validade_mes'] = array(
			'#type' => 'textfield',
			'#id' => 'parcelado_validade_mes',
			'#title' => 'VALIDADE',
			'#placeholder' => 'MÊS',
			'#required' => 'true',
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro', 'input-parcelado-validade'))
		);

		$form['parcelado_validade_ano'] = array(
			'#type' => 'textfield',
			'#id' => 'parcelado_validade_ano',
			'#placeholder' => 'ANO',
			'#required' => 'true',
			'#attributes' => array('class' => array('input-cadastro', 'input-parcelado-validade'))
		);

		/* Débito Online */
		$optBanco = [
			'bancodobrasil' => 'Banco do Brasil',
			'bradesco' => 'Bradesco',
			'itau' => 'Itáu'
		];

		$form['opt_bancos'] = array(
			'#type' => 'select',
			'#id' => 'opt_bancos',
			'#title' => 'Banco',
			'#options' => $optBanco,
			'#field_prefix' => '<br>',
			'#attributes' => array('class' => array('input-cadastro'))
		);

		/* Submit */
		$form['pagamento_parcelado'] = [
			'#type' => 'submit',
			'#value' => $this->t('PROSSEGUIR'),
			'#id' => 'pagamento_parcelado',
			'#attributes' => array('class' => array('input-submit'))
		];

		$form['pagamento_a_vista'] = [
			'#type' => 'submit',
			'#value' => $this->t('PROSSEGUIR'),
			'#id' => 'pagamento_a_vista',
			'#attributes' => array('class' => array('input-submit'))
		];

    /* Input Hidden */
    $form['valor'] = array(
			'#type' => 'hidden',
			'#default_value' => $valor
		);

		$form['plano'] = array(
			'#type' => 'hidden',
			'#default_value' => $plano
		);

		$form['tempo'] = array(
			'#type' => 'hidden',
			'#default_value' => $item['tempo']
		);

		$form['assinatura'] = array(
			'#type' => 'hidden',
			'#default_value' => $assinatura
		);

    $form['#theme'] = 'pagamento';

		return $form;
	}

	public function validateForm(array &$form, FormStateInterface $form_state) {
		if ($form_state->getValue('parcelado_nome_cartao') == '') :
			$form_state->setErrorByName('parcelado_nome_cartao', $this->t('Nome obrigatório.'));
		endif;
	}

	public function submitForm(array &$form, FormStateInterface $form_state) {
		// Submit via JavaScript
	}

}
