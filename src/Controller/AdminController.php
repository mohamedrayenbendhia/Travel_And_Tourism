<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private $mailer;
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, MailerInterface $mailer)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->mailer = $mailer;
    }

    #[Route('/', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'token' => $this->csrfTokenManager->getToken('admin-token')->getValue(),
        ]);
    }

    #[Route('/user/add', name: 'app_admin_user_add', methods: ['POST'])]
    public function addUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if (!$this->isCsrfTokenValid('admin-token', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin');
        }

        try {
            $username = $request->request->get('username');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $roles = $request->request->all('roles');
            $isVerified = $request->request->getBoolean('isVerified');

            // Validate unique username and email
            $existingUser = $entityManager->getRepository(User::class)->findOneBy([
                'username' => $username
            ]);
            if ($existingUser) {
                throw new \Exception('Ce nom d\'utilisateur est déjà utilisé.');
            }

            $existingEmail = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $email
            ]);
            if ($existingEmail) {
                throw new \Exception('Cette adresse email est déjà utilisée.');
            }

            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setRoles($roles);
            $user->setIsVerified($isVerified);

            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur créé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/user/{id}/edit', name: 'app_admin_user_edit', methods: ['POST'])]
    public function editUser(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if (!$this->isCsrfTokenValid('admin-token', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin');
        }

        try {
            $newUsername = $request->request->get('username');
            $newEmail = $request->request->get('email');
            $roles = $request->request->all('roles');
            $isVerified = $request->request->getBoolean('isVerified');

            // Check if username is taken by another user
            $existingUser = $entityManager->getRepository(User::class)->findOneBy([
                'username' => $newUsername
            ]);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                throw new \Exception('Ce nom d\'utilisateur est déjà utilisé.');
            }

            // Check if email is taken by another user
            $existingEmail = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $newEmail
            ]);
            if ($existingEmail && $existingEmail->getId() !== $user->getId()) {
                throw new \Exception('Cette adresse email est déjà utilisée.');
            }

            $user->setUsername($newUsername);
            $user->setEmail($newEmail);
            $user->setRoles($roles);
            $user->setIsVerified($isVerified);

            // Update password if provided
            if ($newPassword = $request->request->get('password')) {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            }

            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid('admin-token', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin');
        }

        try {
            // Prevent admin from deleting themselves
            if ($user === $this->getUser()) {
                throw new \Exception('Vous ne pouvez pas supprimer votre propre compte.');
            }

            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/properties', name: 'app_admin_properties')]
    public function properties(): Response
    {
        return $this->render('admin/properties.html.twig');
    }

    #[Route('/reservations', name: 'app_admin_reservations')]
    public function reservations(): Response
    {
        return $this->render('admin/reservations.html.twig');
    }

    #[Route('/settings', name: 'app_admin_settings')]
    public function settings(): Response
    {
        return $this->render('admin/settings.html.twig');
    }

    #[Route('/user/{id}/ban', name: 'app_admin_user_ban', methods: ['POST'])]
    public function banUser(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid('admin-token', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin');
        }

        try {
            // Prevent admin from banning themselves
            if ($user === $this->getUser()) {
                throw new \Exception('Vous ne pouvez pas vous bannir vous-même.');
            }

            $banReason = $request->request->get('banReason');
            if (!$banReason) {
                throw new \Exception('La raison du bannissement est requise.');
            }

            $user->setIsBanned(true);
            $user->setBanReason($banReason);

            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur banni avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/user/{id}/unban', name: 'app_admin_user_unban', methods: ['POST'])]
    public function unbanUser(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid('admin-token', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin');
        }

        try {
            $user->setIsBanned(false);
            $user->setBanReason(null);

            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur débanni avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/send-email/{id}', name: 'app_admin_send_email', methods: ['POST'])]
    public function sendEmail(Request $request, User $user): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('admin-token', $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin');
        }

        $subject = $request->request->get('subject');
        $message = $request->request->get('message');

        try {
            $email = (new TemplatedEmail())
                ->from($this->getParameter('app.admin_email'))
                ->to($user->getEmail())
                ->subject($subject)
                ->htmlTemplate('emails/admin_message.html.twig')
                ->context([
                    'message' => $message,
                    'username' => $user->getUsername()
                ]);

            $this->mailer->send($email);

            $this->addFlash('success', 'Email envoyé avec succès à ' . $user->getUsername());
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/users/export-pdf', name: 'app_admin_users_export_pdf')]
    public function exportUsersPdf(EntityManagerInterface $entityManager): Response
    {
        // Get all users
        $users = $entityManager->getRepository(User::class)->findAll();

        // Generate HTML content
        $html = $this->renderView('admin/users_pdf.html.twig', [
            'users' => $users,
        ]);

        // Configure Dompdf
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        // Create new PDF instance
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Generate PDF
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="users-list.pdf"');

        return $response;
    }

    #[Route('/admin/user/{id}/roles', name: 'app_admin_user_roles', methods: ['POST'])]
    public function updateUserRoles(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid('admin-token', $request->request->get('_token'))) {
            return $this->json([
                'success' => false,
                'message' => 'Token CSRF invalide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Prevent admin from modifying their own roles
            if ($user === $this->getUser()) {
                throw new \Exception('Vous ne pouvez pas modifier vos propres rôles.');
            }

            $roles = $request->request->all('roles');
            
            // Ensure at least ROLE_USER is present
            if (!in_array('ROLE_USER', $roles)) {
                $roles[] = 'ROLE_USER';
            }

            $user->setRoles($roles);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'message' => 'Les rôles de ' . $user->getUsername() . ' ont été mis à jour avec succès.'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
