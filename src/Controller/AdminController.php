<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use App\Form\UserModifyAdminFormType;
use App\Form\MailUserModifyFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

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
        ///filter by name
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
}
