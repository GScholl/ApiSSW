<?php

require_once "Api.php";

class Coleta extends Api
{

    public function enviaColeta(
        string $cpfCnpjRemetente, // Cpf ou CNPJ do remetente sendo cpf incrementado com "000"
        string $cpfCnpjDestinatario, // Cpf ou CNPJ do destinatario sendo cpf incrementado com "000"
        string $tipoPagamento, // tipo do pagamento sendo origem = "o" e destino = "D"
        string $cepEntrega, //  cep´de entrega formato sem mascara = "00000000"
        string $nomeSolicitante, //  Nome do Solicitante 
        $limiteColeta, // campo  date time YYYY-MM-DDTHH:MM:SS || 2023-11-05T11:49:00
        float $pesoCarga, // 2 casas decimais no máximo
        int $quantidadeVolumes, // número de volumes 
        int $numeroNf = null, // apenas 4 numeros  ex =  "0000" || Não obrigatório
        string $enderecoEntrega = null, //|| Não obrigatório
        string $observacao = null, // Observações da entrega || Não obrigatório
        string $instrucao = null, // Instruções de entrega || Não obrigatório
        string $tipoMercadoria = null, // Tipo da mercadoria || Não obrigatório
        float $valorMercadoria = null // float de duas casas decimais apenas
    ) {



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ssw.inf.br/ws/sswColeta/index.php',
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
                <coletarRequest>
                    <dominio>' . $this->dominio . '</dominio>
                    <login>' . $this->usuario . '</login>
                    <senha>' . $this->senha . '</senha>
                    <cnpjRemetente>' . $cpfCnpjRemetente . '</cnpjRemetente>
                    <cnpjDestinatario>' . $cpfCnpjDestinatario . '</cnpjDestinatario>
                    <numeroNF>' . $numeroNf . '</numeroNF>
                    <tipoPagamento>' . $tipoPagamento . '</tipoPagamento>
                    <enderecoEntrega>' . $enderecoEntrega . '</enderecoEntrega>
                    <cepEntrega>' . $cepEntrega . '</cepEntrega>
                    <solicitante>' . $nomeSolicitante . '</solicitante>
                    <limiteColeta>' . $limiteColeta . '</limiteColeta>
                    <quantidade>' . $quantidadeVolumes . '</quantidade>
                    <peso>' . $pesoCarga . '</peso>
                    <observacao>' . $observacao . '</observacao>
                    <instrucao>' . $instrucao . '</instrucao>
                    <valorMerc>' . $valorMercadoria . '</valorMerc>
                    <especie>' . $tipoMercadoria . '</especie>
                </coletarRequest>
                </soap:Body>
            </soap:Envelope>
            ',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: urn:sswinfbr.sswColeta#coletar'
            ),
        ));


        $resposta = curl_exec($curl);


        curl_close($curl);

        try {
            $ObjetoXML = simplexml_load_string($resposta);


            $RetornoXml = htmlspecialchars_decode((string) $ObjetoXML->xpath('//return')[0], ENT_QUOTES);


            $StringsXml = simplexml_load_string($RetornoXml);
            $coleta['erro'] = (int) $StringsXml->erro;
            if ($coleta['erro'] == 0) {

                $coleta['mensagem'] = (string) $StringsXml->mensagem;
                $coleta['numColeta'] = (string) $StringsXml->numeroColeta;
            } else {

                $coleta['mensagem'] = (string) $StringsXml->mensagem;
            }
        } catch (\Exception $e) {
            $coleta['erro'] = -3;
            $coleta['mensagem'] = "Houve um erro desconhecido, por favor tente novamente mais tarde!";
        }
        return $coleta;
    }
    // Envio da coleta a partir da cotação 
    public function enviaColetaPelaCotacao(
        $numeroCotacao, // Número da cotação gerada
        $tokenCotacao, // token da cotação gerada
        $solicitante, // nome do solicitante 
        $observacao = "" // observação || pode ser mandado vazio
    ) { {

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
                    <coletarRequest>
                        <dominio>' . $this->dominio . '</dominio>
                        <login>' . $this->usuario . '</login>
                        <senha>' . $this->senha . '</senha>
                        <cotacao>' . $numeroCotacao . '</cotacao>
                        <token>' . $tokenCotacao . '</token>
                        <solicitante>' . $solicitante . '</solicitante>
                        <observacao>' . $observacao . '</observacao>
                    </coletarRequest>
                    </soap:Body>
                </soap:Envelope>
                ',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: text/xml; charset=utf-8',
                    'SOAPAction: urn:sswinfbr.sswCotacaoColeta#coletar'
                ),
            ));


            $resposta = curl_exec($curl);


            curl_close($curl);

            try {
                $ObjetoXML = simplexml_load_string($resposta);


                $RetornoXml = htmlspecialchars_decode((string) $ObjetoXML->xpath('//return')[0], ENT_QUOTES);


                $StringsXml = simplexml_load_string($RetornoXml);
                $coleta['erro'] = (int) $StringsXml->erro;
                if ($coleta['erro'] == 0) {

                    $coleta['mensagem'] = (string) $StringsXml->mensagem;
                    $coleta['numColeta'] = (string) $StringsXml->numeroColeta;
                } else {

                    $coleta['mensagem'] = (string) $StringsXml->mensagem;
                }
            } catch (\Exception $e) {
                $coleta['erro'] = -3;
                $coleta['mensagem'] = "Houve um erro desconhecido, por favor tente novamente mais tarde!";
            }
            return $coleta;
        }
    }
}


$coleta = new Coleta();

