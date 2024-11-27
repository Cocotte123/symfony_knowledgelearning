<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Thema;
use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Repository\UserRepository;
use App\Repository\ThemaRepository;
use App\Repository\CursusRepository;
use App\Repository\LessonRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderdetailRepository;
use App\Repository\UsercursusRepository;
use App\Repository\UserCursusLessonRepository;
use App\Form\UserModifyAdminFormType;
use App\Form\MailUserModifyFormType;
use App\Form\ThemaRegistrationFormType;
use App\Form\CursusRegistrationFormType;
use App\Form\LessonRegistrationFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Controller used for administration's pages
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     * Home page admin
     */
    public function index(): Response
    {
        $user = $this->getUser();
       
        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/users", name="app_admin_users")
     * Display list of users with search
     */
    public function usersAdmin(UserRepository $userRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $admin = $this->getUser();
        

        ///////////////////////////////
        ///filter by name or mail
        $searchUser = null;
        $searchUserForm = $this->createFormBuilder()
        ->add('searchUser', TextType::class,[
            
           'attr' => ['class' => 'form-control'],
            'label' => "Rechercher un utilisateur par mail ou nom :",
            'attr' => ['onChange' => 'submit()'],
        ])
        ->getForm();

        $searchUserForm -> handleRequest($request);
        if($searchUserForm->isSubmitted() && $searchUserForm->isValid()){
        $searchUser = $searchUserForm->get('searchUser')->getData();
        }
       



        ////////////////////////////////
       
        return $this->render('admin/admin.users.html.twig', [
            'controller_name' => 'AdminController',
            'admin' => $admin,
            'users' => $userRepository->filteruser($searchUser),
            'searchUserForm' => $searchUserForm->createView(),

        ]);
    }

    /**
     * @Route("/admin/users/delete/{id}", name="app_admin_users_delete")
     * Delete user by id
     * @param int $id User's id
     */
    public function userAdminDelete($id,UserRepository $userRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $userRepository->findOneBy(['id'=>$id]);
       

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', "L'utilisateur a bien été supprimé.");
       
        return $this->redirectToRoute('app_admin_users');
    }

    /**
     * @Route("/admin/users/update/{id}", name="app_admin_users_update")
     * Update name, mail by user
     * @param int $id User's id
     */
    public function userAdminUpdate($id,UserRepository $userRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $admin = $this->getUser();
        $user = $userRepository->findOneBy(['id'=>$id]);
       
        //Modify user
        $userModifyAdminForm = $this->createForm(UserModifyAdminFormType::class,$user);
        $userModifyAdminForm->handleRequest($request);
        if($userModifyAdminForm->isSubmitted() && $userModifyAdminForm->isValid()){
        
            $now = new \DateTimeImmutable();
            $user->setUpdatedAt($now);
            $adminFirstName = $admin->getFirstName();
            $adminLastName = $admin->getLastName();
            $user->setUpdatedBy($adminFirstName.'.'.$adminLastName);  
            
            
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le profil {{ user.email }} a bien été mis à jour.');
            return $this->redirectToRoute('app_admin_users');
        }

        //Modify email
        $mailModifyForm = $this->createForm(MailUserModifyFormType::class,$user);
        $mailModifyForm->handleRequest($request);
        if($mailModifyForm->isSubmitted() && $mailModifyForm->isValid()){
           
                     
            $now = new \DateTimeImmutable();
            $user->setUpdatedAt($now);
            $adminFirstName = $admin->getFirstName();
            $adminLastName = $admin->getLastName();
            $user->setUpdatedBy($adminFirstName.'.'.$adminLastName);
            $user->setIsActivated(false);
             
            
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "Le mail a bien été mis à jour. L'utilisateur devra réactiver votre compte.");
            return $this->redirectToRoute('app_admin_users');
        }

        ////////////////////////////////////////////////////////
       
        return $this->render('admin/admin.update.user.html.twig', [
            'controller_name' => 'AdminController',
            'admin' => $admin,
            'user' => $user,
            'userModifyAdminForm' => $userModifyAdminForm->createView(),
            'mailModifyForm' => $mailModifyForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/learnings", name="app_admin_learnings")
     * Display and create cursus, lessons and thema
     */
    public function learningsAdmin(ThemaRepository $themaRepository, CursusRepository $cursusRepository,LessonRepository $lessonRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $admin = $this->getUser();


        //////////////////////////////////
        //new thema
        $thema = new Thema();
        $newThemaForm = $this->createForm(ThemaRegistrationFormType::class, $thema);
        $newThemaForm -> handleRequest($request);
        if($newThemaForm->isSubmitted() && $newThemaForm->isValid()){
            
            $now = new \DateTimeImmutable();
            $thema->setCreatedAt($now);
            $adminFirstName = $admin->getFirstName();
            $adminLastName = $admin->getLastName();
            $thema->setCreatedBy($adminFirstName.'.'.$adminLastName);  
            $entityManager->persist($thema);
            $entityManager->flush();

            $this->addFlash('success', "Le nouveau thème a bien été créé.");
            return $this->redirectToRoute('app_admin_learnings');
        }

        //////////////////////////////////
        //new cursus
        $cursus = new Cursus();
        $newCursusForm = $this->createForm(CursusRegistrationFormType::class, $cursus);
        $newCursusForm -> handleRequest($request);
        if($newCursusForm->isSubmitted() && $newCursusForm->isValid()){
            $now = new \DateTimeImmutable();
            $cursus->setCreatedAt($now);
            $adminFirstName = $admin->getFirstName();
            $adminLastName = $admin->getLastName();
            $cursus->setCreatedBy($adminFirstName.'.'.$adminLastName);  
            $entityManager->persist($cursus);
            $entityManager->flush();

            $this->addFlash('success', "Le nouveau cursus a bien été créé.");
            return $this->redirectToRoute('app_admin_learnings');
        }

        //////////////////////////////////
        //new lesson
        $lesson = new Lesson();
        $newLessonForm = $this->createForm(LessonRegistrationFormType::class, $lesson);
        $newLessonForm -> handleRequest($request);
        if($newLessonForm->isSubmitted() && $newLessonForm->isValid()){
            $now = new \DateTimeImmutable();
            $lesson->setCreatedAt($now);
            $adminFirstName = $admin->getFirstName();
            $adminLastName = $admin->getLastName();
            $lesson->setCreatedBy($adminFirstName.'.'.$adminLastName);

            $entityManager->persist($lesson);
            $entityManager->flush();

            $this->addFlash('success', "La nouvelle leçon a bien été créée.");
            return $this->redirectToRoute('app_admin_learnings');
        }

        

             
        return $this->render('admin/admin.learnings.html.twig', [
            'controller_name' => 'AdminController',
            'admin' => $admin,
            'themas' => $themaRepository->findAll(),
            'cursuses' => $cursusRepository->findAll(),
            'lessons' => $lessonRepository->findAll(),
            'newThemaForm' => $newThemaForm->createView(),
            'newCursusForm' => $newCursusForm->createView(),
            'newLessonForm' => $newLessonForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/thema/delete/{id}", name="app_admin_thema_delete")
     * Delete thema
     * @param int $id Thema's id
     */
    public function themaAdminDelete($id,ThemaRepository $themaRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $thema = $themaRepository->findOneBy(['id'=>$id]);
       

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($thema);
        $entityManager->flush();

        $this->addFlash('success', "Le thème a bien été supprimé.");
       
        return $this->redirectToRoute('app_admin_learnings');
    }

    /**
     * @Route("/admin/thema/update/{id}", name="app_admin_thema_update")
     * Update thema's name
     * @param int $id Thema's id
     */
    public function themaAdminUpdate($id,ThemaRepository $themaRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $admin = $this->getUser();
        $modifiedThema = $themaRepository->findOneBy(['id'=>$id]);
       
        $modifyThemaForm = $this->createForm(ThemaRegistrationFormType::class, $modifiedThema);
        $modifyThemaForm -> handleRequest($request);
        if($modifyThemaForm->isSubmitted() && $modifyThemaForm->isValid()){
            $now = new \DateTimeImmutable();
            $modifiedThema->setUpdatedAt($now);
            $adminFirstName = $admin->getFirstName();
            $adminLastName = $admin->getLastName();
            $modifiedThema->setUpdatedBy($adminFirstName.'.'.$adminLastName); 
            $entityManager->persist($modifiedThema);
            $entityManager->flush();

            $this->addFlash('success', "Le thème a bien été modifié.");
            return $this->redirectToRoute('app_admin_learnings');
        }
       
        return $this->render('admin/admin.update.thema.html.twig', [
            'controller_name' => 'AdminController',
            'admin' => $admin,
            'modifiedThema' => $modifiedThema,
            'modifyThemaForm' => $modifyThemaForm->createView(),
            
        ]);
    }

    /**
     * @Route("/admin/cursus/delete/{id}", name="app_admin_cursus_delete")
     * Delete cursus
     * @param int $id Cursus's id
     */
    public function cursusAdminDelete($id,CursusRepository $cursusRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $cursus = $cursusRepository->findOneBy(['id'=>$id]);
       

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($cursus);
        $entityManager->flush();

        $this->addFlash('success', "Le cursus a bien été supprimé.");
       
        return $this->redirectToRoute('app_admin_learnings');
    }

    /**
     * @Route("/admin/cursus/update/{id}", name="app_admin_cursus_update")
     * Update cursus, add a lesson and display list of lessons by cursus
     * @param int $id Cursus's id
     */
    public function cursusAdminUpdate($id,CursusRepository $cursusRepository,LessonRepository $lessonRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $admin = $this->getUser();
        $modifiedCursus = $cursusRepository->findOneBy(['id'=>$id]);
        
        
        $modifyCursusForm = $this->createForm(CursusRegistrationFormType::class, $modifiedCursus);
        $modifyCursusForm -> handleRequest($request);
        if($modifyCursusForm->isSubmitted() && $modifyCursusForm->isValid()){
            $now = new \DateTimeImmutable();
            $modifiedCursus->setUpdatedAt($now);
            $adminFirstName = $admin->getFirstName();
            $adminLastName = $admin->getLastName();
            $modifiedCursus->setUpdatedBy($adminFirstName.'.'.$adminLastName); 
            $entityManager->persist($modifiedCursus);
            $entityManager->flush();

            $this->addFlash('success', "Le cursus a bien été modifié.");
            return $this->redirectToRoute('app_admin_learnings');
        }

        //////////////////////////////////
        //new lesson
        $lesson = new Lesson();
        //$cursus = $modifiedCursus->getName();
        //$lesson->setCursus($cursus);
        $newLessonForm = $this->createForm(LessonRegistrationFormType::class, $lesson);
        $newLessonForm -> handleRequest($request);
        if($newLessonForm->isSubmitted() && $newLessonForm->isValid()){
            $now = new \DateTimeImmutable();
            $lesson->setCreatedAt($now);
            $adminFirstName = $admin->getFirstName();
            $adminLastName = $admin->getLastName();
            $lesson->setCreatedBy($adminFirstName.'.'.$adminLastName);

            



            $entityManager->persist($lesson);
            $entityManager->flush();

            $this->addFlash('success', "La nouvelle leçon a bien été créée.");
            return $this->redirectToRoute('app_admin_cursus_update', ['id' => $modifiedCursus->getId(),
              ]);
        }


        
       
        return $this->render('admin/admin.update.cursus.html.twig', [
            'controller_name' => 'AdminController',
            'admin' => $admin,
            'modifiedCursus' => $modifiedCursus,
            'modifyCursusForm' => $modifyCursusForm->createView(),
            'lessons' => $lessonRepository->findBy(['cursus'=>$id]),
            'newLessonForm' => $newLessonForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/lesson/delete/{id}", name="app_admin_lesson_delete")
     * Delete lesson
     * @param int $id Lesson's id
     */
    public function lessonAdminDelete($id,LessonRepository $lessonRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $lesson = $lessonRepository->findOneBy(['id'=>$id]);
       

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($lesson);
        $entityManager->flush();

        $this->addFlash('success', "La leçon a bien été supprimée.");
       
        return $this->redirectToRoute('app_admin_learnings');
    }

    /**
     * @Route("/admin/lesson/update/{id}", name="app_admin_lesson_update")
     * Update lesson
     * @param int $id Lesson's id
     */
    public function lessonAdminUpdate($id,LessonRepository $lessonRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $admin = $this->getUser();
        $modifiedLesson = $lessonRepository->findOneBy(['id'=>$id]);
        
        
        $modifyLessonForm = $this->createForm(LessonRegistrationFormType::class, $modifiedLesson);
        $modifyLessonForm -> handleRequest($request);
        if($modifyLessonForm->isSubmitted() && $modifyLessonForm->isValid()){
            $now = new \DateTimeImmutable();
            $modifiedLesson->setUpdatedAt($now);
            $adminFirstName = $admin->getFirstName();
            $adminLastName = $admin->getLastName();
            $modifiedLesson->setUpdatedBy($adminFirstName.'.'.$adminLastName); 
            $entityManager->persist($modifiedLesson);
            $entityManager->flush();

            $this->addFlash('success', "La leçon a bien été modifiée.");
            return $this->redirectToRoute('app_admin_learnings');
        }

        
       
        return $this->render('admin/admin.update.lesson.html.twig', [
            'controller_name' => 'AdminController',
            'admin' => $admin,
            'modifiedLesson' => $modifiedLesson,
            'modifyLessonForm' => $modifyLessonForm->createView(),
          
        ]);
    }

    /**
     * @Route("/admin/user/history/{id}", name="app_admin_user_history")
     * Display orders, learnings and certifications by user
     * @param int $id User's id
     */
    public function userHistoryAdmin($id, UserRepository $userRepository, OrderRepository $orderRepository, UsercursusRepository $userCursusRepository, CursusRepository $cursusRepository,LessonRepository $lessonRepository, Request $request, EntityManagerInterface $entityManager, UserCursusLessonRepository $userCursusLessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $userRepository->findOneBy(['id'=>$id]);
        
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
        
       
        return $this->render('admin/admin.user.history.html.twig', [
            'controller_name' => 'AdminController',
            'user' => $user,
            'orders' => $orderRepository->orderbyuser($userId),
            //'userCursuses' => $userCursus,
            //'userLearningContents' =>$userLearningContent,
            'userCursusLessonContents' => $userCursusLessonContent,
            'userCertificationContents' => $userCertificationContent,
        ]);
    }

     /**
     * @Route("/admin/orders", name="app_admin_orders")
     * Display orders by month and reporting of sold learnings
     */
    public function ordersAdmin(UsercursusRepository $userCursusRepository, CursusRepository $cursusRepository,LessonRepository $lessonRepository, OrderdetailRepository $orderDetailRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $learningByMonth = $userCursusRepository->learningbymonth();
        //dd($learningByMonth);
        foreach($learningByMonth as $data){
            $year=$data['year'];
            $month=$data['month'];
            $nb=$data['nb'];
            $repository=$data['repository'];
            $userCursusId=$data['learning_id'];
            if($repository=='cursus'){
                $learning = $cursusRepository->findOneBy(['id'=>$userCursusId]);
                $learningName = "Cursus ".$learning->getLevel()." ".$learning->getName();
            } else {
                $learning = $lessonRepository->findOneBy(['id'=>$userCursusId]);
                $learningName = "Leçon ".$learning->getName();
            }
            $distributionLearnings[] = [
                "learning" => $learningName,
                "year" => $year,
                "month" => $month,
                "nb" => $nb,

            ];
            
            
        }
        
        return $this->render('admin/admin.orders.html.twig', [
            'controller_name' => 'AdminController',
            'orders' => $orderDetailRepository->orderbymonth(),
            'distributionLearnings' => $distributionLearnings,
        ]);
    }

    /**
     * @Route("/admin/user/orderdetail/{id}", name="app_admin_user_orderdetail")
     * Display lines by order
     * @param int $id Order's id
     */
    public function userOrderdetailAdmin($id, UserRepository $userRepository, OrderRepository $orderRepository, OrderdetailRepository $orderDetailRepository, CursusRepository $cursusRepository,LessonRepository $lessonRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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

            ];
            
            
        }
                
        return $this->render('admin/admin.user.orderdetail.html.twig', [
            'controller_name' => 'AdminController',
            'orderdetails' => $orderDetail,
            'order' => $order,
            'learningContents' => $learningContent,
        ]);
    }

    
}
