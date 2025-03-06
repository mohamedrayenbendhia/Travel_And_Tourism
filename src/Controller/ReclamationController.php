<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/new', name: 'reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();

        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour soumettre une réclamation');
            return $this->redirectToRoute('app_login');
        }

        $reclamation->setAuteur($user);

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                try {
                    $photoFile->move($this->getParameter('uploads_directory'), $newFilename);
                    $reclamation->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            // Gestion de l'upload du document PDF
            /** @var UploadedFile $documentFile */
            $documentFile = $form->get('document')->getData();
            if ($documentFile) {
                $newFilename = uniqid() . '.' . $documentFile->guessExtension();
                try {
                    $documentFile->move($this->getParameter('uploads_directory'), $newFilename);
                    $reclamation->setDocument($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du document.');
                }
            }

            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_liste');
        }

        return $this->render('reclamation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/liste', name: 'reclamation_liste')]
    public function liste(ReclamationRepository $repo): Response
    {
        $reclamations = $repo->findBy(['auteur' => $this->getUser()]);
        return $this->render('reclamation/list.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/cible', name: 'reclamation_liste_cible')]
    public function listeCible(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos réclamations.');
        }

        $reclamations = $entityManager->getRepository(Reclamation::class)->findBy(['cible' => $user]);

        return $this->render('reclamation/liste_cible.html.twig', [
            'reclamations' => $reclamations,
            'cible' => $user,
        ]);
    }

    #[Route('/changer_statut/{id}', name: 'reclamation_changer_statut')]
    public function changerStatut(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || $reclamation->getCible() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à changer le statut de cette réclamation.');
        }

        $statut = $request->request->get('statut');
        if ($statut) {
            $reclamation->setStatut($statut);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reclamation_liste_cible');
    }

    #[Route('/{id}/repondre', name: 'reclamation_repondre')]
    public function repondre(Reclamation $reclamation, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->getUser() !== $reclamation->getCible()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $reclamation->setStatut($request->request->get('statut'));
            $em->flush();

            $this->addFlash('success', 'Réponse enregistrée avec succès !');
            return $this->redirectToRoute('reclamation_liste');
        }

        return $this->render('reclamation/repondre.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
}
