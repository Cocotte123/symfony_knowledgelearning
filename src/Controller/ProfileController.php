<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Form\UserModifyFormType;
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
     */
    public function updateUser(UserRepository $userRepository,Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $user ->getId();
        $userModifyForm = $this->createForm(UserModifyFormType::class,$user);
        $userModifyForm->handleRequest($request);
        if($userModifyForm->isSubmitted() && $userModifyForm->isValid()){
            //if(!empty($userModifyForm->get('password'))){
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $userModifyForm->get('password')->getData()
                    )
            );
            //}
            $now = new \DateTimeImmutable();
            $user->setUpdatedAt($now);
            $userFirstName = $user->getFirstName();
            $userLastName = $user->getLastName();
            $user->setUpdatedBy($userFirstName.'.'.$userLastName);  
            dd($user);     
            
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le profil a bien été mis à jour.');
            return $this->redirectToRoute('app_profile');
        }
        
        return $this->render('profile/profileUpdateUser.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'userModifyForm' => $userModifyForm->createView(),
        ]);
    }
}
