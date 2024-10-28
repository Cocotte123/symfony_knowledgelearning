<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\SendEmailService;
use App\Service\JWTService;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SendEmailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            
            //send an email to activate user///////////////
            //token

            //header and payload
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            $payload = [
                'user_id' => $user->getId()
            ];

            //generate token with JWTService
            $token = $jwt->generateToken($header, $payload, $this->getParameter('app_jwtsecret'));

            
            //send email with SendEmailService and with a token
            $mail->sendMail(
                'no-reply@knowledge-learning.com',
                $user->getEmail(),
                'Activation de votre compte Knowledge Learning',
                'register',
                [
                    'user' => $user,
                    'token' => $token
                ]
                );

            //////////////////////////////////////////////


            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    
    /**
     * Initial link sent to user with token in order to update field isActivated
     * @Route("/activateuser/{token}", name="app_activate_user")
     * @param string $token
     */
    public function activateUser($token, JWTService $jwt, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        //token has valid format, validity and signature
        if($jwt->isFormatTokenValid($token) && !$jwt->isValidityTokenValid($token) && $jwt->isSignatureTokenValid($token, $this->getParameter('app_jwtsecret')))
        {
            //find user
            $payload = $jwt->getPayload($token);
            $user = $userRepository->find($payload['user_id']);
            if($user && !$user->isIsActivated()){
                $user->setIsActivated(true);
                $entityManager->flush();
                $this->addFlash('success', 'Félicitations! Votre compte est maintenant activé.');
                return $this->redirectToRoute('app_home');
            }

        }
        else 
        {

        //token hasn't valid format, validity and signature
        $this->addFlash('danger', 'Le lien a expiré ou est invalide.');
        return $this->redirectToRoute('app_home');
        }
       
    }

    /**
     * New link sent to user with new token in order to update field isActivated
     * @Route("/activate/resendlink", name="app_activate_resendlink")
     */
    public function activateUserResendLink(JWTService $jwt, EntityManagerInterface $entityManager, UserRepository $userRepository, SendEmailService $mail): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        //send an email to activate user///////////////
            //token

            //header and payload
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            $payload = [
                'user_id' => $user->getId()
            ];

            //generate token with JWTService
            $token = $jwt->generateToken($header, $payload, $this->getParameter('app_jwtsecret'));

            
            //send email with SendEmailService and with a token
            $mail->sendMail(
                'no-reply@knowledge-learning.com',
                $user->getEmail(),
                'Activation de votre compte Knowledge Learning',
                'register',
                [
                    'user' => $user,
                    'token' => $token
                ]
                );

        //////////////////////////////////////////////
        $this->addFlash('success', "Un nouvel email d'activation vient de vous être envoyé.");
        return $this->redirectToRoute('app_home');

    }
}
