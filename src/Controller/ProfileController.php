<?php

namespace App\Controller;

use App\Repository\UserRepository;
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

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     */
    public function indexUser(UserRepository $userRepository): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_USER');
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

    
}
