<?php

namespace App\Controller;

use App\Entity\FormModule;
use App\Form\FormModuleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormModuleController extends AbstractController
{
    #[Route('/form/module', name: 'app_form_module')]
    public function index(): Response
    {
        return $this->render('formModule/index.html.twig', [
            'controller_name' => 'FormModuleController',
        ]);
    }
    //Fonction de listing d'un module
    #[Route('/formModule', name: 'app_form_module')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $formModules = $entityManager->getRepository(FormModule::class)->findAll();
        return $this->render('formModule/index.html.twig', [
            'formModules' => $formModules,
        ]);
    }
    //Fonction de création d'un module - protégé par l'administrateur
    #[Route('/formModule/new', name: 'form_module_new')]
    public function new(FormModule $formModule = null, Request $request, EntityManagerInterface $em): Response{
        if($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
        if(!$formModule){
            $formModule = new FormModule();
        }

        $form = $this->createForm(FormModuleType::class, $formModule);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $formModule = $form->getData();
            $em->persist($formModule);
            $em->flush();
            
            return $this->redirectToRoute('app_form_module');
        }
        return $this->render('formModule/new.html.twig', [
            'formAddFormModule' => $form, 
        ]);
        } else {
            $this->addFlash('error', 'Seul un administrateur connecté peut modifier cette partie');
            return $this->redirectToRoute('app_form_module');
        }
    }
    //Fonction de détail d'un module
    #[Route('/formModule/{id}', name: 'form_module_detail')]
    public function detailsFormModule(FormModule $formModule, EntityManagerInterface $em): Response
    {
        return $this->render('formModule/detail.html.twig', [
            'formModule' => $formModule,
        ]);
    }
}
