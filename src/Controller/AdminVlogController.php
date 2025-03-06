<?php
// src/Controller/AdminVlogController.php
namespace App\Controller;

use App\Entity\Vlog;
use App\Repository\VlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminVlogController extends AbstractController
{
    #[Route('/admin/vlogs', name: 'admin_vlogs')]
    public function index(VlogRepository $vlogRepository): Response
    {
        // Récupérer tous les vlogs depuis la base de données
        $vlogs = $vlogRepository->findAll();

        // Passer les vlogs à la vue Twig
        return $this->render('admin/vlogsadmin/index.html.twig', [
            'vlogs' => $vlogs,
        ]);
    }
}
