<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/restaurant')]
final class RestaurantController extends AbstractController
{
    #[Route(name: 'app_restaurant_index', methods: ['GET'])]
    public function index(RestaurantRepository $restaurantRepository, Request $request): Response
    {
        $isAdmin = $request->query->has('a');
        
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantRepository->findAll(),
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/new', name: 'app_restaurant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('restaurant_images_directory'),
                        $newFilename
                    );
                    $restaurant->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed.');
                }
            }

            $entityManager->persist($restaurant);
            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_restaurant_show', methods: ['GET'])]
    public function show(Restaurant $restaurant, Request $request): Response
    {
        $isAdmin = $request->query->has('a');

        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_restaurant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image lors de l'édition
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('restaurant_images_directory'),
                        $newFilename
                    );
                    $restaurant->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed.');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_restaurant_delete', methods: ['POST'])]
    public function delete(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $restaurant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
    }
}

