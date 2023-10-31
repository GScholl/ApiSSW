<?php 



class Api {

public $dominio = "REU";

public $usuario = "31650257";

public $senha = "31650257";
public $cnpj_edi = "31650257000112";

public function getToken()
{
  

    $data = array(
        "domain" => $this->dominio,
        "username" => $this->usuario,
        "password" => $this->senha,
        "cnpj_edi" => $this->cnpj_edi
    );

    $api_url = 'https://ssw.inf.br/api/generateToken';

    $headers = array(
        'Content-Type: application/json'
    );

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'Erro ao fazer a solicitação: ' . curl_error($ch);
    } else {
        $response = json_decode($response, true);
       
            return $response['token'];
       
    }

    curl_close($ch);
}

}