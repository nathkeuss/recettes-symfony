<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    #[Route('/admin/logout', 'logout', methods: ['GET'])]
    public function logout()
    {
        // cette route est utilisée par symfony
        // dans le security.yaml
        // pour gérer la deconnexion
    }

    #[Route('admin/create/user', 'admin_create_user', methods: ['GET', 'POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        /*dd('cc');*/
        $user = new User();

        $adminUserForm = $this->createForm(AdminUserType::class, $user);

        $adminUserForm->handleRequest($request);

        if ($adminUserForm->isSubmitted()) {
            //récupération du mdp depuis le formulaire
            $password = $adminUserForm->get('password')->getData();

            //hashage du mdp
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            //sauvegarde de l'user en bdd
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur crée');
        }

        $adminUserFormView = $adminUserForm->createView();

        return $this->render('admin/user/create_user.html.twig', [
            'adminUserFormView' => $adminUserFormView,
        ]);
    }

    #[Route('admin/user/{id}/delete', 'admin_delete_user', methods: ['GET'])]
    public function deleteUser(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {

        //récupère l'user qui correspond à l'id dans l'url
        $user = $userRepository->find($id);

        //prépare la suppression
        $entityManager->remove($user);
        //supprime en bdd
        $entityManager->flush();
        //ajout message confirmation
        $this->addFlash('succes', 'Utilisateur supprimé de ce monde');
        //redirige vers la liste des recettes
        return $this->redirectToRoute('admin_list');

    }


}