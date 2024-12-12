<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{

    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard()
    {
        return $this->render('admin/dashboard.html.twig');
    }

}