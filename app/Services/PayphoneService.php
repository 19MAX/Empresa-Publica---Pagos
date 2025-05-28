<?php
namespace App\Services;

class PayphoneService
{
    private $token;
    private $store;

    public function __construct()
    {
        $this->token = getenv('PAYPHONE_API_TOKEN');
        $this->store = getenv('PAYPHONE_API_STORE');
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getStore()
    {
        return $this->store;
    }


}

