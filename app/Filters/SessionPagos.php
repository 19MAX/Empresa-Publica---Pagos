<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use RolesOptions;
class SessionPagos implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('rol')!=RolesOptions::AdministradorDePagos) {
            return redirect()->to(base_url('/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
