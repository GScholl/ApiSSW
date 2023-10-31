<?php

require_once "Api.php";

class Cotacao extends Api
{

    // Contém apenas parametros Obrigatórios, Caso necessário consultar API  Para adicionar algum parametro
    // Envia requisição sem senha do pagador
    public function enviaCotacao(
        string $cpfCnpjPagador, // CPF ou CNPJ do pagador sendo adicionado "000" em caso de CPF
        string $cepOrigem, // CEP de origem sendo o padrão "00000000" sem traço
        string $cepDestino, // CEP de destino sendo o padrão "00000000" sem traço
        float $valorMercadoria, // Valor da mercadoria duas casa decimais maior ou igual que 0.01.
        int $volumesMercadoria, //  quantidade de volumes da mercadoria maior ou igual que 1.
        float $peso, // peso em KG da mercadoria sendo com no máximo duas casas decimais maior ou igual que 0.01.
        float $volume, // volume em cm ^ 3 calculado a partir das dimensoes

    ) {

        $curl = curl_init();
        // XML montado para o CURL

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ssw.inf.br/ws/sswCotacaoColeta/index.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <cotarRequest>
              <dominio>' . $this->dominio . '</dominio>
              <login>' . $this->usuario . '</login>
              <senha>' . $this->senha . '</senha>
              <cnpjPagador>' . $cpfCnpjPagador . '</cnpjPagador> 
              <cepOrigem>' . $cepOrigem . '</cepOrigem>
              <cepDestino>' . $cepDestino . '</cepDestino>
              <valorNF>' . $valorMercadoria . '</valorNF>
              <quantidade>' . $volumesMercadoria . '</quantidade>
              <peso>' . $peso . '</peso>
              <volume>' . $volume . '</volume>
              <mercadoria>1</mercadoria>
            </cotarRequest>
          </soap:Body>
        </soap:Envelope>
        ',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: urn:sswinfbr.sswCotacaoColeta#cotar'
            ),
        ));
        // executa o request para o SSW
        $resposta = curl_exec($curl);
        curl_close($curl);

        // Tenta transformar de um XML para o tipo de dados compativel com php

        try {
            $ObjetoXML = simplexml_load_string($resposta);
            $RetornoXml = htmlspecialchars_decode((string) $ObjetoXML->xpath('//return')[0], ENT_QUOTES);
            $StringsXml = simplexml_load_string($RetornoXml);

            $cotacao['erro'] = (int) $StringsXml->erro;
            if ($cotacao['erro'] == 0) {
                $cotacao['mensagem'] = (string) $StringsXml->mensagem;
                $cotacao['frete'] = (float) $StringsXml->frete;
                $cotacao['prazo'] = (int) $StringsXml->prazo;
                $cotacao['cotacao'] = (int) $StringsXml->cotacao;
                $cotacao['token'] = (string) $StringsXml->token;
            } else {

                $cotacao['mensagem'] = (string) $StringsXml->mensagem;
            }
        } catch (\Exception $e) {

            $cotacao['erro'] = -3;
            $cotacao['mensagem'] = "Houve um erro desconhecido, por favor tente novamente mais tarde!";
        }

        return $cotacao;
    }


    // Envia requisição com senha do pagador
    public function enviaCotacaoSite(
        string $cpfCnpjPagador, // CPF ou CNPJ do pagador sendo adicionado "000" em caso de CPF
        string $cepOrigem, // CEP de origem sendo o padrão "00000000" sem traço
        string $cepDestino, // CEP de destino sendo o padrão "00000000" sem traço
        string $senhaPagador, // senha do pagador
        float $valorMercadoria, // Valor da mercadoria duas casa decimais maior ou igual que 0.01.
        int $volumesMercadoria, //  quantidade de volumes da mercadoria maior ou igual que 1.
        float $peso, // peso em KG da mercadoria sendo com no máximo duas casas decimais maior ou igual que 0.01.
        float $volume, // volume em cm ^ 3 calculado a partir das dimensoes

    ) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ssw.inf.br/ws/sswCotacaoColeta/index.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <cotarSiteRequest>  
                <dominio>' . $this->dominio . '</dominio>
                <login>' . $this->usuario . '</login>
                <senha>' . $this->senha . '</senha>
                <cnpjPagador>' . $cpfCnpjPagador . '</cnpjPagador> 
                <senhaPagador>' . $senhaPagador . '</senhaPagador>
                <cepOrigem>' . $cepOrigem . '</cepOrigem>
                <cepDestino>' . $cepDestino . '</cepDestino>
                <valorNF>' . $valorMercadoria . '</valorNF>
                <quantidade>' . $volumesMercadoria . '</quantidade>
                <peso>' . $peso . '</peso>
                <volume>' . $volume . '</volume>
                <mercadoria>1</mercadoria>
            </cotarSiteRequest>
          </soap:Body>
        </soap:Envelope>
        ',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: urn:sswinfbr.sswCotacaoColeta#cotarSite'
            ),
        ));

        $resposta = curl_exec($curl);


        curl_close($curl);

        try {
            $ObjetoXML = simplexml_load_string($resposta);
            $RetornoXml = htmlspecialchars_decode((string) $ObjetoXML->xpath('//return')[0], ENT_QUOTES);
            $StringsXml = simplexml_load_string($RetornoXml);
            $cotacao['erro'] = (int) $StringsXml->erro;
            if ($cotacao['erro'] == 0) {

                $cotacao['mensagem'] = (string) $StringsXml->mensagem;
                $cotacao['frete'] = (float) $StringsXml->frete;
                $cotacao['prazo'] = (int) $StringsXml->prazo;
                $cotacao['cotacao'] = (int) $StringsXml->cotacao;
                $cotacao['token'] = (string) $StringsXml->token;
            } else {

                $cotacao['mensagem'] = (string) $StringsXml->mensagem;
            }
        } catch (\Exception $e) {
            $cotacao['erro'] = -3;
            $cotacao['mensagem'] = "Houve um erro desconhecido, por favor tente novamente mais tarde!";
        }
        return $cotacao;
    }


   
}



