<?php
// src/Controller/VlogController.php
namespace App\Controller;

use App\Entity\Vlog;
use App\Form\VlogType;
use App\Repository\VlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\SecurityBundle\Attribute\IsGranted;

#[Route('/vlog')]
class VlogController extends AbstractController
{    
    // Création d'un nouveau vlog
    #[Route('/new', name: 'vlog_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_VOYAGEUR')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {     
        $vlog = new Vlog();
        $form = $this->createForm(VlogType::class, $vlog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('image')->getData();
            /** @var UploadedFile|null $videoFile */
            $videoFile = $form->get('video')->getData();

            // Gestion de l'upload de l'image
            if ($imageFile) {
                $imageFileName = uniqid('', true) . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('vlog_images_directory'), $imageFileName);
                    $vlog->setImage($imageFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            // Gestion de l'upload de la vidéo
            if ($videoFile) {
                $videoFileName = uniqid('', true) . '.' . $videoFile->guessExtension();
                try {
                    $videoFile->move($this->getParameter('vlog_videos_directory'), $videoFileName);
                    $vlog->setVideo($videoFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la vidéo.');
                }
            }

            $vlog->setAuthor($this->getUser());
            $entityManager->persist($vlog);
            $entityManager->flush();

            return $this->redirectToRoute('vlog_index');
        }

        return $this->render('vlog/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Afficher les vlogs de l'utilisateur connecté
    #[Route('/nosvlogs', name: 'nos_vlog', methods: ['GET'])]
    public function nosvlog(VlogRepository $vlogRepository): Response
    {
        $user = $this->getUser();  // Utilisateur actuellement connecté
        $vlogs = $vlogRepository->findBy(['author' => $user]);

        return $this->render('vlog/nosvlogs.html.twig', [
            'vlogs' => $vlogs,
        ]);
    }

    // Liste de tous les vlogs
    #[Route('/', name: 'vlog_index', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(VlogRepository $vlogRepository): Response
    {
        $vlogs = $vlogRepository->findAll();

        return $this->render('vlog/list.html.twig', [
            'vlogs' => $vlogs,
        ]);
    }
    
    // Afficher un vlog individuel
    #[Route('/{id}', name: 'vlog_show', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(Vlog $vlog): Response
    {         
        return $this->render('vlog/show.html.twig', [
            'vlog' => $vlog,
        ]);
    }

    // Modifier un vlog
    #[Route('/{id}/edit', name: 'vlog_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_VOYAGEUR')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Vlog $vlog, EntityManagerInterface $entityManager): Response
    {
        // Vérification que l'utilisateur est l'auteur du vlog
        if ($vlog->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à modifier ce vlog.");
        }

        $form = $this->createForm(VlogType::class, $vlog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('image')->getData();
            /** @var UploadedFile|null $videoFile */
            $videoFile = $form->get('video')->getData();

            // Gestion de l'upload de l'image
            if ($imageFile) {
                $imageFileName = uniqid('', true) . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('vlog_images_directory'), $imageFileName);
                    $vlog->setImage($imageFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            // Gestion de l'upload de la vidéo
            if ($videoFile) {
                $videoFileName = uniqid('', true) . '.' . $videoFile->guessExtension();
                try {
                    $videoFile->move($this->getParameter('vlog_videos_directory'), $videoFileName);
                    $vlog->setVideo($videoFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la vidéo.');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('vlog_index');
        }

        return $this->render('vlog/edit.html.twig', [
            'form' => $form->createView(),
            'vlog' => $vlog,
        ]);
    }

    // Supprimer un vlog
    #[Route('/{id}', name: 'vlog_delete', methods: ['POST'])]
    #[IsGranted('ROLE_VOYAGEUR')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Vlog $vlog, EntityManagerInterface $entityManager): Response
    {
        // Vérification que l'utilisateur est l'auteur du vlog
        if ($vlog->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à supprimer ce vlog.");
        }

        if ($this->isCsrfTokenValid('delete' . $vlog->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vlog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('vlog_index');
    }
}
