<?php

require_once "Api.php";

class Rastreamento extends Api
{

    public function RastreamentoChaveDanfe(
        $chaveNFE, // Chave Danfe para o rastreamento
        $token // token gerado na classe api e metodo getToken
    ) {



        $dados =  array(
            "chave_nfe" => $chaveNFE,
            "token"   => $token
        );
        $api_url = 'https://ssw.inf.br/api/trackingdanfe';

        $headers = array(
            'Content-Type: application/json'
        );

        return json_decode($this->enviaRequest($api_url, $headers, $dados));
    }
    public function RastreamentoPFDestinatario($cpf)
    {
        $dados = array(
            "dominio" => $this->dominio,
            "usuario" => $this->usuario,
            "senha" => $this->senha,
            "cpf" => $cpf
        );


        $api_url = 'https://ssw.inf.br/api/trackingpf';

        $headers = array(
            'Content-Type: application/json'
        );

        return json_decode($this->enviaRequest($api_url, $headers, $dados));
    }
    public function RastreamentoDestinatario(
        $cpfCnpj, // CPF ou CNPJ do Destinatario
        $nr, // NF, Pedido ou Coleta 
        $token, // Token gerado pela classe Api e o metodo getToken
        $senha = null // Senha || Não Obrigatória
    ) {
        if (strlen($cpfCnpj) == 11) {
            $dados["cpf"] = $cpfCnpj;
        } else {
            $dados["cnpj"] = $cpfCnpj;
        }
        $valor = trim($nr);
        if (is_numeric($valor)) {
            $dados['nro_nf'] = intval($valor);
        } else {
            $dados['chave_nf'] = $valor;
        }
        if ($senha != null) {

            $dados['senha'] = $senha;
        }

        $dados["token"]  = $token;
        $api_url = 'https://ssw.inf.br/api/trackingdest';


        $headers = array(
            'Content-Type: application/json'
        );

        return json_decode($this->enviaRequest($api_url, $headers, $dados));
    }


    public function RastreamentoPagador(
        $cpfCnpj, // CPF ou CNPJ do Pagador
        $nr, // NF, Pedido ou Coleta 
        $token, // Token gerado pela classe Api e o metodo getToken
        $senha = null // Senha || Não Obrigatória
    ) {
        if (strlen($cpfCnpj) == 11) {
            $dados["cpf"] = $cpfCnpj;
        } else {
            $dados["cnpj"] = $cpfCnpj;
        }
        $valor = trim($nr);
        if (is_numeric($valor)) {
            $dados['nro_nf'] = intval($valor);
        } else {
            $dados['chave_nf'] = $valor;
        }
        if ($senha != null) {

            $dados['senha'] = $senha;
        }

        $dados["token"]  = $token;
        $api_url = 'https://ssw.inf.br/api/trackingpag';


        $headers = array(
            'Content-Type: application/json'
        );

        return json_decode($this->enviaRequest($api_url, $headers, $dados));
    }


    public function RastreamentoRemetente(
        $cpfCnpj, // CPF ou CNPJ do remetente
        $nr, // NF, Pedido ou Coleta 
        $token, // Token gerado pela classe Api e o metodo getToken
        $senha = null // Senha || Não Obrigatória
    ) {
        if (strlen($cpfCnpj) == 11) {
            $dados["cpf"] = $cpfCnpj;
        } else {
            $dados["cnpj"] = $cpfCnpj;
        }
        $valor = trim($nr);
        if (is_numeric($valor)) {
            $dados['nro_nf'] = intval($valor);
        } else {
            $dados['chave_nf'] = $valor;
        }
        if ($senha != null) {

            $dados['senha'] = $senha;
        }

        $dados["token"]  = $token;
        $api_url = 'https://ssw.inf.br/api/tracking';


        $headers = array(
            'Content-Type: application/json'
        );

        return json_decode($this->enviaRequest($api_url, $headers, $dados));
    }
    public function enviaRequest($api_url, $headers, $dados)
    {
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (!$response) {
            return curl_error($ch);
        } else {

            return $response;
        }
    }
}


