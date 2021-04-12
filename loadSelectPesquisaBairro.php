<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client;
$collection = $client->tp->survey;

$identificadorPesquisa = filter_input(INPUT_GET, 'identificadorPesquisa', FILTER_SANITIZE_STRING);

$document = $collection->find(
  [
    'tipoQuestao' => 2,
    'identificadorPesquisa' => $identificadorPesquisa, 
  ],
  [
    'projection'=>[ 'bairro' => 1 ]
  ]
);

$result = [];
foreach($document as $d){
  $result[] = iterator_to_array($d);
}

header('Content-type: application/json');
echo json_encode($result);