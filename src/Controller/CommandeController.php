<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


#[Route('/commande')]
final class CommandeController extends AbstractController
{
    #[Route(name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    
    #[Route('/new_res_comm', name: 'app_commande_new_res', methods: ['POST'])]
    public function new_res(
        Request $request,
        EntityManagerInterface $entityManager,
        RestaurantRepository $restaurantRepository,
        UserRepository $userRepository,
        MailerInterface $mailer
    ): Response {
        $data = json_decode($request->getContent(), true);
    
        if (!$data) {
            return $this->json(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }
    
        $restaurant = $restaurantRepository->find($data['restaurant_id']);
        if (!$restaurant) {
            return $this->json(['message' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        }
    
        $user = $userRepository->find($data['user_id']);
        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
    
        $commande = new Commande();
        $commande->setUtilisateur($user);
        $commande->setDateCommande(new \DateTime($data['date_commande']));
        $commande->setStatut($data['statut']);
        $commande->setRestaurant($restaurant);
    
        $entityManager->persist($commande);
        $entityManager->flush();

        // dd($user->getEmail());
    
        // ✅ FIX: Correct Sender Address
        $email = (new TemplatedEmail())
    ->from(new Address('ameni.bmansour32@gmail.com', 'Your Restaurant'))
    ->to(new Address($user->getEmail(), $user->getEmail()))
    ->subject('Confirmation de votre commande')
    ->htmlTemplate('emails/order_confirmation.html.twig') // Use the template
    ->context([
        'userEmail' => $user->getEmail(),
        'restaurantName' => $restaurant->getNom(),
        'orderStatus' => $commande->getStatut(),
        'orderDate' => $commande->getDateCommande()->format('d-m-Y H:i'),
    ]);

$mailer->send($email);

    
        try {
            $mailer->send($email);
            return $this->json(['message' => 'Commande created successfully, email sent.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Commande created, but email could not be sent: ' . $e->getMessage()], Response::HTTP_OK);
        }
    }
    

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
