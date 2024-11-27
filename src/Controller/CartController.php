<?php

namespace App\Controller;
use App\Repository\CursusRepository;
use App\Repository\LessonRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderdetailRepository;
use App\Repository\UsercursusRepository;
use App\Repository\UserCursusLessonRepository;
use App\Entity\Order;
use App\Entity\Orderdetail;
use App\Entity\Usercursus;
use App\Entity\UserCursusLesson;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Controller used for buying and payment process
 */
class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart")
     * Display cart's content only if ROLE Client
     * @return array
     */
    public function index(SessionInterface $session,CursusRepository $cursusRepository,LessonRepository $lessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();

        $cart = $session->get("cart",[]);
        $cartContent = [];
        $total = 0;

        //display cart's content/////////////////////////////////////////////
        foreach($cart as $learning=> $quantity){
            
            $id = strtok($learning,".");
            $xxx=explode(".",$learning);
        //select repository between cursus or lesson
            $repository = ($xxx[1]);
            if($repository=='cursus'){
                $orderedLearning = $cursusRepository->findOneBy(['id'=>$id]); 
            } else {
                $orderedLearning = $lessonRepository->findOneBy(['id'=>$id]);
            }
            $cartContent[] = [
                "orderedLearning" => $orderedLearning,
                "orderedQuantity" => $quantity,
                "repository" => $repository,

            ];
            
            $total += $orderedLearning->getPrice() * $quantity;
        }

        
        return $this->render('cart/cart.html.twig', [
            'controller_name' => 'CartController',
            'user' => $user,
            'cartContent' => $cartContent,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/cart/add/{repository}/{id}", name="app_cart_add")
     * Add new learning: cursus or lesson to cart
     * @param int $id Cursus ou lesson's id
     * @param string $repository Cursus or Lesson repository
     */
    public function add($id,$repository, SessionInterface $session, CursusRepository $cursusRepository,LessonRepository $lessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        
        
        $cart = $session->get("cart",[]);
        $learning = $id.".".$repository;

        //add learning but only once///////////////////////////////////        
        if(isset($cart[$learning])){
            $this->addFlash('warning', "La formation a déjà été ajoutée au panier.");
        }
        else{
            $cart[$learning] =1;
            $this->addFlash('success', "La formation a bien été ajoutée au panier.");
        }
      

        $session -> set("cart", $cart);
        

        return $this->redirectToRoute("app_cart");
    }

    /**
     * @Route("/cart/remove/{repository}/{id}", name="app_cart_delete")
     * Delete learning by id and repository: cursus or lesson from cart
     * @param int $id Cursus ou lesson's id
     * @param string $repository Cursus ou lesson's repository
     */
    public function delete($id,$repository, SessionInterface $session, CursusRepository $cursusRepository,LessonRepository $lessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $cart = $session->get("cart",[]);
       
        $learning = $id.".".$repository;

        if(!empty($cart[$learning])){
                unset($cart[$learning]);
                $this->addFlash('success', "La formation a bien été retirée du panier.");
            }
        

        
        $session -> set("cart", $cart);
      

        return $this->redirectToRoute("app_cart");
    }

    /**
     * @Route("/cart/pay", name="app_cart_pay")
     * Send session cart into Stripe
     * @param array $session
     * @return string
     */
    public function pay(SessionInterface $session, StripeService $stripeService, CursusRepository $cursusRepository,LessonRepository $lessonRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $cart = $session->get("cart",[]);
        $user = $this->getUser();

        //check if cart is empty
        if($cart === []){
            $this->addFlash('warning', "Votre panier est vide.");
            return $this->redirectToRoute("app_cart");
        }

        $cartId = $session->getId();

        $cartContent = [];
        
        foreach($cart as $learning=> $quantity){
            
            $id = strtok($learning,".");
            $xxx=explode(".",$learning);
            $repository = ($xxx[1]);
            if($repository=='cursus'){
                $orderedLearning = $cursusRepository->findOneBy(['id'=>$id]); 
            } else {
                $orderedLearning = $lessonRepository->findOneBy(['id'=>$id]);
            }
            $cartContent[] = [
                "orderedLearning" => $orderedLearning,
                "orderedQuantity" => $quantity,
                "repository" => $repository,

            ];
            
        }
        

        $checkout_session = $stripeService->stripe($cartContent,$cartId, $user);
      
        //dd($checkout_session);
        //////////////////////////////////////////////////////////////////
        return $this->redirect($checkout_session);
        
       
    }

    /**
     * @Route("/cart/pay/success", name="app_pay_success")
     * Create order, order's details ans usercursuslesson with session's data and remove session after stripe's paiement
     * @param array $session
     */
    public function success(Request $request,SessionInterface $session, CursusRepository $cursusRepository, LessonRepository $lessonRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();

        //create order////////////////////////////////////////////////
        $order = new Order();
        $cart = $session->get("cart",[]);
        $cartId = $session->getId();
        $order->setUser($user);
        $order->setCartId($cartId);
                
        //create order's details
        foreach($cart as $learning=> $quantity){
            
            $id = strtok($learning,".");
            $xxx=explode(".",$learning);
            $repository = ($xxx[1]);
            if($repository=='cursus'){
                $price = $cursusRepository->findOneBy(['id'=>$id])->getPrice(); 
            } else {
                $price = $lessonRepository->findOneBy(['id'=>$id])->getPrice();
            }

            //create order's details            
            $orderDetail = new Orderdetail();
            $orderDetail->setRepository($repository);
            $orderDetail->setLearningId($id);
            $orderDetail->setPrice($price);
            $order->addOrderdetail($orderDetail);

            //create user's cursus
            //$userCursus = new Usercursus();
            //$userCursus->setUser($user);
            //$userCursus->setRepository($repository);
            //$userCursus->setLearningId($id);
            //$entityManager->persist($userCursus);

            //create user's CursusLesson with different treatment if a completed cursus is bought or just one lesson
            if($repository=='lesson'){
            $userCursusLesson = new UserCursusLesson();
            $userCursusLesson->setUser($user);
            $lesson = $lessonRepository->findOneBy(['id'=>$id]);
            $userCursusLesson->setLearning($lesson);
            $cursus = $lessonRepository->findOneBy(['id'=>$id])->getCursus();
            $userCursusLesson->setCursus($cursus);
            $entityManager->persist($userCursusLesson);
            } else {
            $lessonByCursus = $lessonRepository->findBy(['cursus'=>$id]);
            
            foreach($lessonByCursus as $data){
                $userCursusLesson = new UserCursusLesson();
                $userCursusLesson->setUser($user);

                $userCursusLesson->setLearning($data);
                $cursus = $data->getCursus();
                $userCursusLesson->setCursus($cursus);
                $entityManager->persist($userCursusLesson);
               
            }
        }
        
        }

        $entityManager->persist($order);
        $entityManager->flush();
       
         
        
        //delete session
        $session->set("cart",[]);
          
        return $this->render('cart/cart.success.html.twig', [
            'controller_name' => 'CartController',
            'user' => $user,
            'order' => $order,
        ]);
    }

    /**
     * @Route("/cart/pay/cancel", name="app_pay_cancel")
     * Page displaid in case of return from stripe
     */
    public function cancel(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $user = $this->getUser();
        
        return $this->render('cart/cart.cancel.html.twig', [
            'controller_name' => 'CartController',
            'user' => $user,
        ]);
    }

}
