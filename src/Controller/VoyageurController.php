<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/voyageur')]
class VoyageurController extends AbstractController
{
    #[Route('/dashboard', name: 'app_voyageur_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('voyageur/dashboard.html.twig', [
            'user' => $this->getUser()
        ]);
    }
} 