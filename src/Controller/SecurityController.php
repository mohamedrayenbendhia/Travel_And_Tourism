<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $user = $this->getUser();
        
        if ($user) {
            $this->addFlash('success', 'You are already logged in.');
            return $this->redirectToRoute('app_homepage');
        }

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        //$lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            $errorMsg = $error->getMessage();
            if ($errorMsg === "Invalid credentials.") {
                $this->addFlash('error', 'Invalid credentials. Please try again.');
            } else {
                $this->addFlash('error', 'An error occurred. Please try again.');
            }
        }

        return $this->render('Security/login.html.twig', [
            'error' => $error
        ]);

    }
    #[Route(path: '/logout', name: 'app_logout')]

    public function logout(): RedirectResponse
    {
        
        return $this->redirectToRoute('app_login');
    }

    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
        ]);
    }

    #[Route('/rent', name: 'app_rentpage')]
    public function buy(): Response
    {
        return $this->render('page/rent.html.twig', [
        ]);
    }

    #[Route('/blog', name: 'app_blogpage')]
    public function blog(): Response
    {
        return $this->render('page/blog.html.twig', [
        ]);
    }

    #[Route('/contact', name: 'app_contactpage')]
    public function contact(): Response
    {
        return $this->render('page/contact.html.twig', [
        ]);
    }

    #[Route('/aboutus', name: 'app_aboutuspage')]
    public function aboutus(): Response
    {
        return $this->render('page/aboutus.html.twig', [
        ]);
    }

}
