<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TransportController extends AbstractController
{
    #[Route('/transport', name: 'app_transport')]
    public function index(): Response
    {
        return $this->render('transport/index.html.twig', [
            'controller_name' => 'TransportController',
            "user" => $this->getUser()

        ]);
    }
}   