<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Form\TransportType;
use App\Repository\ReservationTransportRepository;
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
    public function index(TransportRepository $transportRepository, Request $request, ReservationTransportRepository $reservationTransportRepository): Response
    {
        $user = $this->getUser();
        $type = $request->query->get('type', '');
    
        if ($this->isGranted('ROLE_ADMIN')) {
            // Admins can see all transports
            $transports = $transportRepository->searchTransports($type);
        } elseif ($this->isGranted('ROLE_TRANSPORTEUR')) {
            // Transport owners only see their own transports
            $transports = $transportRepository->findBy(['user' => $user]);
        } else {
            // Public users can see all transports but cannot manage them
            $transports = $transportRepository->searchTransports($type);
        }
    
        // Check availability and set a flag for each transport
        foreach ($transports as $transport) {
            $transport->isAvailable = !$reservationTransportRepository->getTransportAvailabilityForCurrentDate($transport->getId());
        }
    
        return $this->render('transport/index.html.twig', [
            'transports' => $transports
        ]);
    }
    

    #[Route('/new', name: 'app_transport_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $user = $this->getUser(); // Get the currently logged-in user

    if (!$user || !in_array('ROLE_TRANSPORTEUR', $user->getRoles(), true)) {
        $this->addFlash('error', 'Vous devez être un transporteur pour ajouter un transport.');
        return $this->redirectToRoute('app_transport_index');
    }

    $transport = new Transport();
    $transport->setUser($user); // Assign the transport to the logged-in user

    $form = $this->createForm(TransportType::class, $transport);
    $form->handleRequest($request);

    // if ($form->isSubmitted()){
    //     // dd($form->isValid());
    //     //dump the errors
    //     dd($form->getErrors(true));
    // }

    if ($form->isSubmitted() && $form->isValid()) {

        // dd($form->getData());
        // Handle main image upload
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('transport_images_directory'), // Ensure this is defined in `services.yaml`
                    $newFilename
                );
                $transport->setImage($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Image upload failed.');
            }
        }

        $entityManager->persist($transport);
        $entityManager->flush();

        $this->addFlash('success', 'Transport ajouté avec succès!');
        return $this->redirectToRoute('app_transport_index');
    }

    return $this->render('transport/new.html.twig', [
        'transport' => $transport,
        'form' => $form,
    ]);
}


    // #[Route('/{id}', name: 'app_transport_show', methods: ['GET'])]
    // public function show(Transport $transport, Request $request): Response
    // {
    //     $isAdmin = $request->query->has('a');

    //     return $this->render('transport/show.html.twig', [
    //         'transport' => $transport,
    //         'isAdmin' => $isAdmin,

    //     ]);
    // }

    #[Route('/{id}', name: 'app_transport_show', methods: ['GET'])]
    public function show(Transport $transport, ReservationTransportRepository $reservationTransportRepository): Response
    {
        $reservations = $reservationTransportRepository->getTransportAvailability($transport->getId());

        return $this->render('transport/show.html.twig', [
            'transport' => $transport,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transport_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Transport $transport, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $user = $this->getUser();

    // Restrict access: only the transport owner or an admin can edit
    if (!$this->isGranted('ROLE_ADMIN') && $transport->getUser() !== $user) {
        $this->addFlash('error', 'Vous n\'avez pas la permission de modifier ce transport.');
        return $this->redirectToRoute('app_transport_index');
    }

    $form = $this->createForm(TransportType::class, $transport);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_transport_index');
    }

    return $this->render('transport/edit.html.twig', [
        'transport' => $transport,
        'form' => $form,
    ]);
}



    #[Route('/{id}', name: 'app_transport_delete', methods: ['POST'])]
    public function delete(Request $request, Transport $transport, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    
        // Ensure only the owner or an admin can delete
        if (!$this->isGranted('ROLE_ADMIN') && $transport->getUser() !== $user) {
            $this->addFlash('error', 'Vous n\'avez pas la permission de supprimer ce transport.');
            return $this->redirectToRoute('app_transport_index');
        }
    
        if ($this->isCsrfTokenValid('delete' . $transport->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($transport);
            $entityManager->flush();
            $this->addFlash('success', 'Transport supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }
    
        return $this->redirectToRoute('app_transport_index');
    }
    
}
