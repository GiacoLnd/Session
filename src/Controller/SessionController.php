<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Entity\FormModule;
use App\Entity\Session;
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

    //Fonction de listing des sessions
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
    //Fonction de création d'une session- protégée par l'administrateur
    #[Route('/session/new', name: 'session_new')]
    public function new(Session $session = null, Request $request, EntityManagerInterface $em): Response{
        if($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
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
        } else {
            $this->addFlash('error', 'Seul un administrateur connecté peut modifier cette partie');
            return $this->redirectToRoute('app_session');
        }
    }
    //Fonction d'inscription d'un stagiaire à la session donnée - protégé par l'administrateur
    #[Route('/session/{session}/stagiaire/inscrire/{stagiaire}', name: 'session_inscrire_stagiaire')]

    public function inscrireStagiaire(Session $session, Stagiaire $stagiaire, SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository, EntityManagerInterface $em): Response
    {
        if($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            if ($session->getNombreStagiaires() >= $session->getNombrePlace()) {
                $this->addFlash('error', 'La session est complète. Impossible d\'ajouter plus de stagiaires.');
                return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
            }
            
            $session->addStagiaire($stagiaire);
            $em->persist($session);
            $em->flush();
        
            return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
        } else {
            $this->addFlash('error', 'Seul un administrateur connecté peut modifier cette partie');
            return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
        }

    }

    //Fonction de désinscription d'un stagiaire à la session donnée - protégé par l'administrateur
    #[Route('/session/{session}/stagiaire/desinscrire/{stagiaire}', name: 'session_desinscrire_stagiaire')]
    public function desinscrireStagiaire(Session $session, Stagiaire $stagiaire, SessionRepository $sessionRepository,FormModuleRepository $fr, StagiaireRepository $stagiaireRepository, EntityManagerInterface $em): Response{
        if($this->getUser() && $this->isGranted('ROLE_ADMIN')) {  
            $session->removeStagiaire($stagiaire);
            $em->persist($session);
            $em->flush();

            return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
        } else {
            $this->addFlash('error', 'Seul un administrateur connecté peut modifier cette partie');
            return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
        }
    }

    //Fonction d'inscription d'un module à une session donnée - Protégée par l'administrateur
    #[Route('/session/{session}/module/inscrire/{module}', name: 'session_inscrire_module')]
    public function inscrireModule( Session $session, FormModule $module,Request $request, SessionRepository $sessionRepository, FormModuleRepository $moduleRepository, ProgrammeRepository $pr, EntityManagerInterface $em): Response {
        if($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            $duree = $request->request->get('duree');

            $programme = new Programme();
            $programme->setSession($session);
            $programme->setFormModule($module);
            $programme->setDuree($duree);

            $session->addProgramme($programme);
            $em->persist($programme);
            $em->flush();


            return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
        } else {
            $this->addFlash('error', 'Seul un administrateur connecté peut modifier cette partie');
            return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
        }
    }

    //Fonction de desincription d'un module à une session - protégé par l'administrateur    
    #[Route('/session/{session}/module/desinscrire/{module}', name: 'session_desinscrire_module')]
    public function desinscrireModule(Session $session, FormModule $module, ProgrammeRepository $programmeRepository, EntityManagerInterface $em): Response {
        if($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            $sessionId = $session->getId();
            $moduleId = $module->getId();
            
            $programme = $programmeRepository->findOneBy([
                'session' => $sessionId,
            ]);

            $em->remove($programme);
            $em->flush();

            return $this->redirectToRoute('session_details', ['id' => $sessionId]);
        } else {
            $this->addFlash('error', 'Seul un administrateur connecté peut modifier cette partie');
            return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
        }
    }

    //Fonction de détails d'une session incluant l'inscription d'un stagiaire, d'un module, la desinscription d'un stagiaire et d'un module
    #[Route('/session/{id}', name: 'session_details')]
    public function detailsSession(
        Session $session,
        FormModule $module,
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

            $programme = new Programme();
            $programme->setFormModule($module);
            $programme->setSession($session);
            $programme->setDuree($duree);

            $em->persist($programme);
            $em->flush();

            return $this->redirectToRoute('session_details', ['id' => $session->getId()]);
        }

        return $this->render('session/detail.html.twig', [
            'session' => $session,
            'module' => $module,
            'modulesNonProgrammes' => $nonProgramme,
            'modulesProgrammes' => $moduleProgrammes,
            'stagiairesNonInscrits' => $nonInscrits,
            'stagiairesInscrits' => $stagiairesInscrits,
        ]);
    }

}

