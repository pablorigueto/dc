(function ($, Drupal, once) {

  Drupal.behaviors.payments = {
    attach: function (context, settings) {

			if (tempo == 'mês' || tempo == 'ano') { /* Pagamento Recorrente (ASSINATURA) */

				$.ajax({ /* Criando Plano e Sessão de Pagamento com o PagSeguro */
					url: '/assinar/pagamento-recorrente',
					type: 'POST',
					data: data,
					success: function(response) {
						var plan = response.plan; /* Retorno PagSeguro */

							/* SET SESSION */
					    PagSeguroDirectPayment.setSessionId(response.sessionID);

					    /* Buscando Bandeira do Cartão de Crédito */
					    PagSeguroDirectPayment.getBrand({
					    	cardBin: cardNumber,
					    	success: function(response) {
					    		var brand = response.brand.name; /* Retorno PagSeguro */

					    		PagSeguroDirectPayment.createCardToken({ /* Gerando TOKEN do Cartão de Crédito */
					    			cardNumber: cardNumber,
					    			brand: brand,
					    			cvv: cvv,
					    			expirationMonth: expirationMonth,
					    			expirationYear: expirationYear,
					    			success: function(response) {
					    				var cardToken = response.card.token; /* Retorno PagSeguro */
					    				var hash = PagSeguroDirectPayment.getSenderHash();

					    				var dataAssinatura = {
												valor: valor,
												plano: plano,
												assinatura: assinatura,
												cpf: cpf,
												nome: nome,
												cardNumber: cardNumber,
												cvv: cvv,
												expirationMonth: expirationMonth,
												expirationYear: expirationYear,
												plan: plan,
												hash: hash,
												cardToken: cardToken
											};

						    			$.ajax({ /* Criando Assinatura no Plano Criado (Finaliza Compra) */
												url: '/assinar/pagamento-assinatura',
												type: 'POST',
												data: dataAssinatura,
												success: function(response) {
													if (response == 'erroPhone') {
														console.log('Erro ao finalizar sua compra: Telefone Inválido');

														$('.pagamento-parcelado').html('Seu número de telefone cadastrado é inválido.');
														$('.pagamento-parcelado').show();

														$('.loading').html('');
														$('#pagamento_parcelado').val('PROSSEGUIR');
														$('#pagamento_parcelado').removeClass('opacity-medium');
														$('#pagamento_parcelado').prop('disabled', false);

														return false;
													}

													if (response != 'erro') {
														$('.pagamento-vista').hide();
														window.location = '/assinar/confirmacao';
													} else {
														console.log('Erro ao finalizar sua compra: ' + JSON.stringify(response));

														$('.pagamento-parcelado').show();

														$('.loading').html('');
														$('#pagamento_parcelado').val('PROSSEGUIR');
														$('#pagamento_parcelado').removeClass('opacity-medium');
														$('#pagamento_parcelado').prop('disabled', false);

														return false;
													}
												},
												error: function(response) {
													console.log('Erro ao finalizar compra: ' + JSON.stringify(response));

													$('.pagamento-parcelado').show();

													$('.loading').html('');
													$('#pagamento_parcelado').val('PROSSEGUIR');
													$('#pagamento_parcelado').removeClass('opacity-medium');
													$('#pagamento_parcelado').prop('disabled', false);

													return false;
												},
												complete: function() {
													$('#pagamento_parcelado').val('FINALIZANDO');
												}
											});
					    			},
					    			error: function(response) {
					    				console.log('Erro ao gerar token do cartão: ' + JSON.stringify(response));

											$('.pagamento-parcelado').show();

											$('.loading').html('');
											$('#pagamento_parcelado').val('PROSSEGUIR');
											$('#pagamento_parcelado').removeClass('opacity-medium');
											$('#pagamento_parcelado').prop('disabled', false);

					    				return false;
					    			}
					    		});
					    	},
					    	error: function(response) {
					    		console.log('Erro ao buscar bandeira do cartão: ' + JSON.stringify(response));

									$('.pagamento-parcelado').show();

									$('.loading').html('');
									$('#pagamento_parcelado').val('PROSSEGUIR');
									$('#pagamento_parcelado').removeClass('opacity-medium');
									$('#pagamento_parcelado').prop('disabled', false);

					    		return false;
					    	}
					    });
					},
					error: function(response) {
						console.log('erro ' + JSON.stringify(response));
						return false;
					}
				});
			}
		}
	}


})(jQuery, Drupal, once);
