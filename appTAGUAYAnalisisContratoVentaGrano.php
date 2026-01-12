<?php

header("Content-Type: application/json"); // Fuerza salida JSON
header("Access-Control-Allow-Origin: *"); // Permite acceso desde cualquier origen (opcional)

// 1️ OBTENER EL TOKEN DE ACCESO COMO TEXTO PLANO

$tokenUrl = "https://api.finneg.com/api/oauth/token?grant_type=client_credentials&client_id=40901e9dcf89fc6da790af0e3c2a3cd2&client_secret=86514f5236398dbd16f0ded48d1b9b12";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json"

]);

$response = curl_exec($ch);

curl_close($ch);

$accessToken = trim($response); // Guardamos el token como texto directo

if (empty($accessToken)) {

    die(json_encode(["error" => "Error obteniendo el token de acceso", "detalle" => $response]));

}

// 2️ REALIZAR LA SOLICITUD GET CON EL TOKEN Y PARÁMETROS

$dataUrl = "https://api.finneg.com/api/reports/TAGUAYAnalisisContratoVentaGrano?"

    . "ACCESS_TOKEN=" . urlencode($accessToken)
. "&PARAMWEBREPORT_FechaDesde=2022-01-01"
. "&PARAMWEBREPORT_FechaHasta=2080-01-01&PARAMWEBREPORT_Organizacion="
. "&PARAMWEBREPORT_Corredor="
. "&PARAMWEBREPORT_Producto="
. "&PARAMWEBREPORT_TipoContrato="
. "&PARAMWEBREPORT_CircuitoContable="
. "&PARAMWEBREPORT_FechaEntregaMin=2022-01-01"
. "&PARAMWEBREPORT_FechaEntregaMax=2080-01-01&PARAMWEBREPORT_Dimension="
. "&PARAMWEBREPORT_Empresa="
. "&PARAMWEBREPORT_ContratoNroInterno="
. "&PARAMWEBREPORT_Campana=&PARAMWEBREPORT_Moneda="
. "&PARAMWEBREPORT_ContratoCorredor="
. "&PARAM_TransaccionSubtipo=&PARAMWEBREPORT_Estado=";


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $dataUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [

    "Accept: application/json"

]);



$dataResponse = curl_exec($ch);

curl_close($ch);



$data = json_decode($dataResponse, true);

// 3️ Devolver JSON

echo json_encode($data, JSON_PRETTY_PRINT);



?>
