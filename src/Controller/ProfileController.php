<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        
        // Vérifier que l'utilisateur est bien connecté et est une instance de User
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('profileImage')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );

                    // Update the user's profile image path
                    $user->setProfileImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                    return $this->redirectToRoute('app_profile_edit');
                }
            }

            // Handle password change if provided
            if ($newPassword = $form->get('plainPassword')->getData()) {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            }

            try {
                $entityManager->flush();
                $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de votre profil.');
                return $this->redirectToRoute('app_profile_edit');
            }

            // Redirection en fonction du rôle
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('app_admin');
            } elseif (in_array('ROLE_HOTE', $user->getRoles())) {
                return $this->redirectToRoute('app_hote_dashboard');
            } else {
                return $this->redirectToRoute('app_voyageur_dashboard');
            }
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }

    #[Route('/delete', name: 'app_delete_account')]
    public function deleteAccount(
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response
    {
        // Get the current user
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Remove the user from the database
        $entityManager->remove($user);
        $entityManager->flush();

        // Logout the user
        $request->getSession()->invalidate();
        $this->container->get('security.token_storage')->setToken(null);

        // Add flash message
        $this->addFlash('success', 'Your account has been successfully deleted.');

        // Redirect to homepage
        return $this->redirectToRoute('app_homepage');
    }

    #[Route('/qr-code', name: 'app_profile_qr_code')]
    public function generateQrCode(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        try {
            // Create user data array (excluding sensitive information)
            $userData = [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'profileImage' => $user->getProfileImage(),
            ];

            // Convert user data to JSON
            $jsonData = json_encode($userData);

            // Configure renderer
            $renderer = new ImageRenderer(
                new RendererStyle(400, 10), // Increased size to accommodate more data
                new SvgImageBackEnd()
            );

            // Create writer
            $writer = new Writer($renderer);

            // Generate QR code with JSON data
            $qrCode = $writer->writeString($jsonData);

            // Create response
            $response = new Response($qrCode);
            $response->headers->set('Content-Type', 'image/svg+xml');
            $response->headers->set('Content-Disposition', 'inline; filename="qr-code.svg"');

            return $response;

        } catch (\Exception $e) {
            // Log the error
            error_log('QR Code generation error: ' . $e->getMessage());
            
            // Return error response
            return new Response('Error generating QR code: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 