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
Obs: Não é necessário se preocupar em informar valores padrão para os campos não utilizados (página 19 do manual), a biblioteca já os insere automaticamente quando detecta que não consta em seu `array`.
```php
$person = \BradescoApi\BankSlip::create($data);
```

## Exemplo de implementação

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

putenv('BRADESCO_SANDBOX=true');
putenv('BRADESCO_TIMEOUT=30');
putenv('BRADESCO_CERT_PATH=mycertificate.pfx');
putenv('BRADESCO_CERT_PASSWORD=mypassword');

$data = [
  "nuCPFCNPJ" => "123456789",
  "filialCPFCNPJ" => "0001",
  "ctrlCPFCNPJ" => "39",
  "cdTipoAcesso" => "2",
  "idProduto" => "09",
  "nuNegociacao" => "123400000001234567",
  "cdBanco" => "237",
  "nuCliente" => "123456",
  "dtEmissaoTitulo" => "25.05.2017",
  "dtVencimentoTitulo" => "20.06.2017",
  "vlNominalTitulo" => "100",
  "cdEspecieTitulo" => "04",
  "nomePagador" => "Cliente Teste",
  "logradouroPagador" => "rua Teste",
  "nuLogradouroPagador" => "90",
  "complementoLogradouroPagador" => "",
  "cepPagador" => "12345",
  "complementoCepPagador" => "500",
  "bairroPagador" => "bairro Teste",
  "municipioPagador" => "Teste",
  "ufPagador" => "SP",
  "cdIndCpfcnpjPagador" => "1",
  "nuCpfcnpjPagador" => "12345648901234",
];

try {
    $bankSlip = \BradescoApi\BankSlip::create($data);
    print_r($bankSlip);
} catch (\Exception $e) {
    echo $e->getMessage();
}
```
