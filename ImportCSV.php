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
        $arrPesquisa['numeroQuestao'] = trim($line[0]);
        $arrPesquisa['textoQuestao'] = trim($line[2]);
        $arrPesquisa['entrevistados']  = trim($line[3]);
        $arrPesquisa['tipoQuestao'] = 1;

        $fileHandler->next();
        while( !$fileHandler->eof() ){
          $lineItens = $fileHandler->fgetcsv(';');
          if( $lineItens[0] !== null && $lineItens[2] !== "" ){
            if( $lineItens[0] !== "" ){
              $arrPesquisa['numeroQuestao'] = trim($lineItens[0]);
              $arrPesquisa['textoQuestao']  = trim($lineItens[2]);
              $arrPesquisa['entrevistados'] = trim($lineItens[3]);
            }else{
              $arrPesquisa['resultado'][]  = [
                'opcao' => trim($lineItens[2]), 
                'votos'=> trim($lineItens[3]), 
                'percentual'=>trim($lineItens[4]) 
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
        $arrPesquisa['bairro'] =  trim($line[0]);
        
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
              $questao['numeroQuestao'] = trim($lineItens[0]);
              $questao['textoQuestao']  = trim($lineItens[2]);
              $questao['entrevistados'] = trim($lineItens[3]);
              $arrPesquisa['questoes'][$questaoIndex] = $questao;
            }else{                
              $arrPesquisa['questoes'][$questaoIndex]['resultado'][] =  [
                'opcao' => trim($lineItens[2]), 
                'votos'=> trim($lineItens[3]), 
                'percentual'=>trim($lineItens[4]) 
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