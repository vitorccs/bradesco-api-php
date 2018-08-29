# Bradesco API - SDK PHP
SDK PHP para a API de Registro On-line de Boletos de Cobrança Bradesco


## Descrição
SDK em PHP para integração com os serviços de Registro On-line de Boletos de Cobrança Bradesco

[Manual Registro de Boleto Bancario Online Dez17.pdf](https://github.com/vitorccs/bradesco-api-php/files/2332693/Manual_Registro_de_Boleto_Bancario_Online_Dez17.pdf)



## Instalação
Via Composer
```bash
composer require vitorccs/bradesco-api-php
```


## Variáveis de ambiente
Os seguintes parâmetros devem ser informados:

Parâmetro | Obrigatório | Padrão | Comentário
------------ | ------------- | ------------- | -------------
BRADESCO_CERT_PATH | Sim | null | Caminho do certificado PKCS#7 em formato .pfx
BRADESCO_CERT_PASSWORD | Sim | null | Senha do certificado
BRADESCO_SANDBOX | Não | true | Utilizar ambiente de Homologação (true) ou Produção (false)
BRADESCO_TIMEOUT | Não | 30 | Timeout em segundos para estabelecer conexão com a API
BRADESCO_FOLDER_PATH | Não | "" | Caminho para esta biblioteca gerar arquivos temporários, necessários parar realizar a criptografia. Os arquivos são criados com hash randômica e excluídos automaticamente, sem a necessidade de se preocupar em limpá-los periodicamente.

## Como usar
Após definir as variáveis de ambiente acima, basta utilizar o comando abaixo passando os dados do boleo a registrar em formato `array`.
```php
$person = \BradescoApi\BankSlip::create($data);
```

## Normalização de dados
* Campos ausentes de seu `array` de dados são automaticamente inseridos com seus respectivos valores padrão (confirme orientando na página 19 do manual da API do Bradesco).
* Datas no formato "dd-mm-yyyy" ou "dd/mm/yyy" são normalizadas para o formato exigido pela API ("dd.mm.yyyy").
* Moedas no formato 14.90 ou "14,90" são normalizadas para o formato exigido pela API ("1490").
* Números de CPF e CNPJ "123.456.789-01" são normalizadas para o formato exigido pela API ("00012345678901").

## Exemplo de implementação

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

putenv('BRADESCO_SANDBOX=true');
putenv('BRADESCO_TIMEOUT=20');
putenv('BRADESCO_CERT_PATH=myCertificate.pfx');
putenv('BRADESCO_CERT_PASSWORD=myPassword');

$data = [
  "nuCPFCNPJ" => "123456789",
  "filialCPFCNPJ" => "0001",
  "ctrlCPFCNPJ" => "39",
  "cdTipoAcesso" => "2",
  "idProduto" => "09",
  "nuNegociacao" => "123400000001234567",
  "cdBanco" => "237",
  "nuCliente" => "123456",
  "dtEmissaoTitulo" => "25/05/2017",
  "dtVencimentoTitulo" => "2017-06-20",
  "vlNominalTitulo" => 100.00,
  "cdEspecieTitulo" => "04",
  "nomePagador" => "Cliente Teste",
  "logradouroPagador" => "Rua Teste",
  "nuLogradouroPagador" => "90",
  "complementoLogradouroPagador" => "",
  "cepPagador" => "12345",
  "complementoCepPagador" => "500",
  "bairroPagador" => "Bairro Teste",
  "municipioPagador" => "Cidade Teste",
  "ufPagador" => "SP",
  "cdIndCpfcnpjPagador" => "1",
  "nuCpfcnpjPagador" => "549.435.260-98",
];

try {
    $bankSlip = \BradescoApi\BankSlip::create($data);
    print_r($bankSlip);
} catch (\Exception $e) {
    echo $e->getMessage();
}
```

## Testes
Caso queira contribuir, por favor, implementar testes de unidade em PHPUnit.

Para executar:
1) Faça uma cópia de phpunit.xml.dist em phpunit.xml na raíz do projeto
2) Altere os parâmtros ENV com os dados de seu acesso
3) Execute o comando abaixo no terminal dentro da pasta deste projeto:

```bash
composer test
```
