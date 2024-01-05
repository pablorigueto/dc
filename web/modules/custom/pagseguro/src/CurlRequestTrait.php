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

}
