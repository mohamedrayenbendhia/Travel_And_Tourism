<?php
// src/Controller/ReclamationController.php
namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ReclamationController extends AbstractController
{
    private $entityManager;
    private $reclamationRepository;

    public function __construct(EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository)
    {
        $this->entityManager = $entityManager;
        $this->reclamationRepository = $reclamationRepository;
    }

    // Route pour résoudre une réclamation
    #[Route('/reclamation/{id}/resolve', name: 'app_reclamation_resolve')]
    public function resolveReclamation(Reclamation $reclamation): Response
    {
        // Vérifier que la réclamation appartient bien à l'utilisateur actuel ou à un administrateur
        if ($reclamation->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas résoudre cette réclamation.');
        }

        // Mettre à jour le statut de la réclamation
        $reclamation->setStatut('résolue');

        // Sauvegarder l'état de la réclamation
        $this->entityManager->flush();

        // Message flash de succès
        $this->addFlash('success', 'Réclamation résolue avec succès.');

        // Rediriger l'utilisateur vers la liste des réclamations
        return $this->redirectToRoute('app_reclamations');
    }

    // Route pour refuser une réclamation
    #[Route('/reclamation/{id}/refuse', name: 'app_reclamation_refuse')]
    public function refuseReclamation(Reclamation $reclamation): Response
    {
        // Vérifier que la réclamation appartient bien à l'utilisateur actuel ou à un administrateur
        if ($reclamation->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas refuser cette réclamation.');
        }

        // Mettre à jour le statut de la réclamation
        $reclamation->setStatut('refusée');

        // Sauvegarder l'état de la réclamation
        $this->entityManager->flush();

        // Message flash de succès
        $this->addFlash('success', 'Réclamation refusée.');

        // Rediriger l'utilisateur vers la liste des réclamations
        return $this->redirectToRoute('app_reclamations');
    }

    // Route pour soumettre une réclamation
    #[Route('/reclamation/soumettre', name: 'reclamation_submit')]
    public function submitReclamation(Request $request): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setDateSoumission(new \DateTime());
            $reclamation->setUser($this->getUser()); // Assigner l'utilisateur actuel (voyageur)

            // Optionnel : gérer l'upload des fichiers (photo, document)
            // Si vous avez des fichiers à uploader, vous pouvez les gérer ici avant de persister

            $this->entityManager->persist($reclamation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Réclamation soumise avec succès.');

            return $this->redirectToRoute('app_home'); // Redirection après soumission
        }

        return $this->render('reclamation/submit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour le suivi d'une réclamation
  // Exemple : Contrôleur pour afficher une réclamation
#[Route('/reclamation/{id}/suivi', name: 'reclamation_suivi')]
public function suiviReclamation(Reclamation $reclamation): Response
{
    // Vérifiez que la réclamation appartient à l'utilisateur actuel
    if ($reclamation->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette réclamation.');
    }

    return $this->render('reclamation/suivi.html.twig', [
        'reclamation' => $reclamation,  // On transmet la variable 'reclamation'
    ]);
}


    // Route pour évaluer une réclamation
    #[Route('/reclamation/{id}/evaluer', name: 'reclamation_evaluation')]
    public function evaluationReclamation(Request $request, Reclamation $reclamation): Response
    {
        // Vérifier que la réclamation appartient à l'utilisateur actuel
        if ($reclamation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas évaluer cette réclamation.');
        }

        $form = $this->createForm(EvaluationReclamationType::class); // Assurez-vous d'avoir créé ce formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'évaluation ici
            // Exemple : $reclamation->setEvaluation($form->getData());

            $this->entityManager->flush();

            $this->addFlash('success', 'Évaluation soumise avec succès.');

            return $this->redirectToRoute('reclamation_suivi', ['id' => $reclamation->getId()]);
        }

        return $this->render('reclamation/evaluation.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }

    // Route pour afficher toutes les réclamations en cours
    #[Route('/reclamations', name: 'app_reclamations')]
    public function index(): Response
    {
        // Récupérer toutes les réclamations en cours
        $reclamationsEnCours = $this->reclamationRepository->findBy(['statut' => 'en cours']);

        // Retourner la vue avec les réclamations
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationsEnCours,
        ]);
    }

// Route pour afficher les détails d'une réclamation
#[Route('/reclamation/{id}', name: 'app_reclamationsvoir')]
public function voirReclamation(int $id): Response
{
    // Récupérer la réclamation en fonction de l'ID
    $reclamation = $this->reclamationRepository->find($id);

    // Vérifier si la réclamation existe
    if (!$reclamation) {
        throw $this->createNotFoundException('Réclamation non trouvée');
    }

    // Retourner la vue avec les détails de la réclamation
    return $this->render('reclamation/voir.html.twig', [
        'reclamation' => $reclamation,
    ]);
}

}
