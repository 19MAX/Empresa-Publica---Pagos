<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use RolesOptions;
class SessionUserEventos implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('rol')!=RolesOptions::UsuarioEventos) {
            return redirect()->to(base_url('/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
