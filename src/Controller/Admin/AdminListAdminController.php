<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminListAdminController extends AbstractController
{

    #[Route('/admin/admins/list', name: 'admin_list')]
    public function listAdmin(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('admin/admin/list_admins.html.twig', [
            'users' => $users,
        ]);
    }

}