<?php
require 'vendor/autoload.php';

$id = $_GET['id'];

$client = new MongoDB\Client;

$collection = $client->tp->survey;

$result = $collection->findOne(['_id'=> new MongoDB\BSON\ObjectId($id)]);

header('Content-type: application/json');
echo json_encode($result);