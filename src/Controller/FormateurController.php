<?php

namespace App\Controller;

use App\Entity\Formateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(): Response
    {
        return $this->render('formateur/index.html.twig', [
            'controller_name' => 'FormateurController',
        ]);
    }
    #[Route('/formateur', name: 'app_formateur')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $formateurs = $entityManager->getRepository(Formateur::class)->findAll();
        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs,
        ]);
    }
    #[Route('/formateur/{id}', name: 'formateur_detail')]
    public function DetailsFormateur(Formateur $formateur): Response
    {
        return $this->render('formateur/detail.html.twig', [
            'formateur' => $formateur,
        ]);
    }
}
