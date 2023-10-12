<?php

$url = 'http://172.18.25.171:8080/data/periods/';

$request = curl_init($url);
curl_setopt(
    $request,
    CURLOPT_RETURNTRANSFER,
    true
);
if (is_defined($config->API_KEY))
    curl_setopt(
        $request,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: Token ' . $config->API_KEY
        )
    );
$response = curl_exec($request);
$response = curl_getinfo($request);
curl_close($request);

if (curl_getinfo($request, CURLINFO_HTTP_CODE) == 200)
    msg_success($l->g(101001));
else if (curl_getinfo($request, CURLINFO_HTTP_CODE) == 401)
    msg_error($l->g(101002));

?>