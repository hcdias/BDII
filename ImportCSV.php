<?php

namespace Helpers;

require 'vendor/autoload.php';

class ImportCSV
{
  
  private $dir;
  private $client;

  function __construct($dir,$surveyName)
  {
    $this->dir = $dir;    
    $this->surveyName = $surveyName;
    $this->client = new \MongoDB\Client;
  }

  public function import()
  {
    $db = $this->client->tp;
    $collection = $db->survey;

    $files = $this->getFiles();
    foreach($files as $key => $filename){

      $fileHandler = new \SplFileObject($filename, 'r');
      $line = $fileHandler->fgetcsv(';');

      //arquivos no formato 'pesquisa geral'
      if( $line[0] !== null && $line[2] !== "" ){
        $arrPesquisa = [];
        $arrPesquisa['descricaoPesquisa'] = $this->surveyName;
        $arrPesquisa['identificadorPesquisa'] = strtolower(str_replace(" ", "_", $this->surveyName));
        $arrPesquisa['numeroQuestao'] = filter_var(trim($line[0]),FILTER_SANITIZE_STRING);
        $arrPesquisa['textoQuestao'] = filter_var(trim($line[2]), FILTER_SANITIZE_STRING);
        $arrPesquisa['entrevistados']  = filter_var(trim($line[3]),FILTER_SANITIZE_STRING);
        $arrPesquisa['tipoQuestao'] = 1;

        $fileHandler->next();
        while( !$fileHandler->eof() ){
          $lineItens = $fileHandler->fgetcsv(';');
          if( $lineItens[0] !== null && $lineItens[2] !== "" ){
            if( $lineItens[0] !== "" ){
              $arrPesquisa['numeroQuestao'] = filter_var(trim($lineItens[0]), FILTER_SANITIZE_STRING);
              $arrPesquisa['textoQuestao']  = filter_var(trim($lineItens[2]), FILTER_SANITIZE_STRING);
              $arrPesquisa['entrevistados'] = filter_var(trim($lineItens[3]), FILTER_SANITIZE_STRING);
            }else{
              $arrPesquisa['resultado'][]  = [
                'opcao' => filter_var(trim($lineItens[2]), FILTER_SANITIZE_STRING), 
                'votos'=> filter_var(trim($lineItens[3]), FILTER_SANITIZE_STRING), 
                'percentual'=>filter_var(trim($lineItens[4]), FILTER_SANITIZE_STRING)
              ];
            }
          }
        }

        $collection->insertOne($arrPesquisa);

      }else{
        //arquivos no formato 'pesquisa por bairro'
        $arrPesquisa = [];
        $arrPesquisa['descricaoPesquisa'] = $this->surveyName;
        $arrPesquisa['identificadorPesquisa'] = strtolower(str_replace(" ", "_", $this->surveyName));
        $arrPesquisa['bairro'] =  filter_var(trim($line[0]),FILTER_SANITIZE_STRING);
        
        $fileHandler->next();
        $line = $fileHandler->fgetcsv(';');
        $arrPesquisa['tipoQuestao'] = 2;
        $fileHandler->next();

        $arrPesquisa['questoes'] = [];
        $questaoIndex = 'notset';

        while( !$fileHandler->eof() ){
          $lineItens = $fileHandler->fgetcsv(';');
          if( $lineItens[0] !== null && $lineItens[2] !== "" ){
            if( $lineItens[0] !== "" ){
              $questaoIndex = $lineItens[0];
              $questao = [];
              $questao['numeroQuestao'] = filter_var(trim($lineItens[0]),FILTER_SANITIZE_STRING);
              $questao['textoQuestao']  = filter_var(trim($lineItens[2]),FILTER_SANITIZE_STRING);
              $questao['entrevistados'] = filter_var(trim($lineItens[3]),FILTER_SANITIZE_STRING);
              $arrPesquisa['questoes'][$questaoIndex] = $questao;
            }else{                
              $arrPesquisa['questoes'][$questaoIndex]['resultado'][] =  [
                'opcao' => filter_var(trim($lineItens[2]),FILTER_SANITIZE_STRING), 
                'votos'=> filter_var(trim($lineItens[3]),FILTER_SANITIZE_STRING), 
                'percentual'=>filter_var(trim($lineItens[4]),FILTER_SANITIZE_STRING   ) 
              ];
            }
          }
        }

        $collection->insertOne($arrPesquisa);
      }
    }

    return true;
  }

  protected function getFiles()
  {
    $arr = [];
    foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dir)) as $file){
      if ($file->isDir()) continue;

      $arr[substr($file->getFileName(),6,2) < 10 ? "0".substr($file->getFileName(),6,1) : substr($file->getFileName(),6,2) ] = $file;
    }

    ksort($arr);

    return  $arr;
  }
}