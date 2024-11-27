<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderdetailRepository;
use App\Repository\CursusRepository;
use App\Repository\LessonRepository;
use App\Repository\UsercursusRepository;
use App\Repository\UserCursusLessonRepository;
use App\Form\UserModifyFormType;
use App\Form\PasswordModifyFormType;
use App\Form\MailUserModifyFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Monolog\DateTimeImmutable;

/**
 * Controller used for profile's pages
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     * Profile page
     */
    public function indexUser(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $user ->getId();
        //dd($user);
        //$profile = $userRepository->findOneById($user);
        
        return $this->render('profile/profile.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user
        ]);
    }

    /**
     * @Route("/profile/update", name="app_profile_update")
     * Update profile by user: name, password, mail
     */
    public function updateUser(UserRepository $userRepository,Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $user ->getId();

        //Modify user
        $userModifyForm = $this->createForm(UserModifyFormType::class,$user);
        $userModifyForm->handleRequest($request);
        if($userModifyForm->isSubmitted() && $userModifyForm->isValid()){
           
            $now = new \DateTimeImmutable();
            $user->setUpdatedAt($now);
            $userFirstName = $user->getFirstName();
            $userLastName = $user->getLastName();
            $user->setUpdatedBy($userFirstName.'.'.$userLastName);  
             
            
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le profil a bien été mis à jour.');
            return $this->redirectToRoute('app_profile');
        }

        //Modify password
        $passwordModifyForm = $this->createForm(PasswordModifyFormType::class,$user);
        $passwordModifyForm->handleRequest($request);
        if($passwordModifyForm->isSubmitted() && $passwordModifyForm->isValid()){
           
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $passwordModifyForm->get('password')->getData()
                    )
            );
            
            $now = new \DateTimeImmutable();
            $user->setUpdatedAt($now);
            $userFirstName = $user->getFirstName();
            $userLastName = $user->getLastName();
            $user->setUpdatedBy($userFirstName.'.'.$userLastName);  
             
            
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_profile');
        }

        //Modify email
        $mailModifyForm = $this->createForm(MailUserModifyFormType::class,$user);
        $mailModifyForm->handleRequest($request);
        if($mailModifyForm->isSubmitted() && $mailModifyForm->isValid()){
           
                     
            $now = new \DateTimeImmutable();
            $user->setUpdatedAt($now);
            $userFirstName = $user->getFirstName();
            $userLastName = $user->getLastName();
            $user->setUpdatedBy($userFirstName.'.'.$userLastName);
            $user->setIsActivated(false);
             
            
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le mail a bien été mis à jour. Vous devez réactiver votre compte.');
            return $this->redirectToRoute('app_profile');
        }
        
        
        return $this->render('profile/profile.update.user.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'userModifyForm' => $userModifyForm->createView(),
            'passwordModifyForm' => $passwordModifyForm->createView(),
            'mailModifyForm' => $mailModifyForm->createView(),
        ]);
    }

    /**
     * @Route("/profile/orders", name="app_profile_orders")
     * Display orders by user
     */
    public function ordersUser(UserRepository $userRepository, OrderRepository $orderRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();
        $user ->getId();
        
        
        return $this->render('profile/profile.user.orders.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'orders' => $orderRepository->orderbyuser($user),
        ]);
    }

    /**
     * @Route("/profile/orderdetails/{id}", name="app_profile_orderdetails")
     * Display line of orders by user
     * @param int $id Order's id
     */
    public function orderDetailsUser($id, UserRepository $userRepository, OrderRepository $orderRepository, OrderdetailRepository $orderDetailRepository, CursusRepository $cursusRepository,LessonRepository $lessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();
        $user ->getId();
        $order = $id;
        $orderDetail = $orderDetailRepository->findBy(['ordernumber'=>$id]);

        foreach($orderDetail as $data){
            $repository=$data->getRepository();
            $orderDetailId=$data->getLearningId();
            $orderDetailPrice=$data->getPrice();
            if($repository=='cursus'){
                $learning = $cursusRepository->findOneBy(['id'=>$orderDetailId]);
                $learningName = "Cursus ".$learning->getLevel()." ".$learning->getName();
            } else {
                $learning = $lessonRepository->findOneBy(['id'=>$orderDetailId]);
                $learningName = "Leçon ".$learning->getName();
            }
            $learningContent[] = [
                "learning" => $learningName,
                "price" => $orderDetailPrice,
                "repository" => $repository,
                "learningId" => $orderDetailId,

            ];
            
            
        }
        
        
        return $this->render('profile/profile.user.orderdetails.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'order' => $order,
            'learningContents' => $learningContent,
        ]);
    }

    /**
     * @Route("/profile/learnings", name="app_profile_learnings")
     * Display bought learnings by user
     */
    public function learningsUser(UserRepository $userRepository, UsercursusRepository $userCursusRepository, CursusRepository $cursusRepository,LessonRepository $lessonRepository, UserCursusLessonRepository $userCursusLessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();
        $userId = $user->getId();

        //$userCursus = $userCursusRepository->findBy(['user'=>$user]);

        //foreach($userCursus as $data){
        //    $repository=$data->getRepository();
        //    $userCursusId=$data->getLearningId();
        //    $userCursusCreatedAt=$data->getCreatedAt();
        //    $userCursusUpdatedAt=$data->getUpdatedAt();
        //    $userCursusIsValidated=$data->isIsValidated();
        //    if($repository=='cursus'){
        //        $userLearning = $cursusRepository->findOneBy(['id'=>$userCursusId]);
        //        $userLearningName = "Cursus ".$userLearning->getLevel()." ".$userLearning->getName();
        //    } else {
        //        $userLearning = $lessonRepository->findOneBy(['id'=>$userCursusId]);
        //        $userLearningName = "Leçon ".$userLearning->getName();
        //    }
        //    $userLearningContent[] = [
        //        "learning" => $userLearningName,
        //        "createdAt" => $userCursusCreatedAt,
        //        "updatedAt" => $userCursusUpdatedAt,
        //        "isValidated" => $userCursusIsValidated,
        //    ];
            
            
        //}

        $userCursusLesson = $userCursusLessonRepository->findBy(['user'=>$user]);
        foreach($userCursusLesson as $data){
            $userCursusLessonId=$data->getId();
            $userCursusId=$data->getCursus()->getId();
            $userCursusName=$cursusRepository->findOneBy(['id'=>$userCursusId])->getName();
            $userCursusLevel=$cursusRepository->findOneBy(['id'=>$userCursusId])->getLevel();
            $userCursusNbLesson=$cursusRepository->findOneBy(['id'=>$userCursusId])->getNbLessons();
            $userLessonId=$data->getLearning()->getId();
            $userLessonName=$lessonRepository->findOneBy(['id'=>$userLessonId])->getName();
            $userLessonNumber=$lessonRepository->findOneBy(['id'=>$userLessonId])->getNumber();
            $userCursusLessonCreatedAt=$data->getCreatedAt();
            $userCursusLessonUpdatedAt=$data->getUpdatedAt();
            $userCursusLessonIsValidated=$data->isIsValidated();

            $userCursusLessonContent[] = [
                "id" => $userCursusLessonId,
                "createdAt" => $userCursusLessonCreatedAt,
                "updatedAt" => $userCursusLessonUpdatedAt,
                "isValidated" => $userCursusLessonIsValidated,
                "cursusName" => $userCursusName,
                "cursusLevel" =>$userCursusLevel,
                "cursusNbLesson" =>$userCursusNbLesson,
                "lessonName" => $userLessonName,
                "lessonNumber" => $userLessonNumber,
                "lessonId" =>$userLessonId,
            ];
            
            
        }
       
        return $this->render('profile/profile.user.learnings.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            //'userLearningContents' =>$userLearningContent,
            'userCursusLessonContents' => $userCursusLessonContent,
        ]);
    }


    /**
     * @Route("/profile/certifications", name="app_profile_certifications")
     * Display certifications by user
     */
    public function certificationsUser(UserRepository $userRepository, CursusRepository $cursusRepository, UserCursusLessonRepository $userCursusLessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();
        $userId = $user->getId();

        $userCertifications = $userCursusLessonRepository->certificationbyuser($userId);
        //dd($userCertifications);
        foreach($userCertifications as $data){
            $cursusId=$data['cursus'];
            $cursusName=$cursusRepository->findOneBy(['id'=>$cursusId])->getName();
            $cursusLevel=$cursusRepository->findOneBy(['id'=>$cursusId])->getLevel();

            $userCertificationContent[] = [
                "cursusName" => $cursusName,
                "cursusLevel" =>$cursusLevel,
                
            ];
        }
       
        return $this->render('profile/profile.user.certifications.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'userCertificationContents' => $userCertificationContent,
            
        ]);
    }


    
}
