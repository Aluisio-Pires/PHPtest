<?php

use app\Busca;


require 'app/Busca.php';
require 'config.php';

header('Content-Type: text/xml; charset=utf-8');

$cep = preg_replace('/\D/', "", $_GET["cep"]);

$db  = new Busca();
$xml = $db->fetchCep($cep);

if (is_string($xml)) {
    echo $xml;
}
