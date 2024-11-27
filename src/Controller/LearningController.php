<?php

namespace App\Controller;

use App\Repository\ThemaRepository;
use App\Repository\CursusRepository;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used for shop pages
 */
class LearningController extends AbstractController
{
    /**
     * @Route("/learning", name="app_learning")
     * Display themas
     */
    public function index(ThemaRepository $themaRepository): Response
    {
        $user = $this->getUser();
        
        return $this->render('learning/learning.html.twig', [
            'controller_name' => 'LearningController',
            'user' => $user,
            'themas' => $themaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/learning/{id}", name="app_learning_cursus")
     * Display cursuses by thema
     * @param int $id Thema's id
     */
    public function cursusListing($id, ThemaRepository $themaRepository,CursusRepository $cursusRepository): Response
    {
        $user = $this->getUser();
        $thema = $themaRepository->findOneBy(['id'=>$id]);
       
        
        return $this->render('learning/learning.listing.html.twig', [
            'controller_name' => 'LearningController',
            'user' => $user,
            'thema' => $thema,
            'cursuses' => $cursusRepository->findBy(['thema'=>$id]),
        ]);
    }

    /**
     * @Route("/learning/lesson/{id}", name="app_learning_lesson")
     * Display lessons by cursus
     * @param int $id Cursus's id
     */
    public function lessonListing($id, CursusRepository $cursusRepository,LessonRepository $lessonRepository): Response
    {
        $user = $this->getUser();
        $cursus = $cursusRepository->findOneBy(['id'=>$id]);
        $lessons = $lessonRepository->findBy(['cursus'=>$id]);
        
       
        
        return $this->render('learning/learning.lesson.html.twig', [
            'controller_name' => 'LearningController',
            'user' => $user,
            'cursus' => $cursus,
            'lessons' => $lessons,
        ]);
    }

}
