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
$response = json_decode($response);
curl_close($request);

if (
    $response == 0 ||
    $response->detail == 'Invalid token header. No credentials provided.'
) {
    $kilowattCost = 0;
} else {
    $Date = new DateTime("NOW");
    while ($Date->format("Y-m-01") != $response[0]->period) {
        $Date->modify("-1 month");
    }
    foreach ($response[0]->groups as $group) {
        if ($group->name == $config->CONSUMPTION_TYPE)
            $kilowattCost = $group->electricity_price / 100;
    }
}

$insertQuery = "
    UPDATE greenit_config 
    SET 
    KILOWATT_COST='" . $kilowattCost . "'
    WHERE ID='1';
";
mysql2_query_secure($insertQuery, $_SESSION['OCS']["writeServer"]);

?>