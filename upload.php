<?php

require 'vendor/autoload.php';

$surveyName = filter_input(INPUT_POST, 'survey_name', FILTER_SANITIZE_STRING);
if($surveyName == ''){
  $surveyName = uniqid()."-".gmdate("d-m-Y H:i:s");
}else{
  $surveyName .= "-".gmdate("d-m-Y H:i:s");
}

$dir = "./uploads/".uniqid();

$statusImport = true;
foreach( $_FILES['file']['error'] as $key => $error ){
  if($error == UPLOAD_ERR_OK){
    $tmpName = $_FILES['file']['tmp_name'][$key];
    $info = pathinfo($tmpName);

    if($_FILES['file']['type'][$key] !== "text/csv"){
      $statusImport = false;
      break;
    }

    $name = str_replace(" ","_",filter_var(basename($_FILES["file"]["name"][$key]), FILTER_SANITIZE_STRING));    
    if( !is_dir("$dir") ){
      mkdir("$dir");
    }
    move_uploaded_file($tmpName,"$dir/$name");

  }else{
    die('erro!');
  }
}

if(!$statusImport){
  header('HTTP/1.0 422 Invalid Request');
  echo json_encode(['status'=>'Arquivo inválido. O arquivo deve possuir o formato "text/csv".']);
  die;
}

$csv = new Helpers\ImportCSV($dir,$surveyName);
if($csv->import()){
  header('Content-type: application/json');
  echo json_encode(['status'=>$statusImport]);
}

