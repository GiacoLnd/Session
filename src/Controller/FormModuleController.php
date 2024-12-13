<?php

namespace App\Controller;

use App\Entity\FormModule;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/formModule', name: 'app_form_module')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $formModules = $entityManager->getRepository(FormModule::class)->findAll();
        return $this->render('formModule/index.html.twig', [
            'formModules' => $formModules,
        ]);
    }

    #[Route('/formModule/{id}', name: 'form_module_detail')]
    public function detailsFormModule(FormModule $formModule): Response
    {
        return $this->render('formModule/detail.html.twig', [
            'formModule' => $formModule,
        ]);
    }
}
