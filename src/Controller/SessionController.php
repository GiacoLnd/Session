<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\StagiaireRepository;
use App\Repository\FormModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(): Response
    {
        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
        ]);
    }


    #[Route('/session', name: 'app_session')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $sessions = $entityManager->getRepository(Session::class)->findAll();
    
        $passedSessions = $entityManager->getRepository(Session::class)->findPassedSession();
        $currentSessions = $entityManager->getRepository(Session::class)->findCurrentSession();
        $nextSessions = $entityManager->getRepository(Session::class) ->findComingSession();
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
            'passedSessions' => $passedSessions,
            'currentSessions' => $currentSessions,
            'nextSessions' => $nextSessions
        ]);
    }
    #[Route('/session/new', name: 'session_new')]
    public function new(Session $session = null, Request $request, EntityManagerInterface $em): Response{
        if(!$session){
            $session = new Session();
        }

        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $session = $form->getData();
            $em->persist($session);
            $em->flush();
            
            return $this->redirectToRoute('app_session');
        }
        return $this->render('session/new.html.twig', [
            'formAddSession' => $form, 
        ]);
    }

#[Route('/session/{session_id}/stagiaire/inscrire/{stagiaire_id}', name: 'session_inscrire_stagiaire')]

public function inscrireStagiaire(int $session_id, int $stagiaire_id, SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository, EntityManagerInterface $em): Response
{
    

    $session = $sessionRepository->find($session_id);
    $stagiaire = $stagiaireRepository->find($stagiaire_id);

    if (!$session || !$stagiaire) {
        throw $this->createNotFoundException("Session ou Stagiaire introuvable.");
    }

    $session->addStagiaire($stagiaire);
    $em->persist($session);
    $em->flush();

    $this->addFlash('success', 'Stagiaire inscrit avec succès.');

    return $this->redirectToRoute('session_details', ['id' => $session_id]);
}

#[Route('/session/{session_id}/stagiaire/desinscrire/{stagiaire_id}', name: 'session_desinscrire_stagiaire')]

public function desinscrireStagiaire(int $session_id, int $stagiaire_id, SessionRepository $sessionRepository,FormModuleRepository $fr, StagiaireRepository $stagiaireRepository, EntityManagerInterface $em): Response
{   $session = $sessionRepository->find($session_id);
    $stagiaire = $stagiaireRepository->find($stagiaire_id);


    $session->removeStagiaire($stagiaire);
    $em->persist($session);
    $em->flush();

    return $this->redirectToRoute('session_details', ['id' => $session_id]);
}

#[Route('/session/{session_id}/module/inscrire/{module_id}', name: 'session_inscrire_module')]
public function inscrireModule(int $session_id, int $module_id,Request $request, SessionRepository $sessionRepository, FormModuleRepository $moduleRepository, ProgrammeRepository $pr, EntityManagerInterface $em): Response {
    $session = $sessionRepository->find($session_id);
    $module = $moduleRepository->find($module_id);
    $duree = $moduleRepository->findModuleWithDuration($module_id);
    $duree = $request->request->get('duree');

    $programme = new Programme();
    $programme->setSession($session);
    $programme->setFormModule($module);
    $programme->setDuree($duree);

    $session->addProgramme($programme);
    $em->persist($programme);
    $em->persist($session);
    $em->flush();


    return $this->redirectToRoute('session_details', ['id' => $session_id]);
}

#[Route('/session/{session_id}/module/desinscrire/{module_id}', name: 'session_desinscrire_module')]
public function desinscrireModule(int $session_id, int $module_id, ProgrammeRepository $programmeRepository, EntityManagerInterface $em): Response {
    $programme = $programmeRepository->findOneBy([
        'session' => $session_id,
        'formModule' => $module_id,
    ]);
    $em->remove($programme);
    $em->flush();

    return $this->redirectToRoute('session_details', ['id' => $session_id]);
}

#[Route('/session/{id}', name: 'session_details')]
public function detailsSession(
    Session $session,
    EntityManagerInterface $em,
    FormModuleRepository $formModuleRepository,
    SessionRepository $sessionRepository,
    ProgrammeRepository $programmeRepository,
    Request $request
): Response {
    // Récupération des stagiaires non inscrits
    $nonInscrits = $sessionRepository->findNonInscrits($session->getId());
    // Récupération des stagiaires inscrits
    $stagiairesInscrits = $session->getStagiaires();
    // Récupération des modules non-programmés
    $nonProgramme = $sessionRepository->findNonProgramme($session->getId());
    // Récupération des modules déjà programmés
    $moduleProgrammes = $session->getProgrammes();

    // Vérification si une soumission a été effectuée
    if ($request->isMethod('POST') && $request->request->has('module_id')) {
        $moduleId = $request->request->get('module_id');
        $duree = $formModuleRepository->findModuleWithDuration($moduleId);


        // Recherche du module à partir de l'ID
        $module = $formModuleRepository->find($moduleId);

        if (!$module) {
            $this->addFlash('error', 'Module introuvable.');
            return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
        }

        // Création d'un nouveau programme
        $programme = new Programme();
        $programme->setFormModule($module);
        $programme->setSession($session);
        $programme->setDuree($duree);

        $em->persist($programme);
        $em->flush();

        $this->addFlash('success', 'Le module a été prévu avec succès.');
        return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
    }

    return $this->render('session/detail.html.twig', [
        'session' => $session,
        'modulesNonProgrammes' => $nonProgramme,
        'modulesProgrammes' => $moduleProgrammes,
        'stagiaires' => $nonInscrits,
        'stagiairesInscrits' => $stagiairesInscrits,
    ]);
}

}

