<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        //cette méthode retourne la dernière erreur d'authentification survenue
        //(par exemple, un mot de passe incorrect, un utilisateur inexistant, etc.).
        //si aucune erreur n'est survenue, elle retourne null.
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        //récupère le dernier nom d'utilisateur saisi pour le pré-remplir dans le formulaire

        return $this->render('/public/login.html.twig', [
            'lastUsername' => $lastUsername, //passe le dernier nom d'utilisateur à la template
            'error' => $error,
        ]);
    }

}