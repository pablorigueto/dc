<?php

namespace Drupal\pagseguro;

trait CurlRequestTrait {

  protected function performCurlRequest($url) {
    $_h = curl_init();
    curl_setopt($_h, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=ISO-8859-1"));
    curl_setopt($_h, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($_h, CURLOPT_HTTPGET, 1);
    curl_setopt($_h, CURLOPT_URL, $url);
    curl_setopt($_h, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($_h, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($_h, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
    curl_setopt($_h, CURLOPT_DNS_CACHE_TIMEOUT, 2);
    $output = curl_exec($_h);
    curl_close($_h);

    return simplexml_load_string($output);

  }

  protected function createPlanOrPayment($url, $json) {

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, Array(
      'Accept: application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
      'Content-type: application/json;charset=ISO-8859-1'
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($json));

    $json = curl_exec($curl);
    curl_close($curl);

    $json_return = json_decode($json, TRUE);

    return $json_return['code'];

  }

  protected function getTheIdPlan($linkPag, $email, $token, $plan) {

    // Gera SESSION ID do PagSeguro.
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

    return array('plan' => $plan, 'sessionID' => $sessionID);

  }

}
