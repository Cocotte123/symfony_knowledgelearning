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

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
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

    
}
