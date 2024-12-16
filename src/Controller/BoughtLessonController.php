<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\ThemaRepository;
use App\Repository\CursusRepository;
use App\Repository\LessonRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderdetailRepository;
use App\Repository\UsercursusRepository;
use App\Repository\UserCursusLessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller used for displaying and validating lessons after buying
 */
class BoughtLessonController extends AbstractController
{
    /**
     * @Route("/bought/lesson/{id}", name="app_bought_lesson")
     * Display lesson's content
     * @param int $id Lesson's id
     */
    public function displayLesson($id, CursusRepository $cursusRepository, LessonRepository $lessonRepository, UserCursusLessonRepository $userCursusLessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();
        $userId = $user->getId();
        //dd($userId);
        
        $lessonId = $id;
        $lesson = $userCursusLessonRepository->existlessonbyuser($userId,$lessonId);
       // dd($lesson);

        

        //display lesson
        if(isset($lesson)){
            $lessonDisplay = $lessonRepository->lessonDisplay($lessonId); 
            
        } else {
            $this->addFlash('danger', "Cette leçon n'est pas dans vos formations.");
        }
        
           
        return $this->render('bought_lesson/lesson.html.twig', [
            'controller_name' => 'BoughtLessonController',
            'user' => $user,
            'lessonDisplays' => $lessonDisplay,
            'lessons' => $lesson,
        ]);
    }

    /**
     * @Route("/bought/cursus/{id}", name="app_bought_cursus")
     * Display bought cursuses by user
     * @param int $id Cursus's id
     */
    public function displayCursus($id, CursusRepository $cursusRepository, LessonRepository $lessonRepository, UserCursusLessonRepository $userCursusLessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();
        $userId = $user->getId();
        
        $cursusId = $id;
        $cursus = $userCursusLessonRepository->existcursusbyuser($userId,$cursusId);
        $cursusData = $cursusRepository->find($id);
          
        if(isset($cursus)){
            $cursusDisplay = $cursusRepository->cursusDisplay($cursusId);
            
        } else {
            $this->addFlash('danger', "Ce cursus n'est pas dans vos formations.");
        }
        
           
        return $this->render('bought_lesson/cursus.html.twig', [
            'controller_name' => 'BoughtLessonController',
            'user' => $user,
            'cursusDisplays' => $cursusDisplay,
            'cursusData' => $cursusData,
        ]);
    }

    /**
     * @Route("/bought/lesson/validate/{id}", name="app_bought_lesson_validate")
     * Validate lesson by user
     * @param int $id userCusrsusLesson's id
     */
    public function validateLesson($id, CursusRepository $cursusRepository, LessonRepository $lessonRepository, UserCursusLessonRepository $userCursusLessonRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();
        $userId = $user->getId();
               
       
        $lessonValidated = $userCursusLessonRepository->findOneBy(['id'=>$id]);
        $lessonValidatedId = $lessonValidated->getLearning();
       
        if(isset($lessonValidated)){
          

            $lessonValidated->setIsValidated(true);
            $now = new \DateTimeImmutable();
            $lessonValidated->setUpdatedAt($now);
            $userFirstName = $user->getFirstName();
            $userLastName = $user->getLastName();
            $lessonValidated->setUpdatedBy($userFirstName.'.'.$userLastName); 
            $entityManager->persist($lessonValidated);
            $entityManager->flush();

        } else {
            $this->addFlash('danger', "Cette leçon n'est pas dans vos formations.");
        }

        
        
           
        return $this->render('bought_lesson/lesson.validate.html.twig', [
            'controller_name' => 'BoughtLessonController',
            'user' => $user,
            'lessonValidatedData' => $lessonRepository->findOneBy(['id'=>$lessonValidatedId]),
            
        ]);
    }
}
