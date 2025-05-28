<?php

namespace App\Services;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use Config\Services;

class ApiPrivadaService
{
    public function getDataUser($id)
    {
        try {
            $client = Services::curlrequest();

            if (strlen($id) == 13) {
                $url = getenv('URL_PERSONAS') . "api/ruc/" . $id;
            } else {
                $url = getenv('URL_PERSONAS') . "api/ci/" . $id;
            }

            $token = getenv('API_PERSONAS');

            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'User-agent' => 'CodeIgniter'
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), true);
            } else {
                log_message('warning', 'Solicitud fallida: ' . $response->getStatusCode(), ['id' => $id]);
                return null;
            }
        } catch (\Exception $e) {
            log_message('error', 'Excepción en solicitud: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString(), ['id' => $id]);
            return null;
        }
    }

    public static function setDataUserCi(array $persona)
    {
        // Filtrar los datos no nulos
        $dataToSend = array_filter($persona, function ($value) {
            return !is_null($value) && $value !== '';
        });

        try {
            // URL y token de la API
            $url = getenv('URL_PERSONAS') . "api/ci";
            $token = getenv('API_PERSONAS');

            // Hacer la solicitud POST
            $response = Services::curlrequest()->post($url, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'json' => $dataToSend
            ]);

            // Verificar el éxito de la solicitud
            if ($response->getStatusCode() === 201) {
                return json_decode($response->getBody(), true);
            } else {
                log_message('warning', "Error en la API de registro: " . $response->getBody(), ['status' => $response->getStatusCode()]);
                return null;
            }
        } catch (\Exception $e) {
            log_message('error', $e->getMessage(), ['error' => $e->getMessage()]);
            return null;
        }
    }
}
