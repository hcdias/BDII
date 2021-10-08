# Visualizador de pesquisas eleitorais

Painel para visualização de pesquisas eleitorais, atividade referente ao trabalho prático da disciplina CSI442 - Banco de Dados II.

[Descricao TP](https://drive.google.com/file/d/1C-BKGCbA2bO5JhrJHpChY3TabN5vqLI0/view?usp=sharing)

[Artigo apresentação](https://drive.google.com/file/d/1GjYKzFZeHE0NOolEKed8xs2FCf0kdCFf/view?usp=sharing)


## Dependências para executar o projeto

- Composer
- PHP ^7.2
- Mongodb ^1.5

## Execução

Instale as dependencias do projeto utilizado o [composer](https://getcomposer.org/download/ "Composer download"): `$ composer install`

Após instalar as dependências, importe os dados das planilhas contidos no diretório `csv`, executando o script de importação disponível na raiz do projeto:

`php import.php`

A partir do terminal, disponibilize o acesso a partir do diretório raiz com o comando: `php -S localhost:8000`

Acesse o endereço no navegador: http://localhost:8000

## TODO
- Importar arquivos via upload, na interface?(sugestão de melhoria para o TP, não é requisito funcional)
