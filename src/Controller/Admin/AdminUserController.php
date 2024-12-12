<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;

class AdminUserController
{
    #[Route('/admin/logout', 'logout')]
    public function logout()
    {
        // cette route est utilisée par symfony
        // dans le security.yaml
        // pour gérer la deconnexion
    }


}