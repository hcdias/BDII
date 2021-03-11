<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client;

$collection = $client->tp->survey;

$document = $collection->find(['tipoQuestao'=>1],['projection'=>['textoQuestao'=>1]]);

$result = [];
foreach($document as $d){
	$result[] = iterator_to_array($d);
}

echo json_encode($result);