<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Cursus;
use App\Repository\UserRepository;
use App\Repository\CursusRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    /**
     * Test display Home page admin
     */
    public function testRoute_app_admin(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        
        
    }

    /**
     * Test display page with check presence of a user
     */
    public function testRoute_app_admin_users(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/admin/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('th:contains("unactivated.user@test.com")');
        
        
    }

    /**
     * Test delete one user and redirect
     */
    public function testRoute_app_admin_users_delete(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('POST', '/admin/users/delete/2');

          
        $this->assertPageTitleContains("Redirecting to /admin/users");
        
    }

    /**
     * Test update one user
     */
    public function testRoute_app_admin_users_update(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $csrfTokenManager = $client->getContainer()->get('security.csrf.token_manager');
        $token = $csrfTokenManager->getToken('my_action_id')->getValue();
        //dd($token);
        $crawler = $client->request('POST', '/admin/users/update/1');

        $crawler = $client->submitForm('user_modify_admin_form[update]', [
            'user_modify_admin_form[firstName]' => 'Coco',
            'user_modify_admin_form[_token]' => $token
        ]);

       
        $this->assertResponseIsSuccessful();      
        
        
    }

    /**
     * Test display page with check presence of a cursus
     */
    public function testRoute_app_admin_learnings(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/admin/learnings');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h2:contains("Les cursus")');
        
        
    }

    /**
     * Test create thema with check redirect
     */
    public function testCreate_Thema(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/admin/learnings');

        $crawler = $client->submitForm('thema_registration_form[add]', [
            'thema_registration_form[name]' => 'Coco'
        ]);

        
        $this->assertPageTitleContains("Redirecting to /admin/learnings");
        
        
    }

    /**
     * Test update thema with check redirect
     */
    public function testUpdate_Thema(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/admin/thema/update/2');

        $crawler = $client->submitForm('thema_modify_form[update]', [
            'thema_modify_form[name]' => 'Coco1'
        ]);

        
        $this->assertPageTitleContains("Redirecting to /admin/learnings");
        
        
    }



    /**
     * Test delete thema with check redirect
     */
    public function testRoute_app_admin_thema_delete(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('POST', '/admin/thema/delete/2');

         
        $this->assertPageTitleContains("Redirecting to /admin/learnings");
        
    }

    /**
     * Test display orders by user's page with check presence order and order's element
     */
    public function testRoute_app_admin_user_history(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/admin/user/history/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h2:contains("Ses commandes")');
        $this->assertSelectorExists('th:contains("Cart")');
        $this->assertSelectorExists('th:contains("test_lesson1")');
        $this->assertSelectorExists('h5:contains("Cursus d\'initiation test_cursus")');
        
        
    }

    /**
     * Test display orders' page with check presence of tables and contents
     */
    public function testRoute_app_admin_orders(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/admin/orders');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h2:contains("Suivi des commandes")');
        $this->assertSelectorExists('h2:contains("Répartition des formations vendues sur le mois")');
        $this->assertSelectorExists('th:contains("Cursus d\'initiation test_cursus")');
        
        
    }

    /**
     * Test display details of an order with check table and contents
     */
    public function testRoute_app_admin_user_orderdetail(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'admin.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/admin/user/orderdetail/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1:contains("Détails de la commande n° 1")');
        $this->assertSelectorExists('th:contains("22.22 €")');
        
        
    }
}
