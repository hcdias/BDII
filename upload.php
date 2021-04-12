<?php

require 'vendor/autoload.php';

$surveyName = filter_input(INPUT_POST, 'survey_name', FILTER_SANITIZE_STRING);
if($surveyName == ''){
  $surveyName = uniqid()."_".gmdate("d-m-Y H:i:s");
}

$dir = "./uploads/$surveyName";

if(!is_dir("$dir")){
  mkdir("$dir");
}

foreach( $_FILES['file']['error'] as $key => $error ){
  if($error == UPLOAD_ERR_OK){
    $tmpName = $_FILES['file']['tmp_name'][$key];
    $name = str_replace(" ","_",filter_var(basename($_FILES["file"]["name"][$key]), FILTER_SANITIZE_STRING));
    move_uploaded_file($tmpName,"$dir/$name");
  }else{
    //tratar erro
  }
}

$csv = new Helpers\ImportCSV($dir,$surveyName);
if($csv->import()){
  header('Content-type: application/json');
  echo json_encode(['imported'=>true]);
}