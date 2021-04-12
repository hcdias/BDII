<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client;

$collection = $client->tp->survey;

$document = $collection->aggregate(
  [
    ['$group'  => ['_id'=>'$descricaoPesquisa']],
    ['$sort'   => ['_id'=>1]]
  ],  
);

$result = [];
foreach($document as $key => $d){  
  $itens = iterator_to_array($d);
  $result[$key]['identificadorPesquisa'] = strtolower(str_replace(" ", "_", $itens['_id']));
  $result[$key]['descricao'] = $itens['_id'];
}

header('Content-type: application/json');
echo json_encode($result);