<?php

$cURLConnection = curl_init();

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'authorization: Bearer 72402c54-6bd3-4895-a6b4-adfded0c11dc',
    'seller_id: 6eb2412c-165a-41cd-b1d9-76c575d70a28'
));
curl_setopt($cURLConnection, CURLOPT_URL, 'https://api-homologacao.getnet.com.br/v1/plans?page=1&limit=10');
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$phoneList = curl_exec($cURLConnection);
curl_close($cURLConnection);

$jsonArrayResponse - json_decode($phoneList);

?>