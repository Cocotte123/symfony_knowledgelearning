<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeService {

      
    public function stripe(array $cartContent, string $cartId, $user): string
    {
            
           $stripeSecretKey = $_ENV["STRIPE_SECRET_KEY"];

            \Stripe\Stripe::setApiKey($stripeSecretKey);
            \Stripe\Stripe::setApiVersion('2024-06-20');
            header('Content-Type: application/json');

            $YOUR_DOMAIN = 'http://127.0.0.1:8000';
            $userMail = $user->getEmail();

            $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => $userMail,
            'custom_text' => [
                'submit' => [
                    'message' => "En cliquant sur payer, vous renoncez à votre droit à un délai de rétractation de 14 jours et ne pourrez demander un remboursement."
                ]
            ],
            'billing_address_collection' => "required",
            'line_items' => [
                array_map(fn(array $cartContent)=>[
                    "quantity" => $cartContent["orderedQuantity"],
                    "price_data" => [
                        "currency" => "EUR",
                        "unit_amount" => $cartContent["orderedLearning"]->getPrice()*100,
                        "product_data" => [
                            "name" => $cartContent["orderedLearning"]->getName(),
                        ]
                ]],$cartContent)
            ],
            'mode' => 'payment',
            'metadata' => [
                'cart_id' => $cartId,
            ],
            
            'success_url' => $YOUR_DOMAIN . '/cart/pay/success',
            'cancel_url' => $YOUR_DOMAIN . '/cart/pay/cancel',
            ]);

            //dd($checkout_session->url);

            return $checkout_session->url;
    }
     
}