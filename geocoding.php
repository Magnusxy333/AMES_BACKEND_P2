
<?php

function buscarCoordenadas(string $endereco): ?array
{
    $url = 'https://nominatim.openstreetmap.org/search?' .
        http_build_query([
            'q' => $endereco,
            'format' => 'jsonv2',
            'limit' => 1,
            'countrycodes' => 'br'
        ]);

    $curl = curl_init($url);

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            'User-Agent: AMES-ProjetoAcademico/1.0 (contato: contato@seudominio.com)',
            'Accept: application/json'
        ]
    ]);

  
$resposta = curl_exec($curl);

$codigoHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$erroCurl = curl_error($curl);

curl_close($curl);

// Erro de conexão, SSL ou timeout
if ($resposta === false) {
    throw new RuntimeException(
        'Erro cURL: ' . $erroCurl
    );
}

// Erro retornado pela API
if ($codigoHttp !== 200) {
    throw new RuntimeException(
        'Erro HTTP: ' . $codigoHttp
    );
}


    $dados = json_decode($resposta, true);

    if (!is_array($dados)) {
        throw new RuntimeException(
            'Resposta inválida do serviço de localização.'
        );
    }

    if (empty($dados)) {
        return null;
    }

    if (!isset($dados[0]['lat'], $dados[0]['lon'])) {
        return null;
    }

    return [
        'latitude' => round((float) $dados[0]['lat'], 6),
        'longitude' => round((float) $dados[0]['lon'], 6)
    ];
}
