<?php

namespace App\Services;

use CodeIgniter\HTTP\CURLRequest;
use InvalidArgumentException;

class PayphoneConfirmService
{
    private $apiUrl = 'https://pay.payphonetodoesposible.com/api/button/V2/Confirm';
    private $token;
    private $client;

    public function __construct()
    {
        // Obtener el token desde las variables de entorno
        $this->token = getenv('PAYPHONE_API_TOKEN');
        if (!$this->token) {
            throw new InvalidArgumentException('API token is missing.');
        }

        // Cargar el servicio HTTP de CodeIgniter
        $this->client = \Config\Services::curlrequest();
    }

    public function confirmTransaction($id, $clientTransactionId)
    {
        // Validar los parámetros
        if (empty($id) || empty($clientTransactionId)) {
            return ['success' => false, 'error' => 'Transaction ID and Client Transaction ID are required.'];
        }

        // Configuración del request
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type'  => 'application/json'
            ],
            'json' => [
                'id'        => $id,
                'clientTxId' => $clientTransactionId
            ],
            'http_errors' => false, // Para manejar los errores manualmente
        ];

        // Realizar la solicitud
        $response = $this->client->post($this->apiUrl, $options);
        $httpStatusCode = $response->getStatusCode();
        $responseBody = $response->getBody();

        // Decodificar la respuesta JSON
        $result = json_decode($responseBody, true);

        // Verificar si hubo un error en la solicitud
        if ($httpStatusCode !== 200) {
            return ['success' => false, 'error' => 'API request failed with status code ' . $httpStatusCode, 'data' => $result];
        }

        if (!isset($result['transactionStatus'])) {
            return ['success' => false, 'error' => 'Invalid response from API', 'data' => $result];
        }

        // Retornar el resultado según el estado de la transacción
        return ['success' => $result['transactionStatus'] === 'Approved', 'data' => $result];
    }
}
