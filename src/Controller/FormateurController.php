<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
    #[Route('/formateur/new', name: 'formateur_new')]
    public function new(Formateur $formateur = null, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response{
        if(!$formateur){
            $formateur = new Formateur();
        }

        $form = $this->createForm(FormateurType::class, $formateur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $passwordHash = $passwordHasher->hashPassword($formateur, $formateur->getPassword());
            $formateur->setPassword($passwordHash);
            $formateur = $form->getData();
            $em->persist($formateur);
            $em->flush();
            
            return $this->redirectToRoute('app_formateur');
        }
        return $this->render('formateur/new.html.twig', [
            'formAddFormateur' => $form, 
        ]);
    }

    #[Route('/formateur/{id<\d+>}', name: 'formateur_detail')]
    public function DetailsFormateur(Formateur $formateur): Response
    {
        return $this->render('formateur/detail.html.twig', [
            'formateur' => $formateur,
        ]);
    }
}
