<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use Doctrine\ORM\EntityManagerInterface;
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
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }
    #[Route('/session/{id}', name: 'session_details')]
    public function detailsSession(Session $session, EntityManagerInterface $em): Response
    {
        // Charger les programmes associés à la session
        $programmes = $em->getRepository(Programme::class)
            ->findBy(['session' => $session]);
        $stagiaires = $session->getStagiaires();
    
        return $this->render('session/detail.html.twig', [
            'session' => $session,
            'programmes' => $programmes,
            'stagiaires' => $stagiaires
        ]);
    }
}
