# Bradesco API - SDK PHP
SDK PHP para a API de Registro On-line de Boletos de Cobrança Bradesco

## Requisitos
* PHP >= 7.1

## Descrição
SDK em PHP para integração com os serviços de Registro On-line de Boletos de Cobrança Bradesco

[Manual_Registro_de_Boleto_Bancario_Online_Janeiro 2019.pdf](https://github.com/vitorccs/bradesco-api-php/files/4225281/Manual_Registro_de_Boleto_Bancario_Online_Janeiro.2019.pdf)

## Instalação
Via Composer
```bash
composer require vitorccs/bradesco-api-php
```


## Parâmetros
Parâmetro | Obrigatório | Padrão | Comentário
------------ | ------------- | ------------- | -------------
BRADESCO_CERT_PATH | Sim | - | Caminho do certificado PKCS#7 em formato .pfx
BRADESCO_CERT_PASSWORD | Sim | - | Senha do certificado
BRADESCO_SANDBOX | Não | true | Utilizar ambiente de Homologação (true) ou Produção (false)
BRADESCO_TIMEOUT | Não | 30 | Timeout em segundos para estabelecer conexão com a API
BRADESCO_FOLDER_PATH | Não | "" | Caminho para esta biblioteca gerar arquivos temporários, necessários parar realizar a criptografia. Os arquivos são criados com hash randômica e excluídos automaticamente, sem a necessidade de se preocupar em limpá-los periodicamente.

## Como usar
1) Os parâmetros podem ser definidos por váriaveis de ambiente:
```php
putenv('BRADESCO_SANDBOX=true');
putenv('BRADESCO_TIMEOUT=20');
putenv('BRADESCO_CERT_PATH=myCertificate.pfx');
putenv('BRADESCO_CERT_PASSWORD=myPassword');
```

ou passados por `array`:
```php
\BradescoApi\Http\Bradesco::setParams([
    'BRADESCO_SANDBOX' => true,
    'BRADESCO_TIMEOUT' => 20,
    'BRADESCO_CERT_PATH' => 'myCertificate.pfx',
    'BRADESCO_CERT_PASSWORD' => 'myPassword'
]);
```

2) Em seguida, basta utilizar o comando abaixo passando os dados do boleto em formato `array`.
```php
$boleto = \BradescoApi\BankSlip::create($data);
```

## Normalização de dados
Foram adicionadas diversas funções para normalizar os dados conforme exigido pela API do Bradesco:
* Campos ausentes são inseridos com seus respectivos valores padrão (página 19 do manual).
* Valores em `null` são trocados para vazio "".
* Valores em `integer` ou `float` são convertidos para string.
* Valores com caracteres especiais (ção) são substituídos por caracteres básicos (cao).
* Valores de nome e endereço do pagador são recortados para a quantidade máxima de caracteres.
* Datas no formato "yyyy-mm-dd" ou "dd/mm/yyy" são normalizadas para "dd.mm.yyyy".
* Moedas no formato 14.90 ou "14,90" são normalizadas para "1490".
* Números de CPF e CNPJ "123.456.789-01" são normalizadas para "00012345678901".

## Exemplo de implementação

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

putenv('BRADESCO_SANDBOX=true');
putenv('BRADESCO_TIMEOUT=20');
putenv('BRADESCO_CERT_PATH=myCertificate.pfx');
putenv('BRADESCO_CERT_PASSWORD=myPassword');

use BradescoApi\Exceptions\BradescoApiException;
use BradescoApi\Exceptions\BradescoRequestException;

$data = [
  "nuCPFCNPJ" => "123456789",
  "filialCPFCNPJ" => "0001",
  "ctrlCPFCNPJ" => "39",
  "idProduto" => "09",
  "nuNegociacao" => "123400000001234567",
  "nuCliente" => "123456",
  "dtEmissaoTitulo" => "25/05/2017",
  "dtVencimentoTitulo" => "2017-06-20",
  "vlNominalTitulo" => 100.00,
  "cdEspecieTitulo" => "04",
  "nomePagador" => "Cliente Teste",
  "logradouroPagador" => "Rua Teste",
  "nuLogradouroPagador" => "90",
  "complementoLogradouroPagador" => null,
  "cepPagador" => "12345",
  "complementoCepPagador" => "500",
  "bairroPagador" => "Bairro Teste",
  "municipioPagador" => "Cidade Teste",
  "ufPagador" => "SP",
  "nuCpfcnpjPagador" => "549.435.260-98",
];

try {
    $bankSlip = \BradescoApi\BankSlip::create($data);
    print_r($bankSlip);
} catch (BradescoApiException $e) { // erros retornados pela API Bradesco
    echo $e->getErrorCode() == 69 // este é o único código de erro que não exige tratativa
         ? "API Bradesco indica que boleto já foi registrado"
         : sprintf("%s (%s)", $e->getMessage(), $e->getErrorCode());
} catch (BradescoRequestException $e) { // erros não tratados (erros HTTP 4xx e 5xx)
    echo sprintf("%s (%s)", $e->getMessage(), $e->getErrorCode());
} catch (\Exception $e) { // demais erros
    echo $e->getMessage();
}
```

## Dicas
* Mesmo em ambiente Sandbox é necessário utilizar o arquivo e senha correta do certificado, e utilizar os dados reais do beneficiário (nuCPFCNPJ, filialCPFCNPJ, ctrlCPFCNPJ, nuNegociacao). Também é necessário que a sua carteira de boletos já esteja autorizada junto ao Bradesco para utilizar a API de Registro On-line.
* Como qualquer serviço, a API do Bradesco não possui 100% de disponibilidade. É recomendável armazenar o status de sucesso, e prever algum tratamento de retentativa para os casos que não retornaram sucesso.
* A exceção BradescoApiException é lançada quando é detectado alguma mensagem de erro retornada pela própria API Bradesco, e a exceção BradescoRequestException para problemas diversos de servidor e conexão (erros HTTP 4xx e 5xx) 
* A única exceção que não exige retentativa, é a de código 69 pois representa que o Boleto já se encontra registrado 

## OpenSSL 3
A partir da versão 3 do OpenSSL, o certificado `.pfx` pode apresentar erro de leitura e será necessário habilitar o modo "legacy" ([veja no StackOverflow](https://stackoverflow.com/questions/73832854/php-openssl-pkcs12-read-error0308010cdigital-envelope-routinesunsupported)) 

## Testes
Caso queira contribuir, por favor, implementar testes de unidade em PHPUnit.

Para executar:
1) Faça uma cópia de phpunit.xml.dist em phpunit.xml na raíz do projeto
2) Altere os parâmtros ENV com os dados de seu acesso
3) Execute o comando abaixo no terminal dentro da pasta deste projeto:

```bash
composer test
```
