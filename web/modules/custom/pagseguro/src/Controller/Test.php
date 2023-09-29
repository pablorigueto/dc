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


class Test extends ControllerBase {
    /**
     * Trait to avoid a lot of repeated code.
     * To'do.
     */
    //use CurlRequestTrait;

    public function callPagSeguro(Request $request) {
        // $email = "pabloedurigueto@outlook.com";
        // $token = "50A402506BD14FC39C424C203A68E7B4"; //sandbox
        // $token = "40ca886d-5757-4730-9e77-a968bb3bfb0707a9e4b64e19915a03944348f96be2485826-e7a9-49d3-803c-4144cb48f886"; //prod

        // $sandbox = false;

        // $pagseguro = new PagSeguroAssinaturas($email, $token, $sandbox);
        
        // $codigoPlano = 'FFE70DD1E9E9AF16648EBFAF1DD61475';
        // $url = $pagseguro->assinarPlanoCheckout($codigoPlano);
        
        // echo 'URL para o Checkout: ' . $url;

        $email = "pabloedurigueto@outlook.com";
        $token = "50A402506BD14FC39C424C203A68E7B4";
        $sandbox = true;

        $pagseguro = new PagSeguroAssinaturas($email, $token, $sandbox);

        //Sete apenas TRUE caso queira importa o Jquery também. Caso já possua, não precisa
        $js = $pagseguro->preparaCheckoutTransparente(true);

        echo $js['completo']; //Importa todos os javascripts necessários
        ?>

        <h2> Campos Obrigatórios </h2>

        <p>Número do Cartão</p>
        <!-- OBRIGATÓRIO UM CAMPO COM O ID pagseguro_cartao_numero-->
        <input type="text" id="pagseguro_cartao_numero" value="4111111111111111"/>

        <p>CVV do cartão</p>
        <!-- OBRIGATÓRIO UM CAMPO COM O ID pagseguro_cartao_cvv-->
        <input type="text" id="pagseguro_cartao_cvv" value="123"/>

        <p>Mês de expiração do Cartao</p>
        <!-- OBRIGATÓRIO UM CAMPO COM O ID pagseguro_cartao_mes-->
        <input type="text" id="pagseguro_cartao_mes" value="12"/>

        <p>Ano de Expiração do Cartão</p>
        <!-- OBRIGATÓRIO UM CAMPO COM O ID pagseguro_cartao_ano-->
        <input type="text" id="pagseguro_cartao_ano" value="2030"/>

        <br/>

        <button id="botao_comprar">Comprar</button>

        <script type="text/javascript">

            //Gera os conteúdos necessários
            $('#botao_comprar').click(function() {
                PagSeguroBuscaHashCliente(); //Cria o Hash identificador do Cliente usado na transição
                PagSeguroBuscaBandeira();   //Através do pagseguro_cartao_numero do cartão busca a bandeira
                PagSeguroBuscaToken();      //Através dos 4 campos acima gera o Token do cartão  
                setTimeout(function() {
                    enviarPedido();
                }, 3000);
            });

            function enviarPedido() {
                /** FAÇA O QUE QUISER DAQUI PARA BAIXO **/
                alert($('#pagseguro_cliente_hash').val())
                alert($('#pagseguro_cartao_token').val())
                
                var data = {
                    hash:  $('#pagseguro_cliente_hash').val(),
                    token: $('#pagseguro_cartao_token').val()
                };
                
                $.post('https://dc.lndo.site/resp', data, function(response) {
                    alert(response);
                });
            }
        </script>

    <?php
    }

    function resp($response) {
        dump($response);
    }
}
