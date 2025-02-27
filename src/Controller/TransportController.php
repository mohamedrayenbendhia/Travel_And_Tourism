<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Form\TransportType;
use App\Repository\TransportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/transport')]
final class TransportController extends AbstractController
{
    #[Route(name: 'app_transport_index', methods: ['GET'])]
    public function index(TransportRepository $transportRepository, Request $request): Response
    {
        $isAdmin = $request->query->has('a');

        return $this->render('transport/index.html.twig', [
            'transports' => $transportRepository->findAll(),
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/new', name: 'app_transport_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $transport = new Transport();
    $form = $this->createForm(TransportType::class, $transport);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('transport_images_directory'), // Assure-toi que ce paramètre est bien défini dans `services.yaml`
                    $newFilename
                );
                $transport->setImage($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Image upload failed.');
            }
        }

        $entityManager->persist($transport);
        $entityManager->flush();

        return $this->redirectToRoute('app_transport_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('transport/new.html.twig', [
        'transport' => $transport,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_transport_show', methods: ['GET'])]
    public function show(Transport $transport): Response
    {
        return $this->render('transport/show.html.twig', [
            'transport' => $transport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transport $transport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transport/edit.html.twig', [
            'transport' => $transport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transport_delete', methods: ['POST'])]
    public function delete(Request $request, Transport $transport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transport->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($transport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transport_index', [], Response::HTTP_SEE_OTHER);
    }
}
