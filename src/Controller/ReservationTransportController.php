<?php

namespace App\Controller;

use App\Entity\ReservationTransport;
use App\Form\ReservationTransportType;
use App\Repository\ReservationTransportRepository;
use App\Repository\TransportRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation/transport')]
final class ReservationTransportController extends AbstractController
{
    #[Route(name: 'app_reservation_transport_index', methods: ['GET'])]
    public function index(ReservationTransportRepository $reservationTransportRepository): Response
    {
        return $this->render('reservation_transport/index.html.twig', [
            'reservation_transports' => $reservationTransportRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_transport_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservationTransport = new ReservationTransport();
        $form = $this->createForm(ReservationTransportType::class, $reservationTransport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transport = $reservationTransport->getTransportId();
            
            // if (!$transport->isDisponibilte()) {
            //     $this->addFlash('error', 'The selected transport is not available.');
            //     return $this->redirectToRoute('app_reservation_transport_new');
            // }

            $entityManager->persist($reservationTransport);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_transport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation_transport/new.html.twig', [
            'reservation_transport' => $reservationTransport,
            'form' => $form,
        ]);
    }

    #[Route('/new_res', name: 'app_reservation_transport_new_res', methods: ['POST'])]
    public function new_res(Request $request, EntityManagerInterface $entityManager, TransportRepository $transportRepository, 
        UserRepository $userRepository
    ): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $transport = $transportRepository->find($data['transport_id']);
        if (!$transport) {
            return $this->json(['message' => 'Transport not found'], Response::HTTP_NOT_FOUND);
        }

        $reservation = new ReservationTransport();
        $reservation->setUserId($userRepository->find($data['user_id']));
        $reservation->setTransportId(transport_id: $transport);
        $reservation->setStartDate(new \DateTime($data['start_date']));
        $reservation->setEndDate(new \DateTime($data['end_date']));
        $reservation->setStatut($data['statut']);

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->json(['message' => 'Reservation created successfully'], Response::HTTP_OK);
    }


    

    #[Route('/{id}', name: 'app_reservation_transport_show', methods: ['GET'])]
    public function show(ReservationTransport $reservationTransport): Response
    {
        return $this->render('reservation_transport/show.html.twig', [
            'reservation_transport' => $reservationTransport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_transport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReservationTransport $reservationTransport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationTransportType::class, $reservationTransport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_transport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation_transport/edit.html.twig', [
            'reservation_transport' => $reservationTransport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_transport_delete', methods: ['POST'])]
    public function delete(Request $request, ReservationTransport $reservationTransport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservationTransport->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservationTransport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_transport_index', [], Response::HTTP_SEE_OTHER);
    }
}
