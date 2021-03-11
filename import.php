<?php
/**
 * Importa arquivos csv contendo os dados das pesquisas
 *
 * @author     Hc
 * @since      2021
 */

require 'vendor/autoload.php';

$client = new MongoDB\Client;

$tp = $client->tp;
$collection = $tp->survey;

$arr = [];

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./csv')) as $file){
    if ($file->isDir()) continue;

    $arr[substr($file->getFileName(),6,2) < 10 ? "0".substr($file->getFileName(),6,1) : substr($file->getFileName(),6,2) ] = $file;
}

ksort($arr);

foreach($arr as $key => $filename){

	echo "\n\n";
	echo "------> Arquivo {$filename->getFilename()} <--------\n";

    $fileHandler = new SplFileObject($filename, 'r');
    $line = $fileHandler->fgetcsv(';');

    //arquivos 4 a 14
    if( $line[0] !== null && $line[2] !== "" ){
		
		$arrPesquisa = [];
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
					$arrPesquisa['resultado'][]  = ['opcao' => trim($lineItens[2]), 'votos'=> trim($lineItens[3]), 'percentual'=>trim($lineItens[4]) ];

				}
			}
		}
		$result = $collection->insertOne($arrPesquisa);
		echo "Importado! \n\n";

	}else{
		//arquivo 15 em diante

		$arrPesquisa = [];		
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
					
					$arrPesquisa['questoes'][$questaoIndex]['resultado'][] =  ['opcao' => trim($lineItens[2]), 'votos'=> trim($lineItens[3]), 'percentual'=>trim($lineItens[4]) ];
				}
			}
		}
		$result = $collection->insertOne($arrPesquisa);
		echo "Importado! \n\n";
	}
	
	echo "EXECUCAO TERMINADA \n\n";
}
