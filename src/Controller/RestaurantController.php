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
    // #[Route(name: 'app_restaurant_index', methods: ['GET'])]
    // public function index(RestaurantRepository $restaurantRepository, Request $request): Response
    // {
    //     $isAdmin = $request->query->has('a');
        
    //     return $this->render('restaurant/index.html.twig', [
    //         'restaurants' => $restaurantRepository->findAll(),
    //         'isAdmin' => $isAdmin,
    //     ]);
    // }

    #[Route(name: 'app_restaurant_index', methods: ['GET'])]
    public function index(RestaurantRepository $restaurantRepository, Request $request): Response
    {
        $user = $this->getUser();

        // Get search parameters
        $nom = $request->query->get('nom', '');
        $prix = $request->query->get('prix', '');
        $prix = is_numeric($prix) ? (float)$prix : null;

        if ($this->isGranted('ROLE_ADMIN')) {
            // Admin can see all restaurants
            $restaurants = $restaurantRepository->searchRestaurants($nom, $prix);
        } elseif ($this->isGranted('ROLE_RESTAURANT')) {
            // Restaurant owners see only their restaurants
            $restaurants = $restaurantRepository->findBy(['user' => $user]);
        } else {
            // Public users see all restaurants
            $restaurants = $restaurantRepository->searchRestaurants($nom, $prix);
        }

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants
        ]);
    }
    

    #[Route('/new', name: 'app_restaurant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser(); // Get the currently logged-in user

        if (!$user || !in_array('ROLE_RESTAURANT', $user->getRoles(), true)) {
            $this->addFlash('error', 'Vous devez être un propriétaire de restaurant pour ajouter un restaurant.');
            return $this->redirectToRoute('app_restaurant_index');
        }

        $restaurant = new Restaurant();
        $restaurant->setUser($user); // Assign the restaurant to the logged-in user

        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle main image upload
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
                    $this->addFlash('error', 'Main image upload failed.');
                }
            }

            // Handle optional image1 upload
            $imageFile1 = $form->get('imageFile1')->getData();
            if ($imageFile1) {
                $originalFilename = pathinfo($imageFile1->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename1 = $safeFilename . '-' . uniqid() . '.' . $imageFile1->guessExtension();

                try {
                    $imageFile1->move(
                        $this->getParameter('restaurant_images_directory'),
                        $newFilename1
                    );
                    $restaurant->setImage1($newFilename1);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Additional Image 1 upload failed.');
                }
            }

            // Handle optional image2 upload
            $imageFile2 = $form->get('imageFile2')->getData();
            if ($imageFile2) {
                $originalFilename = pathinfo($imageFile2->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename2 = $safeFilename . '-' . uniqid() . '.' . $imageFile2->guessExtension();

                try {
                    $imageFile2->move(
                        $this->getParameter('restaurant_images_directory'),
                        $newFilename2
                    );
                    $restaurant->setImage2($newFilename2);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Additional Image 2 upload failed.');
                }
            }

            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Restaurant ajouté avec succès!');
            return $this->redirectToRoute('app_restaurant_index');
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
        $user = $this->getUser();

        // Ensure only the owner or an admin can edit
        if (!$this->isGranted('ROLE_ADMIN') && $restaurant->getUser() !== $user) {
            $this->addFlash('error', 'Vous n\'avez pas la permission de modifier ce restaurant.');
            return $this->redirectToRoute('app_restaurant_index');
        }

        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload during editing
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

            return $this->redirectToRoute('app_restaurant_index');
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_restaurant_delete', methods: ['POST'])]
    public function delete(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Ensure only the owner or an admin can delete
        if (!$this->isGranted('ROLE_ADMIN') && $restaurant->getUser() !== $user) {
            $this->addFlash('error', 'Vous n\'avez pas la permission de supprimer ce restaurant.');
            return $this->redirectToRoute('app_restaurant_index');
        }

        if ($this->isCsrfTokenValid('delete' . $restaurant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($restaurant);
            $entityManager->flush();
            $this->addFlash('success', 'Restaurant supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_restaurant_index');
    }

}

