<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;  // Ajout du service Security
use Symfony\Component\Security\Core\Exception\AccessDeniedException;  // Ajout de l'exception d'accès refusé

class UserController extends AbstractController
{
    private $entityManager;  // Déclaration de la propriété entityManager
    private $security;  // Déclaration de la propriété security

    // Injection de l'EntityManager et du service Security via le constructeur
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;  // Initialisation de la propriété security
    }

    #[Route('/admin/users', name: 'user_management')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/{id}/profile', name: 'user_profile_management')]
    public function manageProfile(User $user): Response
    {
        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce profil.');
        }

        return $this->render('admin/user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/users/{id}/roles', name: 'user_roles_management')]
    public function manageRoles(User $user, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès interdit');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation de l'EntityManager injecté pour persister les changements
            $this->entityManager->flush();

            $this->addFlash('success', 'Rôles de l\'utilisateur mis à jour avec succès.');

            return $this->redirectToRoute('user_management');
        }

        return $this->render('admin/user/roles.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/admin/users/{id}/permissions', name: 'user_permissions_management')]
    public function managePermissions(User $user): Response
    {
        // Vérifier si l'utilisateur connecté a le rôle ROLE_ADMIN
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès interdit');
        }

        // Récupérer les permissions réelles de l'utilisateur (à ajuster selon votre logique)
        $permissions = $this->getUserPermissions($user);

        return $this->render('admin/user/permissions.html.twig', [
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }

    private function getUserPermissions(User $user): array
    {
        // Récupérer les permissions de l'utilisateur depuis une source (ex : base de données)
        // Remplacez cette logique par celle qui vous est propre
        return $user->getRoles();  // Exemple : suppose que `getRoles()` retourne un tableau des rôles de l'utilisateur
    }

    #[Route('/admin/users/create', name: 'user_create')]
    public function create(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation de l'EntityManager pour persister l'utilisateur
            $this->entityManager->persist($user);  // Persister l'utilisateur
            $this->entityManager->flush();  // Enregistrer dans la base de données

            // Redirection après création réussie
            $this->addFlash('success', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('user_management');
        }

        return $this->render('admin/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/{id}/delete', name: 'user_delete')]
    public function deleteUser(User $user): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès interdit');
        }

        // Suppression de l'utilisateur de la base de données
        $this->entityManager->remove($user);
        $this->entityManager->flush();  // Enregistrement des modifications

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('user_management');
    }
}
