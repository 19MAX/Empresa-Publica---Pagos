<?php
function grantAccess(int $rol = null)
{
    $userSessionRol = session('rol');
    if (isset($userSessionRol)) {
        $userSessionRol = (int) $userSessionRol;
        if (isset($rol)) {
            if ($rol !== $userSessionRol) {
                session()->destroy();
                return redirect()->to(base_url('/login'));
            }
            return true;
        } else {
            switch ($userSessionRol) {
                case RolesOptions::AdminPrincipal:
                    return redirect()->to(base_url("admin/dashboard"));
                case RolesOptions::AdministradorDePagos:
                    return redirect()->to(base_url("punto/pago"));
                // case RolesOptions::AdministradorDeEventos:
                //     return redirect()->to(base_url("eventos/dashboard"));
                // case RolesOptions::UsuarioPublico:
                //     return redirect()->to(base_url("public/dashboard"));
                default:
                    session()->destroy();
                    return redirect()->to(base_url("/login"));
            }
        }
    }
    return false;
}
function checkActiveModule($modulo, $value)
{
    return ($modulo == $value) ? true : false;
}