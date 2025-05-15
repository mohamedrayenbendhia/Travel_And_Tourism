<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/hote')]
class HoteController extends AbstractController
{
    #[Route('/dashboard', name: 'app_hote_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('hote/dashboard.html.twig', [
            'user' => $this->getUser()
        ]);
    }
} 