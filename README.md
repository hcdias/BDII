# Visualizador de pesquisas eleitorais

Painel para visualização de pesquisas eleitorais

## Dependências para executar o projeto

- Composer
- PHP ^7.2
- Mongodb ^1.5

## Execução

Instale as dependencias do projeto utilizado o [composer](https://getcomposer.org/download/ "Composer download"): `$ composer install`

Após instalar as dependências, importe os dados das planilhas contidos no diretório `csv`, executando o script de importação disponível na raiz do projeto:

`php import.php`

A partir do terminal, disponibilize o acesso a partir do diretório `/public` com o comando: `php -S localhost:8000`

Acesse o endereço no navegador: http://localhost:8000

##TODO
- Importar arquivos via upload, na interface?(sugestão de melhoria para o TP, não é requisito funcional)