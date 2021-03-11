<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client;

$collection = $client->tp->survey;

$document = $collection->find(['tipoQuestao'=>2],[ 'projection'=> ['bairro'=>1] ]);

$result = [];
foreach($document as $d){
	$result[] = iterator_to_array($d);
}

header('Content-type: application/json');
echo json_encode($result);