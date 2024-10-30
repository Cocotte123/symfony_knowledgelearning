<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoughtLessonController extends AbstractController
{
    /**
     * @Route("/bought/lesson", name="app_bought_lesson")
     */
    public function index(): Response
    {
        return $this->render('bought_lesson/index.html.twig', [
            'controller_name' => 'BoughtLessonController',
        ]);
    }
}
