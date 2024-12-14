<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(): Response
    {
        return $this->render('stagiaire/index.html.twig', [
            'controller_name' => 'StagiaireController',
        ]);
    }
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $stagiaires = $entityManager->getRepository(Stagiaire::class)->findAll();
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }
    #[Route('/stagiaire/new', name: 'stagiaire_new')]
    public function new(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $em): Response{
        if(!$stagiaire){
            $stagiaire = new Stagiaire();
        }

        $form = $this->createForm(StagiaireType::class, $stagiaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $stagiaire = $form->getData();
            $em->persist($stagiaire);
            $em->flush();
            
            return $this->redirectToRoute('app_stagiaire');
        }
        return $this->render('stagiaire/new.html.twig', [
            'formAddStagiaire' => $form, 
        ]);
    }
    
    #[Route('/stagiaire/{id}/edit', name: 'stagiaire_edit')]
    public function edit(Stagiaire $stagiaire, Request $request, EntityManagerInterface $em): Response
    {
    $form = $this->createForm(StagiaireType::class, $stagiaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        return $this->redirectToRoute('>>');
    }

    return $this->render('stagiaire/edit.html.twig', [
        'formEditStagiaire' => $form,    
    ]);
}

    #[Route('/stagiaire/{id}', name: 'stagiaire_detail')]
    public function detailsStagiaire(Stagiaire $stagiaire): Response
    {
        return $this->render('stagiaire/detail.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }
}
