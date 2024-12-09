<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testRoute_app_cart_add(): void
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);
        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);       
        $client->loginUser($loggedUser);

        $crawler = $client->request('GET', '/cart/add/cursus/1');

        
        $crawler = $client->request('GET', '/cart');

        $this->assertSelectorExists('p:contains("test_cursus")');
    }

    public function testRoute_app_cart_delete(): void
    {
        $client = static::createClient();

        //$entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        //$repository = $entityManager->getRepository(User::class);
        //$loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);       
        //$client->loginUser($loggedUser);

        //$crawler = $client->request('GET', '/cart/add/cursus/1');
        
        $crawler = $client->request('GET', '/cart/remove/cursus/1');

        $this->assertSelectorNotExists('p:contains("test_cursus")');
    }

    public function testRoute_app_cart_pay(): void
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);
        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);       
        $client->loginUser($loggedUser);

        $session = new Session(new MockFileSessionStorage());
        $cart = $session->get("cart",[]);
        $learning = "1"."."."cursus";
        $cart[$learning] =1;
        $session -> set("cart", $cart);

        //dd($session);
        
        $crawler = $client->request('GET', '/cart/pay');

        
        $this->assertPageTitleContains("Redirecting to /cart");
    }


    public function testRoute_app_pay_success(): void
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);
        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);       
        $client->loginUser($loggedUser);

        $session = new Session(new MockFileSessionStorage());
        $cart = $session->get("cart",[]);
        $learning = "1"."."."cursus";
        $cart[$learning] =1;
        $session -> set("cart", $cart);
        
        $crawler = $client->request('GET', '/cart/pay/success');

        $this->assertSelectorExists('h1:contains("Achat réalisé avec succès!")');
    }

    public function testRoute_app_pay_cancel(): void
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);
        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);       
        $client->loginUser($loggedUser);

         
        $crawler = $client->request('GET', '/cart/pay/cancel');

        $this->assertSelectorExists('h1:contains("Votre paiement est annulé")');
    }
}
